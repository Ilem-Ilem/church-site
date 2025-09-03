<?php

use App\Models\Chapter;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;

new class extends Component {
    use Interactions;

    public $name;

    public $data = [
        'address' => '',
        'city' => '',
        'state' => '',
        'country' => '',
        'description' => ''
    ];

    public function save()
    {
        // Validate the input
        $validatedData = $this->validate([
            'name' => 'required|unique:Chapters,id|min:3', // Assuming 'id' is the PK in Chapter
            'data.address' => 'required|min:2',
            'data.city' => 'required',
            'data.state' => 'required',
            'data.country' => 'required',
            'data.description' => 'nullable|string'
        ]);

        // Save to Chapter model
        $Chapter = Chapter::create([
            'name' => $validatedData['name'], // Using name as ID
            'data' => json_encode($validatedData['data']),
        ]);

        $this->toast()
            ->success('Done!', 'Chapter created successfully!')
            ->flash()
            ->send();

        // Optional: return or flash message
        return $this->redirect(route('super-admin.conclaves'));
    }

}; ?>

<div>
    <x-card>
        <form wire:submit.prevent='save'>
            <!-- Email Address -->
            <flux:input wire:model="name" label="Name" type="text" required autocomplete="name" placeholder="Name" />
            <div class="grid md:grid-cols-2 mt-4  gap-2">
                <flux:input wire:model="data.address" label="Address *" type="text" required autocomplete="name"
                    placeholder="Name" />
                <flux:input label="City *" invalidate wire:model='data.city'></flux:input>
                <flux:input label="State *" invalidate wire:model='data.state'></flux:input>
                <flux:input label="Country *" invlaidate wire:model='data.country'></flux:input>
            </div>

            <div class="mt-3">
                <flux:textarea label="Dscription" wire:model='data.description'></flux:textarea>
            </div>

            <button class="bg-white text-gray-800 dark:bg-zinc-900 dark:text-white border border-gray-300 dark:border-zinc-700 px-6 py-2 mt-5 rounded hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors duration-200">
                <span wire:loading.remove>Save</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </form>
    </x-card>
</div>