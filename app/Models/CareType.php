<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CareType
 *
 * @property $id
 * @property $nombre
 * @property $descripcion
 * @property $created_at
 * @property $updated_at
 *
 * @property Care[] $cares
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CareType extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'descripcion'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cares()
    {
        return $this->hasMany(\App\Models\Care::class, 'id', 'tipo_cuidado_id');
    }
    
}
