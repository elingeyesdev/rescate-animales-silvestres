<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo para tracking/seguimiento de actividades de usuarios
 * Similar a AnimalHistory pero para usuarios
 * 
 * @property int $id
 * @property int|null $user_id Usuario sobre el que se registra la acción
 * @property int|null $performed_by Usuario que realizó la acción
 * @property string $action_type Tipo de acción
 * @property string $action_description Descripción de la acción
 * @property string|null $related_model_type Tipo de modelo relacionado
 * @property int|null $related_model_id ID del modelo relacionado
 * @property array|null $old_values Valores anteriores
 * @property array|null $new_values Valores nuevos
 * @property array|null $metadata Información adicional
 * @property \Carbon\Carbon $performed_at Fecha/hora de la acción
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class UserTracking extends Model
{
    protected $table = 'user_tracking';

    protected $fillable = [
        'user_id',
        'performed_by',
        'action_type',
        'action_description',
        'related_model_type',
        'related_model_id',
        'valores_antiguos',
        'valores_nuevos',
        'metadata',
        'realizado_en',
    ];

    protected $casts = [
        'valores_antiguos' => 'array',
        'valores_nuevos' => 'array',
        'metadata' => 'array',
        'realizado_en' => 'datetime',
    ];

    /**
     * Usuario sobre el que se registra la acción
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Usuario que realizó la acción
     * Nota: performed_by no tiene foreign key, se obtiene mediante consulta
     */
    public function performer()
    {
        if (!$this->performed_by) {
            return null;
        }
        return User::find($this->performed_by);
    }

    /**
     * Obtener el usuario que realizó la acción mediante consulta directa
     * Útil para eager loading o cuando se necesita el modelo completo
     */
    public function getPerformerAttribute()
    {
        if (!$this->performed_by) {
            return null;
        }
        return User::find($this->performed_by);
    }

    /**
     * Obtener el modelo relacionado (polimórfico)
     */
    public function relatedModel()
    {
        if (!$this->related_model_type || !$this->related_model_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . $this->related_model_type;
        if (!class_exists($modelClass)) {
            return null;
        }

        return $modelClass::find($this->related_model_id);
    }
}

