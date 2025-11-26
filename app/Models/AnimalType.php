<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AnimalType
 *
 * @property $id
 * @property $nombre
 * @property $permite_adopcion
 * @property $permite_liberacion
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile[] $animalFiles
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class AnimalType extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'permite_adopcion', 'permite_liberacion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        return $this->hasMany(\App\Models\AnimalFile::class, 'id', 'tipo_id');
    }
    
}
