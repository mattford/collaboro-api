<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $primaryKey = 'slug';
    public $incrementing = false;
    
    protected $hidden = ['pivot'];
    
    public function questions()
    {
        return $this->belongsToMany('App\Question');
    }
}
