<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class PostController extends Controller
{
    public function __invoke(): View
    {
        $posts = Post::with('userVotes')
            ->withCount(['votes as likesCount' => fn (Builder $query) => $query->where('vote', '>', 0)], 'vote')
            ->withCount(['votes as dislikesCount' => fn (Builder $query) => $query->where('vote', '<', 0)], 'vote')
            ->latest()
            ->paginate();

        return view('posts', compact('posts'));
    }
}
