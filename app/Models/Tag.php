<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'name'
    ];
    
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Question');
    }
}
