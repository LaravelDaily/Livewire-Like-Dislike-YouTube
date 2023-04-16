<?php

namespace App\Http\Livewire;

use Throwable;
use App\Models\Post;
use App\Models\Vote;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;

class LikeDislike extends Component
{
    public Post $post;
    public ?Vote $userVote = null;
    public int $likes = 0;
    public int $dislikes = 0;
    public int $lastUserVote = 0;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->userVote = $post->userVotes;
        $this->likes = $post->likesCount;
        $this->dislikes = $post->dislikesCount;
        $this->lastUserVote = $this->userVote->vote ?? 0;
    }

    /**
     * @throws Throwable
     */
    public function like(): void
    {
        $this->validateAccess();

        if ($this->hasVoted(1)) {
            $this->updateVote(0);
            return;
        }

        $this->updateVote(1);
    }

    /**
     * @throws Throwable
     */
    public function dislike(): void
    {
        $this->validateAccess();

        if ($this->hasVoted(-1)) {
            $this->updateVote(0);
            return;
        }

        $this->updateVote(-1);
    }

    public function render(): View
    {
        return view('livewire.like-dislike');
    }

    private function hasVoted(int $val): bool
    {
        return $this->userVote && $this->userVote->vote === $val;
    }

    private function updateVote(int $val): void
    {
        if ($this->userVote) {
            $this->post->votes()->update(['user_id' => auth()->id(), 'vote' => $val]);
        } else {
            $this->userVote = $this->post->votes()->create(['user_id' => auth()->id(), 'vote' => $val]);
        }
        $this->setLikesAndDislikesCount($val);

        $this->lastUserVote = $val;
    }

    /**
     * @throws Throwable
     */
    private function validateAccess(): void
    {
        throw_if(
            auth()->guest(),
            ValidationException::withMessages(['unauthenticated' => 'You need to <a href="' . route('login') . '" class="underline">login</a> to click like/dislike'])
        );
    }

    private function setLikesAndDislikesCount(int $val): void
    {
        match (true) {
            $this->lastUserVote === 0 && $val === 1 => $this->likes++,
            $this->lastUserVote === 0 && $val === -1 => $this->dislikes++,
            $this->lastUserVote === 1 && $val === 0 => $this->likes--,
            $this->lastUserVote === -1 && $val === 0 => $this->dislikes--,
            $this->lastUserVote === -1 && $val === 1 => call_user_func(function () {
                $this->dislikes--;
                $this->likes++;
            }),
            $this->lastUserVote === 1 && $val === -1 => call_user_func(function () {
                $this->dislikes++;
                $this->likes--;
            }),
        };
    }
}
