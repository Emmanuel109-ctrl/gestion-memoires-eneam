<?php

namespace App\Services;

use App\Models\Memoire;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Génère et sert la page de garde d'un mémoire.
 *
 * Le PDF complet reste privé ; seule cette image est publique.
 * Plusieurs moteurs sont essayés dans l'ordre, avec un repli SVG
 * aux couleurs de l'ENEAM si aucun outil de rendu n'est disponible.
 */
class CouvertureService
{
    public function generer(Memoire $memoire, ?string $pdfAbsolu = null, int $page = 0): string
    {
        $disque = Storage::disk('local');
        $disque->makeDirectory('couvertures');

        $this->supprimerAncienne($memoire);

        if ($pdfAbsolu !== null && is_file($pdfAbsolu)) {
            // WinRT (rendu Windows) refuse les chemins à séparateurs mixtes.
            $pdfAbsolu = str_replace('/', DIRECTORY_SEPARATOR, $pdfAbsolu);
            $png = str_replace('/', DIRECTORY_SEPARATOR, storage_path('app/private/couvertures/'.$memoire->id.'.png'));

            if ($this->rendreAvecPdftoppm($pdfAbsolu, $png, $page)
                || $this->rendreAvecGhostscript($pdfAbsolu, $png, $page)
                || $this->rendreAvecWindows($pdfAbsolu, $png, $page)) {
                $memoire->forceFill(['couverture' => 'couvertures/'.$memoire->id.'.png'])->save();

                return $memoire->couverture;
            }
        }

        $svg = 'couvertures/'.$memoire->id.'.svg';
        $disque->put($svg, $this->placeholder($memoire));
        $memoire->forceFill(['couverture' => $svg])->save();

        return $svg;
    }

    /**
     * Réponse HTTP de la page de garde, réelle ou générée à la volée.
     */
    public function reponse(Memoire $memoire): \Symfony\Component\HttpFoundation\Response
    {
        // Couverture manquante (dépôt récent, import partiel) : on la génère
        // à la première demande plutôt que d'afficher un aperçu générique.
        if ($memoire->couverture === null) {
            $pdf = str_replace('/', DIRECTORY_SEPARATOR, Storage::disk('local')->path($memoire->fichier_pdf));

            if (is_file($pdf)) {
                try {
                    $this->generer($memoire, $pdf);
                    $memoire->refresh();
                } catch (\Throwable) {
                    //
                }
            }
        }

        if ($memoire->couverture !== null) {
            $disque = Storage::disk('local');

            if ($disque->exists($memoire->couverture)) {
                $mime = Str::endsWith($memoire->couverture, '.svg')
                    ? 'image/svg+xml'
                    : 'image/png';

                return response($disque->get($memoire->couverture), 200, [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        return response($this->placeholder($memoire), 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * SVG de repli, aux couleurs de l'ENEAM.
     */
    public function placeholder(Memoire $memoire): string
    {
        $titre = $this->xml($this->decouper($memoire->titre, 34, 4));
        $titreLignes = '';

        foreach (explode("\n", $titre) as $index => $ligne) {
            $dy = $index === 0 ? 0 : 48;
            $titreLignes .= sprintf(
                '<tspan x="64" dy="%d">%s</tspan>',
                $dy,
                $ligne
            );
        }

        $auteur = $this->xml($memoire->auteur_nom ?? 'Auteur non renseigné');
        $filiere = $this->xml($memoire->filiere?->nom ?? 'Filière non renseignée');
        $annee = $this->xml(
            $memoire->annee?->libelle
            ?? ($memoire->date_soutenance?->format('Y') ?? date('Y'))
        );

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="1130" viewBox="0 0 800 1130" role="img" aria-label="Page de garde du mémoire">
  <defs>
    <linearGradient id="fond" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#172554"/>
      <stop offset="55%" stop-color="#1e40af"/>
      <stop offset="100%" stop-color="#2563eb"/>
    </linearGradient>
  </defs>
  <rect width="800" height="1130" fill="#f8fafc"/>
  <rect width="800" height="1130" fill="url(#fond)" opacity="0.97"/>
  <circle cx="700" cy="120" r="220" fill="#ffffff" opacity="0.05"/>
  <circle cx="80" cy="1040" r="260" fill="#93c5fd" opacity="0.12"/>

  <g font-family="Montserrat, Arial, sans-serif" fill="#ffffff">
    <text x="64" y="110" font-size="17" font-weight="700" letter-spacing="5">ENEAM</text>
    <text x="64" y="140" font-size="13" letter-spacing="1.5" opacity="0.8">BIBLIOTHÈQUE NUMÉRIQUE DES MÉMOIRES</text>

    <text x="64" y="208" font-size="12" font-weight="600" letter-spacing="2.5" opacity="0.7">MÉMOIRE DE SOUTENANCE</text>

    <text x="64" y="300" font-size="36" font-weight="700">
      {$titreLignes}
    </text>

    <line x1="64" y1="760" x2="736" y2="760" stroke="#ffffff" stroke-opacity="0.25"/>

    <text x="64" y="805" font-size="12" font-weight="600" letter-spacing="2" opacity="0.7">AUTEUR</text>
    <text x="64" y="834" font-size="18" font-weight="600">{$auteur}</text>

    <text x="64" y="892" font-size="12" font-weight="600" letter-spacing="2" opacity="0.7">FILIÈRE</text>
    <text x="64" y="921" font-size="18" font-weight="600">{$filiere}</text>

    <text x="64" y="1010" font-size="12" font-weight="600" letter-spacing="2" opacity="0.7">PROMOTION</text>
    <text x="64" y="1039" font-size="18" font-weight="600">{$annee}</text>

    <text x="736" y="1070" text-anchor="end" font-size="11" opacity="0.65">École Nationale d'Économie Appliquée et de Management</text>
  </g>
</svg>
SVG;
    }

    private function supprimerAncienne(Memoire $memoire): void
    {
        if ($memoire->couverture !== null) {
            Storage::disk('local')->delete($memoire->couverture);
        }
    }

    private function rendreAvecPdftoppm(string $pdf, string $sortie, int $page): bool
    {
        if (! $this->commandeDisponible('pdftoppm')) {
            return false;
        }

        $prefixe = preg_replace('/\.png$/i', '', $sortie);

        $resultat = Process::timeout(120)->run([
            'pdftoppm',
            '-f', (string) ($page + 1),
            '-l', (string) ($page + 1),
            '-singlefile',
            '-r', '120',
            '-png',
            $pdf,
            $prefixe,
        ]);

        return $resultat->successful() && is_file($sortie);
    }

    private function rendreAvecGhostscript(string $pdf, string $sortie, int $page): bool
    {
        $commande = $this->commandeDisponible('gswin64c')
            ? 'gswin64c'
            : ($this->commandeDisponible('gs') ? 'gs' : null);

        if ($commande === null) {
            return false;
        }

        $resultat = Process::timeout(120)->run([
            $commande,
            '-dSAFER', '-dBATCH', '-dNOPAUSE',
            '-sDEVICE=png16m',
            '-r120',
            '-dFirstPage='.($page + 1),
            '-dLastPage='.($page + 1),
            '-sOutputFile='.$sortie,
            $pdf,
        ]);

        return $resultat->successful() && is_file($sortie);
    }

    private function rendreAvecWindows(string $pdf, string $sortie, int $page): bool
    {
        if (PHP_OS_FAMILY !== 'Windows' || ! $this->commandeDisponible('powershell')) {
            return false;
        }

        $script = base_path('scripts/render-pdf-page.ps1');

        if (! is_file($script)) {
            return false;
        }

        $resultat = Process::timeout(120)->run([
            'powershell',
            '-NoProfile',
            '-ExecutionPolicy', 'Bypass',
            '-File', $script,
            '-Path', $pdf,
            '-Out', $sortie,
            '-PageIndex', (string) $page,
        ]);

        return $resultat->successful() && is_file($sortie);
    }

    private function commandeDisponible(string $commande): bool
    {
        $test = PHP_OS_FAMILY === 'Windows'
            ? ['where', $commande]
            : ['which', $commande];

        try {
            return Process::timeout(5)->run($test)->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    private function xml(string $texte): string
    {
        return htmlspecialchars($texte, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Découpe un titre en lignes courtes pour le SVG.
     */
    private function decouper(string $texte, int $longueur, int $maxLignes): string
    {
        $mots = preg_split('/\s+/u', trim($texte)) ?: [];
        $lignes = [];
        $courante = '';

        foreach ($mots as $mot) {
            $essai = $courante === '' ? $mot : $courante.' '.$mot;

            if (mb_strlen($essai) > $longueur && $courante !== '') {
                $lignes[] = $courante;
                $courante = $mot;
            } else {
                $courante = $essai;
            }
        }

        if ($courante !== '') {
            $lignes[] = $courante;
        }

        if (count($lignes) > $maxLignes) {
            $lignes = array_slice($lignes, 0, $maxLignes);
            $lignes[$maxLignes - 1] .= '…';
        }

        return implode("\n", $lignes);
    }
}
