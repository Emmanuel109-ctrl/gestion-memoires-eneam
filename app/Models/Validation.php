<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    protected $fillable = [
        'memoire_id',
        'validateur_id',
        'statut',
        'motif',
        'conforme_version_papier',
        'format_conforme',
        'date_decision',
    ];

    protected $casts = [
        'conforme_version_papier' => 'boolean',
        'format_conforme' => 'boolean',
        'date_decision' => 'datetime',
    ];

    public function memoire(): BelongsTo
    {
        return $this->belongsTo(Memoire::class);
    }

    public function validateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }
}
