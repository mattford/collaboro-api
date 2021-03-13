<?php

namespace App\Models;

use App\Models\Traits\HasFingerprints;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Question extends Model
{
    use HasFingerprints;
    use HasTimestamps;

    protected $fillable = [
        'title',
        'content',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'slug'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany('App\Models\Tag');
    }
    
    public function answers(): HasMany
    {
        return $this->hasMany('App\Models\Answer');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
//
//    public function votes(): BelongsToMany
//    {
//        return $this->belongsToMany('App\Models\User', 'question_votes');
//    }
}
