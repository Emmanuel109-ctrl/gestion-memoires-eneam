<?php

namespace App\Notifications;

use App\Models\Memoire;
use Illuminate\Notifications\Notification;

class DecisionMemoireNotification extends Notification
{
    public function __construct(
        public Memoire $memoire,
        public string $statut,
        public ?string $motif = null,
    ) {
    }

    /**
     * Notification interne : elle reste consultable dans l'application,
     * sans dépendre d'une configuration SMTP encore absente.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $libelle = match ($this->statut) {
            'valide' => 'Mémoire validé',
            'a_corriger' => 'Correction demandée',
            'rejete' => 'Mémoire rejeté',
            default => 'Décision enregistrée',
        };

        return [
            'memoire_id' => $this->memoire->id,
            'titre' => $this->memoire->titre,
            'statut' => $this->statut,
            'libelle' => $libelle,
            'motif' => $this->motif,
            'url' => route('memoires.miennes'),
        ];
    }
}
