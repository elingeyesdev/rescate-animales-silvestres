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
    protected $fillable = [
        'tratamiento_id',
        'tratamiento_texto',
        'descripcion',
        'diagnostico',
        'peso',
        'temperatura',
        'recomendacion',
        'apto_traslado',
        'fecha',
        'veterinario_id',
        'animal_file_id',
        'imagen_url'
    ];

    protected $casts = [
        'fecha' => 'date',
        'peso' => 'decimal:2',
        'temperatura' => 'decimal:2',
    ];


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
    
    /**
     * Hoja de Animal asociada a la evaluación.
     */
    public function animalFile()
    {
        return $this->belongsTo(\App\Models\AnimalFile::class, 'animal_file_id', 'id');
    }
    
    // Nota: no se define relación directa de FK para animal_file_id por decisión de negocio.
    
}
