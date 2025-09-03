<?php

use App\Models\Chapter;
use App\Models\Functions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions;

    #[Url]
    public $chapter;
    public $name;
    public $description;

    public function create()
    {

        $function = $this->save();
        $this->toast()->success('Done', 'Function ' . $function->name . ' created successfully!')->flash()->send();
        $this->reset(['name', 'description']);
        return $this->redirect(route('admin.dashboard.functions.index', ['chapter' => e($this->chapter)]), navigate: true);
    }

    public function createAndCreateAgain()
    {
        $function = $this->save();
        $this->toast()->success('Done', 'Function ' . $function->name . ' created successfully!')->send();
        $this->reset(['name', 'description']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Logic to create a new function

        return $function = Functions::create([
            'name' => $this->name,
            'description' => $this->description,
            'chapter_id' => Chapter::where('name', '=', e($this->chapter))->first()->id
        ]);
    }
}; ?>

<div>
    <x-fancy-header title="Create Function" subtitle="Add a new function to the system" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard', request()->query())],
        ['label' => 'Functions', 'url' => route('admin.dashboard.functions.index', request()->query())],
        ['label' => 'Create Function']
    ]" class="mb-5">
        <x-button href="{{ route('admin.dashboard.functions.index', request()->query()) }}" wire:navigate>
            Back to Functions
        </x-button>
    </x-fancy-header>
    <x-card class="bg-white dark:bg-zinc-800 shadow-md dark:shadow-none rounded-lg">

        <form >
            <div class="space-y-4 bg-white dark:bg-zinc-800 shadow-md dark:shadow-none rounded-lg">
            <div>
                <label for="name" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Name</label>
                <input type="text" id="name" wire:model="name"
                class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400">
                @error('name') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span> @enderror
            </div>

            <div>
                <label for="description"
                class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">Description</label>
                <textarea id="description" wire:model="description" rows="4"
                class="mt-1 block w-full rounded-md border-zinc-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-zinc-800 dark:border-zinc-600 dark:text-zinc-100 dark:focus:border-indigo-400 dark:focus:ring-indigo-400"></textarea>
                @error('description') <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex space-x-4">
                <button type="button" wire:click="create"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                <span wire:loading.remove wire:target="create">Create</span>
                <span wire:loading wire:target="create">
                    <div class="flex justify-between">
                    <x-icon name="arrow-path" class="mr-2 h-6 w-6 animate-spin" />
                    Creating...
                    </div>
                </span>
                </button>
                <button type="button" wire:click="createAndCreateAgain"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900">
                <span wire:loading.remove wire:target="createAndCreateAgain">Create & Add Another</span>
                <span wire:loading wire:target="createAndCreateAgain">
                    <div class="flex justify-between">
                    <x-icon name="arrow-path" class="mr-2 h-6 w-6 animate-spin" />
                    Creating...
                    </div>
                </span>
                </button>
            </div>
            </div>
        </form>
    </x-card>
</div>