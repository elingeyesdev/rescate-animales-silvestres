<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Disposition
 *
 * @property $id
 * @property $tipo
 * @property $center_id
 * @property $latitud
 * @property $longitud
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Disposition extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['tipo', 'center_id', 'latitud', 'longitud'];


}
