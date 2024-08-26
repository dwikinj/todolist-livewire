<?php

namespace App\Livewire;

use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class ListTodo extends Component
{
    use WithPagination;

    // edit todo

    public $todoId = '';

    #[Rule('required|string|min:3')]
    public $todoName = '';

    public function startEditing($todoId) {
        $todo = Todo::find($todoId);
        $this->todoId = $todo->id;
        $this->todoName = $todo->name;
    }

    public function cancelEditing() {
        $this->todoId = null;
        $this->todoName = '';
    }

    public function updateTodo() {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('triggerToast', [
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
            return;
        }

       

        $todo = Todo::find($this->todoId);
        $todo->update(['name' => $this->todoName]);

        $this->cancelEditing();
        $this->dispatch('triggerToast', [
            'status' => 'success',
            'message' => 'Todo Updated',
        ]);
    }
    // end edit todo

    public $search = '';
    protected $listeners = ['todoCreated'=> 'render'];

    public function deleteTodo($todoId) {
        $todo = Todo::where('id', $todoId)->where('user_id', Auth::id())->first();

        if ($todo) {
            $todo->delete();
            $this->dispatch('triggerToast', [
                'status' => 'success',
                'message' => 'Todo deleted',
            ]);
        }
    }

    public function render()
    {
        $todos = Todo::where('user_id',Auth::id())->where('name','like','%'. $this->search.'%')->paginate(5);
        return view('livewire.list-todo',compact('todos'));
    }
}
