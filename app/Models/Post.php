<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'text',
    ];

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function rating(): int
    {
        return $this->votes()->sum('vote');
    }

    public function userVotes(): HasOne
    {
        return $this->votes()->one()->where('user_id', auth()->id());
    }
}
