<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Memoire extends Model
{
    protected $fillable = [
        'titre',
        'resume',
        'mots_cles',
        'fichier_pdf',
        'fichier_hash',
        'statut',
        'confidentialite',
        'telechargement_autorise',
        'date_embargo',
        'etudiant_id',
        'auteur_nom',
        'type_diplome',
        'date_soutenance',
        'cote',
        'filiere_id',
        'annee_id',
        'admin_depose',
        'vues',
        'date_depot',
    ];

    protected $casts = [
        'date_embargo' => 'date',
        'date_soutenance' => 'date',
        'date_depot' => 'datetime',
        'admin_depose' => 'boolean',
        'telechargement_autorise' => 'boolean',
        'vues' => 'integer',
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function annee(): BelongsTo
    {
        return $this->belongsTo(AnneeAcademique::class, 'annee_id');
    }

    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(VersionMemoire::class);
    }

    public function favorisPar(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favoris')->withTimestamps();
    }

    public function historiquesLecture(): HasMany
    {
        return $this->hasMany(HistoriqueLecture::class);
    }
}
