<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Center
 *
 * @property $id
 * @property $nombre
 * @property $direccion
 * @property $latitud
 * @property $longitud
 * @property $contacto
 * @property $created_at
 * @property $updated_at
 *
 * @property Transfer[] $transfers
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Center extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'direccion', 'latitud', 'longitud', 'contacto'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transfers()
    {
        return $this->hasMany(\App\Models\Transfer::class, 'id', 'centro_id');
    }
    
}
