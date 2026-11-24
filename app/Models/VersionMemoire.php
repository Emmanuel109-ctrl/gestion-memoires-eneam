<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VersionMemoire extends Model
{
    protected $table = 'versions_memoires';

    protected $fillable = [
        'memoire_id',
        'fichier_pdf',
        'numero_version',
    ];

    protected $casts = [
        'numero_version' => 'integer',
    ];

    public function memoire(): BelongsTo
    {
        return $this->belongsTo(Memoire::class);
    }
}
