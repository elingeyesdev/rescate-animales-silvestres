<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FeedingPortion
 *
 * @property $id
 * @property $cantidad
 * @property $unidad
 * @property $created_at
 * @property $updated_at
 *
 * @property CareFeeding[] $careFeedings
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class FeedingPortion extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['cantidad', 'unidad'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function careFeedings()
    {
        return $this->hasMany(\App\Models\CareFeeding::class, 'id', 'feeding_portion_id');
    }
    
}
