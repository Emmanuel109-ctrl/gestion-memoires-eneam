<?php

namespace App\Notifications;

use App\Models\Memoire;
use Illuminate\Notifications\Notification;

class NouveauDepotNotification extends Notification
{
    public function __construct(
        public Memoire $memoire,
    ) {
    }

    /**
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
        return [
            'memoire_id' => $this->memoire->id,
            'titre' => $this->memoire->titre,
            'statut' => $this->memoire->statut,
            'libelle' => 'Nouveau dépôt à vérifier',
            'motif' => null,
            'url' => route('validations.show', $this->memoire),
        ];
    }
}
