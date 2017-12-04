<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnswerComment extends Model
{
    protected $fillable = [
        'content',
        'updated_by',
        'created_by'
    ];
    
    public function answer()
    {
        return $this->belongsTo('App\Answer');
    }
    
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    
    public function updator()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
}
