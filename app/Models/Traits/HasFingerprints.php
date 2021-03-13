<?php
namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasFingerprints {
    public function creator(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'created_by');
    }

    public function updator(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'updated_by');
    }
}