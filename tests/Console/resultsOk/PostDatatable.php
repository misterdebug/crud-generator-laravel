<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Livewire\WithPagination;

class PostDatatable extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $posts = Post::when($this->search, function ($query) {
            $query->where('title', 'like', '%' . $this->search . '%');
        })->paginate(10);

        return view('posts.post-datatable', ['posts'=>$posts]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function highlightTitle($title)
    {
        return str()->replace($this->search, '<span style="background-color: yellow">'. $this->search.'</span>', $title);
    }
}
