<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Transfer
 *
 * @property $id
 * @property $rescatista_id
 * @property $centro_id
 * @property $created_at
 * @property $updated_at
 *
 * @property Center $center
 * @property Rescuer $rescuer
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Transfer extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['rescatista_id', 'centro_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function center()
    {
        return $this->belongsTo(\App\Models\Center::class, 'centro_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rescuer()
    {
        return $this->belongsTo(\App\Models\Rescuer::class, 'rescatista_id', 'id');
    }
    
}
