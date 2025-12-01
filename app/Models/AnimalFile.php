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
 * @property $estado_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Species $species
 * @property AnimalStatus $animalStatus
 * @property Animal $animal
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
    protected $fillable = ['animal_id', 'especie_id', 'imagen_url', 'estado_id', 'centro_id'];


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
     * Animal asociado (animal_files -> animals)
     */
    public function animal()
    {
        return $this->belongsTo(\App\Models\Animal::class, 'animal_id', 'id');
    }
        
    /**
     * Centro actual asociado a la hoja de vida (campo sin FK dura en DB).
     */
    public function center()
    {
        return $this->belongsTo(\App\Models\Center::class, 'centro_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cares()
    {
        return $this->hasMany(\App\Models\Care::class, 'hoja_animal_id', 'id');
    }
    
    /**
     * Relación 1:1 con Release por la nueva FK en releases
     */
    public function release()
    {
        return $this->hasOne(\App\Models\Release::class, 'animal_file_id', 'id');
    }
    
}
