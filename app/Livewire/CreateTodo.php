<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class CreateTodo extends Component
{
    #[Rule('required|string|min:5')]
    public $name = '';

    public function createTodo()
    {
        $this->validate();

        Todo::create([
            'name' => $this->name,
            'user_id' => Auth::id(),
        ]);

        $this->reset(['name']);
        $this->dispatch('todoCreated');
        $this->dispatch('triggerToast', [
            'status' => 'success',
            'message' => 'Todo saved'
        ]);
    }

    public function render()
    {
        return view('livewire.create-todo');
    }
}
