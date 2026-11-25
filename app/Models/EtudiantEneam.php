<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Annuaire des étudiants autorisés à activer un compte.
 *
 * Aucun mot de passe n'est stocké ici : uniquement les données
 * strictement nécessaires à la vérification d'identité.
 */
class EtudiantEneam extends Model
{
    protected $table = 'etudiants_eneam';

    protected $fillable = [
        'matricule',
        'nom',
        'email',
        'filiere',
        'promotion',
        'actif',
        'source',
        'synced_at',
    ];

    protected $casts = [
        'actif' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'matricule', 'matricule');
    }

    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('actif', true);
    }
}
