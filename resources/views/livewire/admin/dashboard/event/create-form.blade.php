<?php
// File: app/Http/Livewire/CreateEvent.php

namespace App\Http\Livewire;

use App\Models\Chapter;
use App\Models\Events;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\{Layout};
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithFileUploads, Interactions;

    // Ownership / scoping
    public $chapter;
    public $chapter_id;

    // Core details
    public $title;
    public $slug;
    public $description;

    // Scheduling
    public $start_at;
    public $end_at;
    public $timezone;

    // Venue / media
    public $location;
    public $is_online = false;
    public $livestream_url;
    public $banner; // uploaded file

    // Management
    public $status = 'draft';
    public $capacity;
    public $registration_required = false;


    protected function rules(): array
    {
        return [
            'chapter' => ['required', 'string'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('events')->where(function ($query) {
                return $query->where('chapter_id', $this->chapter_id);
            })],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_online' => ['boolean'],
            'livestream_url' => ['nullable', 'url'],
            'banner' => ['nullable', 'image', 'max:5120'], // 5MB
            'status' => ['required', Rule::in(['draft','published','cancelled','archived'])],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'registration_required' => ['boolean'],
        ];
    }

    public function mount($chapter = null)
    {
        // Accept chapter name or id
        $this->chapter = $chapter ?? request('chapter');
        $this->chapter_id = Chapter::where('name', $this->chapter)->value('id');

        // load dark mode preference from browser via JS -> set on mount if passed via query string
        $this->darkMode = false;
    }

    public function updatedTitle($value)
    {
        if (!$this->slug) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
    {
        $this->validate();

        $data = [
            'chapter_id' => $this->chapter_id,
            'created_by' => auth()->id(),
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'description' => $this->description,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'timezone' => $this->timezone,
            'location' => $this->location,
            'is_online' => $this->is_online,
            'livestream_url' => $this->livestream_url,
            'status' => $this->status,
            'capacity' => $this->capacity,
            'registration_required' => $this->registration_required,
        ];

        $event = Events::create($data);

        if ($this->banner) {
            $path = $this->banner->storePublicly('events/banners', 'public');
            $event->update(['banner' => $path]);
        }

        $this->toast()->success('Event created', 'Your event was created successfully')->send();

        return redirect()->route('events.show', [$event->id]);
    }


}?>

<div>
    <div class="min-h-screen bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 p-6">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-semibold">Create Event</h1>
            </div>

            <form wire:submit.prevent="save" class="space-y-6 bg-zinc-50 dark:bg-zinc-800 p-6 rounded-2xl shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input wire:model.lazy="title" type="text" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                        @error('title') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Slug (optional)</label>
                        <input wire:model.lazy="slug" type="text" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                        @error('slug') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea wire:model.lazy="description" rows="4" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border"></textarea>
                    @error('description') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Start</label>
                        <input wire:model.lazy="start_at" type="datetime-local" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                        @error('start_at') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">End (optional)</label>
                        <input wire:model.lazy="end_at" type="datetime-local" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                        @error('end_at') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Timezone</label>
                        <input wire:model.lazy="timezone" type="text" placeholder="e.g. UTC, America/New_York" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                        @error('timezone') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Location</label>
                        <input wire:model.lazy="location" type="text" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                        @error('location') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center">
                        <input wire:model="is_online" type="checkbox" class="mr-2" />
                        <span>Is online</span>
                    </label>

                    <label class="inline-flex items-center">
                        <input wire:model="registration_required" type="checkbox" class="mr-2" />
                        <span>Registration required</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Livestream URL</label>
                    <input wire:model.lazy="livestream_url" type="url" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                    @error('livestream_url') <span class="text-xs text-red-400">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Banner (image)</label>
                    <input wire:model="banner" type="file" accept="image/*" />
                    @error('banner') <span class="text-xs text-red-400">{{ $message }}</span> @enderror

                    <div class="mt-3">
                        <template x-if="$wire.banner">
                            <img :src="$wire.banner ? URL.createObjectURL($wire.banner) : ''" class="max-h-40 rounded-md" />
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select wire:model="status" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Capacity</label>
                        <input wire:model.lazy="capacity" type="number" min="1" class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                    </div>

                    <div class="text-right">
                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white">Create Event</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
