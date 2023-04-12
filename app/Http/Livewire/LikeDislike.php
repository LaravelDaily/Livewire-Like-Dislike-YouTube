<?php

namespace App\Http\Livewire;

use App\Models\Post;
use App\Models\Vote;
use Livewire\Component;
use Illuminate\Contracts\View\View;

class LikeDislike extends Component
{
    public Post $post;

    public Vote|null $vote = null;

    public string $type = '';

    protected $listeners = ['update' => '$refresh'];

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

/*    public function toggleUpvote(int $value): bool|Vote
    {
        if (! in_array($value, [-1, 1])) {
            return false;
        }

        $user = auth()->user();

        if (is_null($user)) {
            $this->addError('unauthenticated', 'You need to [<a href="' . route('login') .'">login</a>] to click like/dislike');
            return false;
        }

        if ($this->vote && $this->vote->vote === $value) {
            $this->vote->delete();
            $this->reset('vote');

            return true;
        }

        if ($this->vote && $this->vote->vote !== $value) {
            $this->vote->update(['vote' => $value]);

            return true;
        }

        $this->vote = $this->post->votes()->create([
            'user_id' => $user->id,
            'vote'    => $value
        ]);

        return $this->vote;
    }*/

    public function like(): Vote
    {
        $this->validAccess();

        if ($this->hasVoted(1)) {
            return $this->updateVote(0);
        }

        return $this->updateVote(1);
    }

    public function dislike(): Vote
    {
        $this->validAccess();

        if ($this->hasVoted(-1)) {
            return $this->updateVote(0);
        }

        return $this->updateVote(-1);
    }

    public function hasVoted(int $val): bool
    {
        return $this->post->votes()
            ->where('user_id', auth()->id())
            ->where('vote', $val)
            ->exists();
    }

    public function updateVote(int $val)
    {
        $post = $this->post->votes()
            ->updateOrCreate(
                ['user_id' => auth()->id()],
                ['vote' => $val]);

        $this->emitSelf('update');

        return $post;
    }

    private function validAccess(): bool
    {
        if (auth()->guest()) {
            $this->addError('unauthenticated', 'You need to [<a href="' . route('login') . '">login</a>] to click like/dislike');

            return false;
        }

        return true;
    }

    public function render(): View
    {
        return view('livewire.like-dislike');
    }
}
