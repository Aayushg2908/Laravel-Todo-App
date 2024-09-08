<?php

use App\Models\User;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component {
    public $todos;
    public string $content = '';
    public $editingTodoId = null;
    public $editingTodoContent = '';
    public $deletingTodoId = null;

    public function mount(): void
    {
        $this->todos = Auth::user()->todos()->orderBy('created_at', 'desc')->get();
    }

    public function createTodo(): void
    {
        $this->validate([
            'content' => ['required', 'string', 'max:255'],
        ]);

        Auth::user()->todos()->create([
            'content' => $this->content,
        ]);

        $this->content = '';
        $this->todos = Auth::user()->todos()->orderBy('created_at', 'desc')->get();
    }

    public function toggleComplete($todoId): void
    {
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
        $this->todos = Auth::user()->todos()->orderBy('created_at', 'desc')->get();
    }

    public function editTodo($todoId): void
    {
        $this->editingTodoId = $todoId;
        $this->editingTodoContent = Todo::find($todoId)->content;
    }

    public function updateTodo(): void
    {
        $this->validate([
            'editingTodoContent' => ['required', 'string', 'max:255'],
        ]);

        $todo = Todo::find($this->editingTodoId);
        $todo->content = $this->editingTodoContent;
        $todo->save();

        $this->editingTodoId = null;
        $this->editingTodoContent = '';
        $this->todos = Auth::user()->todos()->orderBy('created_at', 'desc')->get();
    }

    public function confirmDelete($todoId): void
    {
        $this->deletingTodoId = $todoId;
    }

    public function deleteTodo(): void
    {
        Todo::destroy($this->deletingTodoId);
        $this->deletingTodoId = null;
        $this->todos = Auth::user()->todos()->orderBy('created_at', 'desc')->get();
    }
};

?>

<div class="space-y-6">
    <form wire:submit.prevent="createTodo" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div>
            <x-input-label for="content" :value="__('New Todo')" class="text-lg font-semibold" />
            <x-text-input wire:model="content" id="content" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" type="text" name="content" required autofocus autocomplete="name" placeholder="Enter your todo..." />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <x-secondary-button class="mt-4 w-full justify-center">
            {{ __('Create Todo') }}
        </x-secondary-button>
    </form>

    @if($todos->isEmpty())
        <p class="text-gray-500 text-center text-lg">No todos yet. Add one above!</p>
    @else
        <ul class="space-y-3">
            @foreach($todos as $todo)
                <li class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg transition duration-300 ease-in-out hover:shadow-lg">
                    <div class="flex items-center justify-between p-4">
                        <div class="flex items-center gap-2 flex-grow">
                            <input class="dark:bg-gray-900 h-5 w-5 text-indigo-600 transition duration-150 ease-in-out rounded-full" type="checkbox" wire:click="toggleComplete({{ $todo->id }})" {{ $todo->completed ? 'checked' : '' }}>
                            <span class="text-sm sm:text-base {{ $todo->completed ? 'line-through text-gray-500' : 'text-gray-900 dark:text-gray-100' }}">{{ $todo->content }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $todo->created_at->diffForHumans() }}</span>
                            <x-secondary-button x-on:click.prevent="$dispatch('open-modal', 'edit-todo')" wire:click="editTodo({{ $todo->id }})">Edit</x-secondary-button>
                            <x-danger-button x-on:click.prevent="$dispatch('open-modal', 'confirm-todo-deletion')" wire:click="confirmDelete({{ $todo->id }})">Delete</x-danger-button>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif

    <x-modal name="edit-todo" :show="$editingTodoId !== null" focusable>
        <form wire:submit.prevent="updateTodo" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Edit Todo') }}
            </h2>

            <div class="mt-6">
                <x-input-label for="editingTodoContent" value="{{ __('Content') }}" class="sr-only" />

                <x-text-input
                    wire:model="editingTodoContent"
                    id="editingTodoContent"
                    name="editingTodoContent"
                    class="mt-1 block w-full"
                    type="text"
                    required
                />

                <x-input-error :messages="$errors->get('editingTodoContent')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-primary-button class="ms-3" x-on:click="$dispatch('close')">
                    {{ __('Save') }}
                </x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="confirm-todo-deletion" :show="$deletingTodoId !== null" focusable>
        <form wire:submit.prevent="deleteTodo" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ __('Are you sure you want to delete this todo?') }}
            </h2>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <x-danger-button class="ms-3" x-on:click="$dispatch('close')">
                    {{ __('Delete Todo') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</div>