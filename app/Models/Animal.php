<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Animal
 *
 * @property $id
 * @property $nombre
 * @property $sexo
 * @property $descripcion
 * @property $created_at
 * @property $updated_at
 * @property $reporte_id
 *
 * @property Report $report
 * @property AnimalFile[] $animalFiles
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Animal extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'sexo', 'descripcion', 'reporte_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function report()
    {
        return $this->belongsTo(\App\Models\Report::class, 'reporte_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        return $this->hasMany(\App\Models\AnimalFile::class, 'animal_id', 'id');
    }
    
}
