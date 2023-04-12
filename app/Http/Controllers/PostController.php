<?php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function __invoke()
    {
        $posts = Post::with('userVotes')->latest()->paginate();

        return view('posts', compact('posts'));
    }
}
