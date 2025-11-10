<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AnimalFile
 *
 * @property $id
 * @property $animal_id
 * @property $tipo_id
 * @property $especie_id
 * @property $imagen_url
 * @property $raza_id
 * @property $estado_id
 * @property $adopcion_id
 * @property $liberacion_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Adoption $adoption
 * @property Species $species
 * @property AnimalStatus $animalStatus
 * @property Release $release
 * @property Animal $animal
 * @property AnimalType $animalType
 * @property Care[] $cares
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AnimalFile extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['animal_id', 'tipo_id', 'especie_id', 'imagen_url', 'raza_id', 'estado_id', 'adopcion_id', 'liberacion_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function adoption()
    {
        return $this->belongsTo(\App\Models\Adoption::class, 'adopcion_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Species::class, 'especie_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function animalStatus()
    {
        return $this->belongsTo(\App\Models\AnimalStatus::class, 'estado_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function release()
    {
        return $this->belongsTo(\App\Models\Release::class, 'liberacion_id', 'id');
    }
    
    /**
     * Animal asociado (animal_files -> animals)
     */
    public function animal()
    {
        return $this->belongsTo(\App\Models\Animal::class, 'animal_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function animalType()
    {
        return $this->belongsTo(\App\Models\AnimalType::class, 'tipo_id', 'id');
    }
    
    /**
     * Raza relacionada vía raza_id
     */
    public function breed()
    {
        return $this->belongsTo(\App\Models\Breed::class, 'raza_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cares()
    {
        return $this->hasMany(\App\Models\Care::class, 'hoja_animal_id', 'id');
    }
    
}
