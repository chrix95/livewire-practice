<?php

namespace App\Http\Livewire;

use App\Models\Comment as CommentModel;
use Livewire\Component;
use Livewire\WithPagination;

class Comment extends Component
{
    use WithPagination;

    public $newComment;

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'newComment' => 'required|max:255',
        ]);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|max:255',
        ]);
        $createdComment = CommentModel::create([
            'body' => $this->newComment,
            'user_id' => 1,
        ]);
        $this->comments->prepend($createdComment);
        $this->newComment = '';
        session()->flash('message', 'Comment has been added successfully 😊');
    }

    public function removeComment($commentId)
    {
        // Find the comment
        $commentToDelete = CommentModel::find($commentId);
        // delete comment from the database
        $commentToDelete->delete();
        // remove comment from the frontend comments
        $this->comments = $this->comments->where('id', '!=', $commentId);
        session()->flash('message', 'Comment deleted successfully 😭');
    }

    public function render()
    {
        return view('livewire.comment', [
            'comments' => CommentModel::latest()->paginate(2),
        ]);
    }
}
