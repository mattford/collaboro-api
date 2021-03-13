<?php

namespace App\Models;

use App\Models\Traits\HasFingerprints;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Answer extends Model
{
    use HasFingerprints;
    use HasTimestamps;

    protected $fillable = [
        'content',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
    
    public function question(): BelongsTo
    {
        return $this->belongsTo('App\Models\Question');
    }
    
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
    
//    public function votes(): BelongsToMany
//    {
//        return $this->belongsToMany('App\Models\User', 'answer_votes');
//    }
}
