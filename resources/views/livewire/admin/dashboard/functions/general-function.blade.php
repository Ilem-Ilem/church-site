<?php

use App\Models\Functions;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;

new  #[Layout('components.layouts.admin')]  class extends Component {
    use Interactions;

    public $name;
    public $description;

    public function create()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Logic to create a new function

        $function = Functions::create([
            'name' => $this->name,
            'description' => $this->description,
        ]); 

        $this->toast()->success('Done','Function '.$function->name.' created successfully!');
        $this->reset(['name', 'description']);
        // return $this->redirect(route('admin.functions.index'));
    }
}; ?>

<div>
    <x-card>
        <form wire:submit.prevent="create">
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</label>
                    <input type="text" id="name" wire:model="name" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                    @error('name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                    <textarea id="description" wire:model="description" rows="4" class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"></textarea>
                    @error('description') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                        Create Function
                    </button>
                </div>
            </div>
        </form>
    </x-card>
</div>
