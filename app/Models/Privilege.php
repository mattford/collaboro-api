<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function users(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\User');
    }
}
