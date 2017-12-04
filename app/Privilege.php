<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model
{
    protected $hidden = [
        'id',
        'pivot'
    ];
    
    /**
     * Relationship with User
     *
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
