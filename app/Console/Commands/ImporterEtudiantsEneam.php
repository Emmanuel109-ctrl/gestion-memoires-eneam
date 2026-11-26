<?php

namespace App\Console\Commands;

use App\Models\EtudiantEneam;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Importe l'annuaire des étudiants ENEAM depuis un fichier CSV.
 *
 * Le fichier peut être séparé par des points-virgules ou des virgules.
 * Colonnes attendues : matricule, nom, email, filiere, promotion, actif.
 */
class ImporterEtudiantsEneam extends Command
{
    protected $signature = 'eneam:importer-etudiants
                            {fichier? : Chemin du fichier CSV (par défaut le CSV d’annuaire présent dans database/data)}
                            {--source= : Libellé de la source (ex. scolarite-2026)}
                            {--dry-run : Simuler sans écrire en base}';

    protected $description = "Importe ou met à jour l'annuaire des étudiants ENEAM depuis un CSV";

    public function handle(): int
    {
        $fichier = $this->argument('fichier') ?? $this->fichierParDefaut();

        if (! is_file($fichier)) {
            $this->components->error("Fichier introuvable : {$fichier}");

            return self::FAILURE;
        }

        $lignes = $this->lireCsv($fichier);

        if ($lignes === []) {
            $this->components->warn('Le fichier ne contient aucune ligne de données.');

            return self::SUCCESS;
        }

        $source = $this->option('source') ?: basename($fichier);
        $simulation = (bool) $this->option('dry-run');

        $crees = 0;
        $misAJour = 0;
        $erreurs = [];
        $matriculesVus = [];
        $emailsVus = [];

        DB::beginTransaction();

        try {
            foreach ($lignes as $numero => $ligne) {
                [$donnees, $erreur] = $this->validerLigne($ligne, $numero + 2);

                if ($erreur !== null) {
                    $erreurs[] = $erreur;

                    continue;
                }

                if (isset($matriculesVus[$donnees['matricule']])) {
                    $erreurs[] = "Ligne {$numero}: matricule {$donnees['matricule']} présent plusieurs fois.";

                    continue;
                }

                if (isset($emailsVus[mb_strtolower($donnees['email'])])) {
                    $erreurs[] = "Ligne {$numero}: e-mail {$donnees['email']} présent plusieurs fois.";

                    continue;
                }

                $matriculesVus[$donnees['matricule']] = true;
                $emailsVus[mb_strtolower($donnees['email'])] = true;

                $existant = EtudiantEneam::where('matricule', $donnees['matricule'])->first();

                if ($existant === null) {
                    EtudiantEneam::create([
                        ...$donnees,
                        'source' => $source,
                        'synced_at' => now(),
                    ]);
                    $crees++;
                } else {
                    $existant->fill([
                        ...$donnees,
                        'source' => $source,
                        'synced_at' => now(),
                    ])->save();
                    $misAJour++;
                }
            }

            if ($simulation) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $exception) {
            DB::rollBack();

            $this->components->error('Import interrompu : '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->components->info(sprintf(
            '%s : %d création(s), %d mise(s) à jour, %d erreur(s).',
            $simulation ? 'Simulation' : 'Import terminé',
            $crees,
            $misAJour,
            count($erreurs)
        ));

        foreach ($erreurs as $erreur) {
            $this->components->warn($erreur);
        }

        if ($simulation) {
            $this->components->warn('Aucune donnée n’a été écrite (--dry-run).');
        }

        return $erreurs === [] ? self::SUCCESS : self::FAILURE;
    }

    private function fichierParDefaut(): string
    {
        $fichiers = [
            database_path('data/etudiants_eneams.csv'),
            database_path('data/etudiants_eneam.csv'),
        ];

        foreach ($fichiers as $fichier) {
            if (is_file($fichier)) {
                return $fichier;
            }
        }

        return $fichiers[1];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function lireCsv(string $fichier): array
    {
        $contenu = file_get_contents($fichier);

        if ($contenu === false) {
            return [];
        }

        // Retire le BOM UTF-8 produit par Excel.
        $contenu = preg_replace('/^\xEF\xBB\xBF/', '', $contenu) ?? $contenu;
        $premiereLigne = strtok($contenu, "\r\n") ?: '';
        $separateur = substr_count($premiereLigne, ';') >= substr_count($premiereLigne, ',') ? ';' : ',';

        $flux = fopen('php://temp', 'r+');
        fwrite($flux, $contenu);
        rewind($flux);

        $entetes = fgetcsv($flux, null, $separateur);
        $lignes = [];

        if ($entetes === false) {
            fclose($flux);

            return [];
        }

        $entetes = array_map(fn ($entete) => mb_strtolower(trim((string) $entete)), $entetes);

        while (($valeurs = fgetcsv($flux, null, $separateur)) !== false) {
            if ($valeurs === [null] || trim(implode('', $valeurs)) === '') {
                continue;
            }

            $ligne = [];

            foreach ($entetes as $index => $entete) {
                $ligne[$entete] = trim((string) ($valeurs[$index] ?? ''));
            }

            $lignes[] = $ligne;
        }

        fclose($flux);

        return $lignes;
    }

    /**
     * @param  array<string, string>  $ligne
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    private function validerLigne(array $ligne, int $numero): array
    {
        $matricule = preg_replace('/\s+/', '', $ligne['matricule'] ?? '') ?? '';
        $nom = trim($ligne['nom'] ?? '');
        $email = mb_strtolower(trim($ligne['email'] ?? ''));
        $filiere = trim($ligne['filiere'] ?? '');
        $promotion = trim($ligne['promotion'] ?? '');
        $actifBrut = mb_strtolower(trim($ligne['actif'] ?? '1'));

        if (! preg_match('/^\d{8}$/', $matricule)) {
            return [[], "Ligne {$numero}: matricule « {$matricule} » invalide (8 chiffres attendus)."];
        }

        if ($nom === '') {
            return [[], "Ligne {$numero}: nom manquant."];
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [[], "Ligne {$numero}: e-mail « {$email} » invalide."];
        }

        $actif = ! in_array($actifBrut, ['0', 'non', 'false', 'inactif'], true);

        return [[
            'matricule' => $matricule,
            'nom' => $nom,
            'email' => $email,
            'filiere' => $filiere !== '' ? $filiere : null,
            'promotion' => $promotion !== '' ? $promotion : null,
            'actif' => $actif,
        ], null];
    }
}
