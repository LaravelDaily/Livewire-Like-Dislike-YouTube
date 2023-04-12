<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    protected $fillable = [
        'post_id',
        'user_id',
        'vote',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
