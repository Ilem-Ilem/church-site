<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{BeliversAcademy, Chapter};
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions;

    #[Url(keep: true)]
    public $chapter;
    public $chapterId;
    public $academy = [
        'status' => '',
        'start_at' => '',
        'chapter_id' => '',
    ];

    public function mount()
    {
        $academy = BeliversAcademy::with('chapter')->whereHas('chapter', fn($chapter) => $chapter->where('name', '=', $this->chapter))->first();
        $this->academy =
            $academy != null
                ? $academy->toArray()
                : [
                    'status' => '',
                    'start_at' => '',
                    'chapter_id' => '',
                ];
        $this->chapterId = Chapter::where('name', e($this->chapter))->first()->id;
    }

    public function save()
    {
        $this->validate([
            'academy.status' => 'required|in:open,closed',
            'academy.start_at' => 'required|date',
        ]);

        $academy = BeliversAcademy::first();
        $academy->status = $this->academy['status'];
        $academy->start_at = $this->academy['start_at'];
        $academy->chapter_id = $this->chapterId;
        $academy->save();
        $this->academy = $academy->toArray();
        $this->dispatch('$refresh');
        $this->toast()->success('Success', 'Class status Changed')->send();
    }
}; ?>

<div>
    <x-card class="darK:bg-zinc-900">
        <x-link class="px-4 py-2 bg-blue-700 text-white font-semibold mt-3 mb-2 rounded" :href="route('admin.dashboard.believers_class.index', request()->query())">+ Add Classes</x-link>
        <form wire:submit.prevent='save'>
            <div>
                <label for="status">Status</label>
                {{ $academy['status'] }}
                <select name="" id="status" wire:model.live='academy.status'
                    class="w-full dark:bg-dark-900 dark:text-white border border-dark-800 mb-2">
                    <option value="">Start Status</option>
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                </select>
                @error('academy.status')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="" class="font-bold mt-4">Start At</label>
                <input type="date" name="" id="" wire:model='academy.start_at'
                    class="dark:bg-zinc-900 darK:text-white w-full rounded-sm border-dark-950">
                @error('academy.start_at')
                    <span class="text-sm text-red-700">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-700 text-white font-semibold mt-3">
                <span wire:loading.remove>Save</span>
                <span wire:loading wire:target="save"> 
                    <x-spinner-loader color="white" size="sm"></x-spinner-loader>saving
                </span>
            </button>
        </form>
    </x-card>
</div>
