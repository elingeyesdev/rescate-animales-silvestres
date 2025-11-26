<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Breed
 *
 * @property $id
 * @property $especie_id
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 *
 * @property Species $species
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Breed extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['especie_id', 'nombre'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Species::class, 'especie_id', 'id');
    }
    
}
