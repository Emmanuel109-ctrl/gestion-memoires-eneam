<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'couverture',
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

    /**
     * Le mémoire est-il publié au catalogue ?
     */
    public function estValide(): bool
    {
        return $this->statut === 'valide';
    }

    public function estEnEmbargo(): bool
    {
        return $this->date_embargo !== null
            && $this->date_embargo->copy()->endOfDay()->isFuture();
    }

    public function estAuteur(?User $utilisateur): bool
    {
        return $utilisateur !== null
            && $this->etudiant_id !== null
            && (int) $this->etudiant_id === (int) $utilisateur->id;
    }

    public function estPersonnel(?User $utilisateur): bool
    {
        return $utilisateur !== null
            && ($utilisateur->isAdmin() || $utilisateur->isBibliothecaire());
    }

    /**
     * Un mémoire validé et public est visible même sans connexion.
     */
    public function estPublic(): bool
    {
        return $this->estValide() && $this->confidentialite === 'public';
    }

    /**
     * Accès au texte intégral.
     */
    public function estConsultablePar(?User $utilisateur): bool
    {
        if ($this->estPersonnel($utilisateur) || $this->estAuteur($utilisateur)) {
            return true;
        }

        if ($utilisateur === null || ! $this->estValide()) {
            return false;
        }

        if ($this->estEnEmbargo()) {
            return false;
        }

        return in_array($this->confidentialite, ['public', 'interne'], true);
    }

    public function estTelechargeablePar(?User $utilisateur): bool
    {
        if ($this->estPersonnel($utilisateur) || $this->estAuteur($utilisateur)) {
            return true;
        }

        return $this->estConsultablePar($utilisateur)
            && $this->telechargement_autorise;
    }

    /**
     * Notices visibles dans le catalogue selon le profil.
     *
     * - visiteur : mémoires validés et publics ;
     * - étudiant : validés publics ou internes, plus ses propres dépôts ;
     * - personnel : tout le fonds.
     */
    public function scopeVisiblesPour(Builder $query, ?User $utilisateur): Builder
    {
        if ($this->estPersonnel($utilisateur)) {
            return $query;
        }

        if ($utilisateur === null) {
            return $query->where('statut', 'valide')
                ->where('confidentialite', 'public');
        }

        return $query->where(function (Builder $q) use ($utilisateur): void {
            $q->where(function (Builder $publics): void {
                $publics->where('statut', 'valide')
                    ->whereIn('confidentialite', ['public', 'interne']);
            })->orWhere('etudiant_id', $utilisateur->id);
        });
    }

    public function libelleConfidentialite(): string
    {
        return match ($this->confidentialite) {
            'public' => 'Accès ENEAM',
            'interne' => 'Interne ENEAM',
            'restreint' => 'Accès restreint',
            default => 'Non précisé',
        };
    }

    public function libelleStatut(): string
    {
        return match ($this->statut) {
            'soumis' => 'Soumis',
            'en_verification' => 'En vérification',
            'a_corriger' => 'À corriger',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
            default => 'Inconnu',
        };
    }
}
