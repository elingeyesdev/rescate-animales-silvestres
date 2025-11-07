<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Species
 *
 * @property $id
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @property AnimalFile[] $animalFiles
 * @property Breed[] $breeds
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Species extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function animalFiles()
    {
        return $this->hasMany(\App\Models\AnimalFile::class, 'id', 'especie_id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function breeds()
    {
        return $this->hasMany(\App\Models\Breed::class, 'id', 'especie_id');
    }
    
}
