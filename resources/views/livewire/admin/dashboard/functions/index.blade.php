<?php

use App\Models\Chapter;
use App\Models\Functions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;


new #[Layout('components.layouts.admin')] class extends Component {
    public ?int $quantity = 10;

    public ?string $search = null;

    public array $selected = [];

    #[Url]
    public $chapter;

    public function mount()
    {

    }

    public function with(): array
    {

        return [
            'headers' => [
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'description', 'label' => 'Description'],
                ['index' => 'action'],
            ],
            'rows' => $this->rows()->paginate($this->quantity)->withQueryString()

        ];
    }

    public function rows()
    {
        return Functions::withCount('teams')
            ->when($this->search, fn($q) => $q->where('name', 'Like', "%{$this->search}%"))
            ->when($this->chapter, fn($q) => $q->where('chapter_id', '=', Chapter::where('name', '=', $this->chapter)->first()->id));
    }

    public function deleteBulk()
    {
        $count = Functions::whereIn('id', $this->selected)->count();
        Functions::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->toast()->success('Done', $count . ' functions deleted successfully!')->flash()->send();
    }
}; ?>

<div>
    <x-fancy-header title="Functions" subtitle="Manage Functions" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard', request()->query())],
        ['label' => 'Functions']
    ]" class="mb-5">
        <x-button href="{{ route('admin.dashboard.functions.create-function', request()->query()) }}" wire:navigate>
            Add New Function
        </x-button>
    </x-fancy-header>
    @if($selected !== [])
        <div class="flex items-center space-x-4 mb-4">
            <x-button color="red" wire:click="deleteBulk" class="px-4 py-2 text-red-600 border border-red-600 rounded shadow-md hover:bg-red-50 active:shadow-inner transition duration-150 ease-in-out">
                Delete Selected
            </x-button>
            <x-button color="blue" class="px-4 py-2 text-blue-600 border border-blue-600 rounded shadow-md hover:bg-blue-50 active:shadow-inner transition duration-150 ease-in-out">
                Export Selected
            </x-button>
        </div>
    @endif
    <x-table :$headers :$rows :filter="['quantity' => 'quantity', 'search' => 'search']" :quantity="[5, 15, 50, 100, 250]" paginate persistent selectable wire:model.live="selected" loading>
        @interact('column_action', $row)
        {{-- Delete Team --}}
        <x-button.circle color="red" icon="trash" wire:click="deleteTeam('{{ $row->id }}')" />

        {{-- Edit Team --}}
        <x-link :href="route('admin.dashboard.teams.edit', ['team' => $row->id, 'chapter' => request()->query('chapter')])"
            class="inline-block px-3 py-1.5 text-sm font-medium text-white bg-amber-500 rounded shadow-md hover:bg-amber-600 active:shadow-inner transition duration-150 ease-in-out"
            wire:navigate>
            Edit Team
        </x-link>
        @endinteract
    </x-table>
</div>