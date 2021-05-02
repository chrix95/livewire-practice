<?php

namespace App\Http\Livewire;

use App\Models\Comment as CommentModel;
use Livewire\Component;

class Comment extends Component
{
    public $newComment;
    public $comments;
    public $errorMessage = false;

    public function mount()
    {
        $this->comments = CommentModel::latest()->get();
    }

    public function addComment()
    {
        if ($this->newComment == '') {
            $this->errorMessage = true;
            return;
        }
        $createdComment = CommentModel::create([
            'body' => $this->newComment,
            'user_id' => 1,
        ]);
        $this->comments->prepend($createdComment);
        $this->newComment = '';
        $this->errorMessage = false;
    }

    public function render()
    {
        return view('livewire.comment');
    }
}
