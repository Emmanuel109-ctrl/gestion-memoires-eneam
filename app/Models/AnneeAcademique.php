<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeAcademique extends Model
{
    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'active',
    ];

    public function memoires(): HasMany
    {
        return $this->hasMany(Memoire::class);
    }
}
