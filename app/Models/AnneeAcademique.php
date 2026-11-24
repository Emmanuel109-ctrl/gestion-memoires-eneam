<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeAcademique extends Model
{
    protected $table = 'annees_academiques';

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'active',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'active' => 'boolean',
    ];

    public function memoires(): HasMany
    {
        return $this->hasMany(Memoire::class);
    }
}
