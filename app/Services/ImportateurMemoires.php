<?php

namespace App\Services;

use App\Models\AnneeAcademique;
use App\Models\Filiere;
use App\Models\Memoire;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Importe un manifeste de mémoires (CSV) et, si demandé, copie les PDF
 * dans le stockage privé et génère les pages de garde.
 *
 * Le manifeste réel est ignoré par Git : aucune donnée personnelle ni PDF
 * ne doit être versionné.
 */
class ImportateurMemoires
{
    public function __construct(private readonly CouvertureService $couvertures) {}

    /**
     * @return array<int, array<string, string>>
     */
    public function lignes(string $manifeste): array
    {
        if (! is_file($manifeste)) {
            return [];
        }

        $contenu = file_get_contents($manifeste);

        if ($contenu === false) {
            return [];
        }

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
     * @return array{crees: int, mis_a_jour: int, fichiers: int, couvertures: int, erreurs: array<int, string>}
     */
    public function importer(
        string $manifeste,
        ?string $dossierSource = null,
        bool $simulation = false,
        bool $copierFichiers = true,
        bool $genererCouvertures = true,
    ): array {
        $resultat = [
            'crees' => 0,
            'mis_a_jour' => 0,
            'fichiers' => 0,
            'couvertures' => 0,
            'erreurs' => [],
        ];

        $lignes = $this->lignes($manifeste);

        if ($lignes === []) {
            $resultat['erreurs'][] = 'Le manifeste est vide ou introuvable : '.$manifeste;

            return $resultat;
        }

        $fichiersSource = $dossierSource !== null && is_dir($dossierSource)
            ? $this->indexerFichiers($dossierSource)
            : [];

        foreach ($lignes as $index => $ligne) {
            $titre = trim($ligne['titre'] ?? '');

            if ($titre === '') {
                $resultat['erreurs'][] = 'Ligne '.($index + 2).': titre manquant.';

                continue;
            }

            $filiereCode = mb_strtoupper(trim($ligne['filiere'] ?? ''));
            $anneeLibelle = trim($ligne['annee'] ?? '');

            $filiere = Filiere::query()
                ->where('code', $filiereCode)
                ->orWhere('nom', $ligne['filiere'] ?? '')
                ->first();

            $annee = AnneeAcademique::where('libelle', $anneeLibelle)->first();

            if ($filiere === null || $annee === null) {
                $resultat['erreurs'][] = "Ligne ".($index + 2).": filière ou année inconnue ({$filiereCode} / {$anneeLibelle}).";

                continue;
            }

            $nomFichier = trim($ligne['fichier'] ?? '');
            $pdfSource = null;

            if ($nomFichier !== '' && $dossierSource !== null) {
                $pdfSource = $this->trouverFichier($dossierSource, $nomFichier, $fichiersSource);
            }

            $dateSoutenance = $this->date($ligne['date_soutenance'] ?? null);
            $confidentialite = in_array($ligne['confidentialite'] ?? '', ['public', 'interne', 'restreint'], true)
                ? $ligne['confidentialite']
                : 'public';
            $telechargement = ! in_array(mb_strtolower($ligne['telechargement_autorise'] ?? '1'), ['0', 'non', 'false'], true);
            $matricule = trim($ligne['matricule'] ?? '');
            $etudiant = $matricule !== '' ? User::where('matricule', $matricule)->first() : null;

            $donnees = [
                'titre' => $titre,
                'resume' => ($ligne['resume'] ?? '') !== '' ? $ligne['resume'] : null,
                'mots_cles' => ($ligne['mots_cles'] ?? '') !== '' ? $ligne['mots_cles'] : null,
                'statut' => 'valide',
                'confidentialite' => $confidentialite,
                'telechargement_autorise' => $telechargement,
                'date_embargo' => $this->date($ligne['date_embargo'] ?? null),
                'etudiant_id' => $etudiant?->id,
                'auteur_nom' => ($ligne['auteur'] ?? '') !== '' ? $ligne['auteur'] : null,
                'type_diplome' => ($ligne['type_diplome'] ?? '') !== '' ? $ligne['type_diplome'] : 'Licence',
                'date_soutenance' => $dateSoutenance,
                'cote' => $this->cote($filiere->code, $annee->libelle, $index + 1),
                'filiere_id' => $filiere->id,
                'annee_id' => $annee->id,
                'admin_depose' => true,
                'date_depot' => $dateSoutenance?->copy()->setTime(10, 0) ?? now(),
            ];

            $memoire = Memoire::where('titre', $titre)->first();
            $creation = $memoire === null;

            if ($simulation) {
                $creation ? $resultat['crees']++ : $resultat['mis_a_jour']++;

                continue;
            }

            if ($memoire === null) {
                $memoire = new Memoire;
                $resultat['crees']++;
            } else {
                $resultat['mis_a_jour']++;
            }

            $memoire->fill($donnees);
            $memoire->fichier_hash ??= hash('sha256', 'manifeste:'.$titre);
            $memoire->fichier_pdf = 'memoires/'.$memoire->id.'/'.$this->slug($titre).'.pdf';
            $memoire->save();

            // Le chemin dépend de l'identifiant : on le fixe après la création.
            $memoire->fichier_pdf = 'memoires/'.$memoire->id.'/'.$this->slug($titre).'.pdf';
            $memoire->save();

            if ($copierFichiers && $pdfSource !== null) {
                $destination = $memoire->fichier_pdf;

                if (! Storage::disk('local')->exists($destination)) {
                    $flux = fopen($pdfSource, 'rb');

                    if ($flux !== false) {
                        Storage::disk('local')->put($destination, $flux);
                        fclose($flux);
                        $resultat['fichiers']++;
                    }
                }

                if (Storage::disk('local')->exists($destination)) {
                    $memoire->forceFill([
                        'fichier_pdf' => $destination,
                        'fichier_hash' => hash_file('sha256', $pdfSource),
                    ])->save();
                }
            }

            if ($genererCouvertures) {
                $page = $this->pageCouverture($ligne['couverture_page'] ?? '');

                $this->couvertures->generer(
                    $memoire,
                    $page === null ? null : ($pdfSource ?? Storage::disk('local')->path($memoire->fichier_pdf)),
                    $page ?? 0
                );
                $resultat['couvertures']++;
            }
        }

        return $resultat;
    }

    /**
     * @return array<string, string>
     */
    private function indexerFichiers(string $dossier): array
    {
        $index = [];

        foreach (scandir($dossier) ?: [] as $fichier) {
            if ($fichier === '.' || $fichier === '..') {
                continue;
            }

            $chemin = rtrim($dossier, '\\/').DIRECTORY_SEPARATOR.$fichier;

            if (is_file($chemin)) {
                $index[mb_strtolower($fichier)] = $chemin;
                $index[$this->cleFichier($fichier)] = $chemin;
            }
        }

        return $index;
    }

    /**
     * @param  array<string, string>  $index
     */
    private function trouverFichier(string $dossier, string $nom, array $index): ?string
    {
        $exact = rtrim($dossier, '\\/').DIRECTORY_SEPARATOR.$nom;

        if (is_file($exact)) {
            return $exact;
        }

        return $index[mb_strtolower($nom)] ?? $index[$this->cleFichier($nom)] ?? null;
    }

    private function cleFichier(string $nom): string
    {
        return Str::ascii(mb_strtolower($nom));
    }

    private function date(?string $valeur): ?\Illuminate\Support\Carbon
    {
        $valeur = trim((string) $valeur);

        if ($valeur === '') {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($valeur);
        } catch (\Throwable) {
            return null;
        }
    }

    private function pageCouverture(string $valeur): ?int
    {
        $valeur = mb_strtolower(trim($valeur));

        if ($valeur === 'placeholder' || $valeur === 'aucune') {
            return null;
        }

        if ($valeur === '') {
            return 0;
        }

        return max(0, (int) $valeur);
    }

    private function cote(string $filiere, string $annee, int $index): string
    {
        $debut = Str::before($annee, '-');

        return sprintf('%s-%s-%03d', $filiere, $debut !== '' ? $debut : date('Y'), $index);
    }

    private function slug(string $titre): string
    {
        return Str::slug(Str::limit($titre, 80, '')) ?: 'memoire';
    }
}
