<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Care
 *
 * @property $id
 * @property $hoja_animal_id
 * @property $tipo_cuidado_id
 * @property $descripcion
 * @property $fecha
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile $animalFile
 * @property CareType $careType
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Care extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['hoja_animal_id', 'tipo_cuidado_id', 'descripcion', 'fecha', 'imagen_url'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function animalFile()
    {
        return $this->belongsTo(\App\Models\AnimalFile::class, 'hoja_animal_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function careType()
    {
        return $this->belongsTo(\App\Models\CareType::class, 'tipo_cuidado_id', 'id');
    }
    
}
