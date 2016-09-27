<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarkerVisit extends Model
{
     /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id', 'created_at', 'updated_at'];

   	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    /**
     * relation between this model and user model
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('\App\Models\User'); 
    }

    /**
     * relation between this model and marker model
     * @return BelongsTo
     */
    public function marker()
    {
        return $this->belongsTo('\App\Models\Marker'); 
    }
}
