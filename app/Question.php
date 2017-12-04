<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    
    protected $fillable = [
        'title',
        'content',
        'created_by',
        'updated_by',
        'slug'
    ];
    
    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
    
    public function creator()
    {
        return $this->belongsTo('App\User', 'created_by');
    }
    
    public function updator()
    {
        return $this->belongsTo('App\User', 'updated_by');
    }
    
    public function answers()
    {
        return $this->hasMany('App\Answer');
    }
    
    public function comments()
    {
        return $this->hasMany('App\QuestionComment');
    }
    
    public function votes()
    {
        return $this->belongsToMany('App\User', 'question_votes');
    }
}
