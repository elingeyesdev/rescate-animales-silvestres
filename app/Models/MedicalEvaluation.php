<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class MedicalEvaluation
 *
 * @property $id
 * @property $tratamiento_id
 * @property $descripcion
 * @property $fecha
 * @property $veterinario_id
 * @property $created_at
 * @property $updated_at
 *
 * @property TreatmentType $treatmentType
 * @property Veterinarian $veterinarian
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class MedicalEvaluation extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['tratamiento_id', 'descripcion', 'fecha', 'veterinario_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function treatmentType()
    {
        return $this->belongsTo(\App\Models\TreatmentType::class, 'tratamiento_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function veterinarian()
    {
        return $this->belongsTo(\App\Models\Veterinarian::class, 'veterinario_id', 'id');
    }
    
}
