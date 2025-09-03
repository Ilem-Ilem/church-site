<?php

use App\Models\Functions;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;


new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions;

    public $selectedTeam;

    public $selectedFunction;

    public ?array $functions;

    public ?array $teams;

    public ?array $teamsWithFunction;   

    #[Url]
    public $schaper;

    public function mount()
    {
        $this->functions = Functions::all()->map(fn($f) => [
            'value' => $f->id,
            'label' => $f->name,
        ])->toArray();
    }

    public function updatedselectedFunction($functionId){
        dd($functionId);
    }
}; ?>

<div>
    <x-fancy-header title="Teams" subtitle="Manage Teams" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard', request()->query())],
        ['label'=>'Teams', 'url'=>route('admin.dashboard.teams', request()->query())],
        ['label' => 'Edit Teams Leader']
    ]" class="mb-4">
      
    </x-fancy-header>
    
<x-card>
    <div class="">
        <div>
            <label for="team" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Select Function <span class="float-right">{{ count($functions) }}</span>
            </label>
            <select id="team" wire:model.live="selectedFunction" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 shadow-sm
                       bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                       focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-50">
                <option value="">Choose a team</option>
                @foreach($functions as $function)
                    <option value="{{ $function['value'] }}">{{ $function['label'] }}</option>
                @endforeach
            </select>
        </div>
        {{-- 
                Teams
        --}}
        <div>
            <label for="team" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Select Function <span class="float-right">{{ count($functions) }}</span>
            </label>
            <select id="team" wire:model.live="selectedFunction" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 shadow-sm
                       bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                       focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-50">
                <option value="">Choose a team</option>
                @foreach($functions as $function)
                    <option value="{{ $function['value'] }}">{{ $function['label'] }}</option>
                @endforeach
            </select>
        </div>
    </div>
</x-card>

</div>
