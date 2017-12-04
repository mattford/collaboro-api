<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionComment extends Model
{
    protected $fillable = [
        'content',
        'created_by',
        'updated_by'
    ];
    
    public function question()
    {
        return $this->belongsTo('App\Question');
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
