<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Report
 *
 * @property $id
 * @property $persona_id
 * @property $aprobado
 * @property $imagen_url
 * @property $observaciones
 * @property $cantidad_animales
 * @property $created_at
 * @property $updated_at
 *
 * @property Person $person
 * @property Animal[] $animals
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Report extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['persona_id', 'aprobado', 'imagen_url', 'observaciones', 'cantidad_animales', 'latitud', 'longitud', 'direccion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function person()
    {
        return $this->belongsTo(\App\Models\Person::class, 'persona_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        // Compatibilidad: obtener animal_files a través de animals (report -> animals -> animal_files)
        return $this->hasManyThrough(
            \App\Models\AnimalFile::class,
            \App\Models\Animal::class,
            'reporte_id',   // Foreign key on animals referencing reports.id
            'animal_id',    // Foreign key on animal_files referencing animals.id
            'id',           // Local key on reports
            'id'            // Local key on animals
        );
    }
    
    /**
     * Animales relacionados (reporte tiene muchos animales)
     */
    public function animals()
    {
        return $this->hasMany(\App\Models\Animal::class, 'reporte_id', 'id');
    }
}
