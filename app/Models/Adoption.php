<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Adoption
 *
 * @property $id
 * @property $direccion
 * @property $latitud
 * @property $longitud
 * @property $detalle
 * @property $aprobada
 * @property $adoptante_id
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile $animalFile
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Adoption extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['direccion', 'latitud', 'longitud', 'detalle', 'aprobada', 'adoptante_id', 'animal_file_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function animalFile()
    {
        return $this->belongsTo(\App\Models\AnimalFile::class, 'animal_file_id', 'id');
    }
    
    public function adopter()
    {
        return $this->belongsTo(\App\Models\Person::class, 'adoptante_id', 'id');
    }
    
}
