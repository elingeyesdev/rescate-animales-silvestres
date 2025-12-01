<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    protected $fillable = [
        'user_id',
        'motivo',
        'mensaje',
        'leido',
        'leido_at',
        'leido_por',
    ];

    protected $casts = [
        'leido' => 'boolean',
        'leido_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leido_por');
    }

    public static function getMotivos(): array
    {
        return [
            'tardanza_respuesta' => 'Tardanza en responder a mi solicitud',
            'problema_cuenta' => 'Problema con mi cuenta',
            'contacto_directo' => 'Quiero comunicarme directamente',
        ];
    }
}
