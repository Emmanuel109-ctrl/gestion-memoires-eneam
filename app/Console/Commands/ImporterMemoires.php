<?php

namespace App\Console\Commands;

use App\Services\ImportateurMemoires;
use Illuminate\Console\Command;

class ImporterMemoires extends Command
{
    protected $signature = 'eneam:importer-memoires
                            {--manifeste= : Chemin du manifeste CSV}
                            {--source= : Dossier contenant les PDF}
                            {--dry-run : Simuler sans écrire}
                            {--sans-fichiers : Ne pas copier les PDF}
                            {--sans-couvertures : Ne pas générer les pages de garde}';

    protected $description = 'Importe les mémoires depuis un manifeste CSV, copie les PDF en privé et génère les pages de garde';

    public function handle(ImportateurMemoires $importateur): int
    {
        $manifeste = $this->option('manifeste') ?: database_path('data/memoires.csv');
        $source = $this->option('source') ?: env('MEMOIRES_SOURCE');
        $simulation = (bool) $this->option('dry-run');

        if (! is_file($manifeste)) {
            $this->components->error("Manifeste introuvable : {$manifeste}");

            return self::FAILURE;
        }

        if ($source !== null && ! is_dir($source)) {
            $this->components->error("Dossier source introuvable : {$source}");

            return self::FAILURE;
        }

        if ($source === null && ! $this->option('sans-fichiers')) {
            $this->components->warn('Aucun dossier source fourni : les notices seront créées sans copier les PDF.');
        }

        $resultat = $importateur->importer(
            $manifeste,
            $source,
            $simulation,
            ! $this->option('sans-fichiers') && $source !== null,
            ! $this->option('sans-couvertures'),
        );

        $this->newLine();
        $this->components->info(sprintf(
            '%s : %d création(s), %d mise(s) à jour, %d fichier(s) copié(s), %d couverture(s).',
            $simulation ? 'Simulation' : 'Import terminé',
            $resultat['crees'],
            $resultat['mis_a_jour'],
            $resultat['fichiers'],
            $resultat['couvertures'],
        ));

        foreach ($resultat['erreurs'] as $erreur) {
            $this->components->warn($erreur);
        }

        return $resultat['erreurs'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
