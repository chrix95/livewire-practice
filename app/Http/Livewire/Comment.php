<?php

namespace App\Http\Livewire;

use App\Models\Comment as CommentModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as Image;
use Livewire\Component;
use Livewire\WithPagination;

class Comment extends Component
{
    use WithPagination;

    public $newComment;
    public $image;
    public $ticketId;

    protected $listeners = [
        'fileUpload' => 'handleFileUpload',
        'ticketSelected',
    ];

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'newComment' => 'required|max:255',
        ]);
    }

    public function handleFileUpload($imageData)
    {
        $this->image = $imageData;
    }

    public function ticketSelected($ticketId)
    {
        $this->ticketId = $ticketId;
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|max:255',
        ]);
        $image = $this->storeImage();
        CommentModel::create([
            'body' => $this->newComment,
            'image' => $image,
            'user_id' => 1,
            'support_ticket_id' => $this->ticketId,
        ]);
        $this->newComment = '';
        $this->image = '';
        session()->flash('message', 'Comment has been added successfully 😊');
    }

    public function storeImage()
    {
        if (!$this->image) {
            return null;
        }
        $img = Image::make($this->image)->encode('jpg');
        $name = Str::random() . '.jpg';
        Storage::disk('public')->put($name, $img);
        return $name;
    }

    public function removeComment($commentId)
    {
        // Find the comment
        $commentToDelete = CommentModel::find($commentId);
        // delete comment from the database
        Storage::disk('public')->delete($commentToDelete->image);
        $commentToDelete->delete();
        session()->flash('message', 'Comment deleted successfully 😭');
    }

    public function render()
    {
        return view('livewire.comment', [
            'comments' => CommentModel::where('support_ticket_id', $this->ticketId)->latest()->paginate(2),
        ]);
    }
}
