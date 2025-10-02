<?php

use App\Models\{Events, Chapter, Accounts, AccountEvent};
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions, WithPagination;

    public ?int $quantity = 10;
    public ?string $search = null;
    public array $selected = [];
    public ?string $bulkAction = null;
    public $event = null;
    public ?string $status = null;
    public ?int $chapterId;

    #[Url]
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
    public $capacity;
    public $registration_required = false;
    public $accounts;
    public array $account_id=[];

    public function mount()
    {
        $this->chapterId = Chapter::where('name', '=', $this->chapter)->firstOrFail()->id;
        $this->accounts = Accounts::where('is_active', 1)->get();
        $this->rows();
    }

    /**
     * Table headers
     */
    public function with(): array
    {
        return [
            'headers' => [['index' => 'title', 'label' => 'Title'], ['index' => 'start_at', 'label' => 'Starts'], ['index' => 'end_at', 'label' => 'Ends'], ['index' => 'status', 'label' => 'Status'], ['index' => 'action', 'label' => 'Action']],
            'rows' => $this->rows(),
        ];
    }

    /**
     * Query rows with filtering + pagination
     */
    public function rows()
    {
        return Events::latest()->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('description', 'like', "%{$this->search}%"))->when($this->chapterId, fn($q) => $q->where('chapter_id', $this->chapterId))->when($this->status, fn($q) => $q->where('status', $this->status))->paginate($this->quantity)->withQueryString();
    }

    public function updatedStatus()
    {
        $this->rows();
    }

    public function ids(): array
    {
        return $this->rows()->pluck('id')->toArray();
    }

    public function selectAll()
    {
        $this->selected = $this->ids();
    }

    public function delete($id)
    {
        $event = Events::findOrFail($id);
        $event->delete();

        $this->toast()->success('Done!', 'Event deleted successfully!')->send();
        $this->dispatch('$refresh');
    }

    public function deleteEvent($id)
    {
        $this->dialog()
            ->error('Are you sure you want to delete this event?')
            ->hook([
                'ok' => [
                    'method' => 'delete',
                    'params' => [$id],
                ],
            ])
            ->send();
    }

    public function changeStatus(int $id, string $status)
    {
        $event = Events::findOrFail($id);
        $event->status = $status;
        $event->save();

        $this->toast()
            ->success('Done!', "Event marked as {$status}.")
            ->send();
        $this->dispatch('$refresh');
    }

    public function loadEvent(int $id)
    {
        $this->event = Events::findOrFail($id);
    }

    public function selectedEvent(int $id)
    {
        $event = Events::find($id);
        if (!$event) {
            abort(404, 'No Such Event Found');
        }
        $this->event = $event;
        $this->account_id = AccountEvent::where('event_id', $this->event->id)->first()->pluck('id')->toArray();
        $this->fill([
            'title' => $event->title,
            'slug' => $event->slug,
            'description' => $event->description,
            'start_at' => $event->start_at?->format('Y-m-d\TH:i'),
            'end_at' => $event->end_at?->format('Y-m-d\TH:i'),
            'timezone' => $event->timezone,
            'location' => $event->location,
            'is_online' => $event->is_online,
            'livestream_url' => $event->livestream_url,
            'status' => $event->status,
            'capacity' => $event->capacity,
            'registration_required' => $event->registration_required,
        ]);
    }

    public function edit()
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('events', 'slug')->ignore($this->event?->id)],
            'description' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'timezone' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_online' => ['boolean'],
            'livestream_url' => ['nullable', 'url', 'max:255'],
            'banner' => ['nullable', 'image', 'max:2048'], // max 2MB
            'status' => ['required', Rule::in(['draft', 'published', 'cancelled', 'archived'])],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'registration_required' => ['boolean'],
        ]);

        $data = [
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

        if ($this->banner) {
            $path = $this->banner->storePublicly('events/banners', 'public');
            $data['banner'] = $path;
        }

        $this->event->update($data);

        $this->toast()->success('Event updated', 'The event details were updated successfully')->send();
        $this->dispatch('$refresh');
        $this->dispatch('Edited');
        $this->dispatch('$closeModal', 'event-edit-modal');
    }

    public function addAccount(){
        foreach($this->account_id as $account){
            AccountEvent::create([
                'account_id'=>$account,
                'event_id'=>$this->event->id
            ]);
        }
        $this->toast()->success('Event updated', 'Account Added to Event  successfully')->send();
        $this->dispatch('$refresh');
        $this->dispatch('Edited');
    }
};
?>
<div>
    <x-fancy-header title="Events" subtitle="View and Manage All Events" :breadcrumbs="[['label' => 'Home', 'url' => route('admin.dashboard', request()->query())], ['label' => 'Events']]" />

    <x-modal id="event-edit-modal" title="Event Management" size="2xl">
        <div x-data="{ openSection: 'edit' }" class="space-y-4">

            <!-- Accordion: Edit Event -->
            <div class="border rounded-lg overflow-hidden">
                <button type="button" @click="openSection = (openSection === 'edit' ? null : 'edit')"
                    class="w-full flex justify-between items-center px-4 py-3 bg-zinc-100 dark:bg-zinc-700 text-left">
                    <span class="font-medium text-gray-800 dark:text-gray-200">✏️ Edit Event Details</span>
                    <p>scroll to bottom to attach an account to the event</p>
                    <svg :class="{ 'rotate-180': openSection==='edit' }" class="w-5 h-5 transform transition-transform"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openSection==='edit'" x-collapse>
                    <form wire:submit.prevent="edit" class="space-y-6 bg-zinc-50 dark:bg-zinc-800 p-6">
                        {{-- Title + Slug --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Title</label>
                                <input wire:model.lazy="title" type="text"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                                @error('title')
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Slug (optional)</label>
                                <input wire:model.lazy="slug" type="text"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                                @error('slug')
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Description</label>
                            <textarea wire:model.lazy="description" rows="4"
                                class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border"></textarea>
                            @error('description')
                                <span class="text-xs text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Start/End --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Start</label>
                                <input wire:model.lazy="start_at" type="datetime-local"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                                @error('start_at')
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">End (optional)</label>
                                <input wire:model.lazy="end_at" type="datetime-local"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                                @error('end_at')
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Timezone / Location --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1">Timezone</label>
                                <input wire:model.lazy="timezone" type="text"
                                    placeholder="e.g. UTC, America/New_York"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                                @error('timezone')
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Location</label>
                                <input wire:model.lazy="location" type="text"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                                @error('location')
                                    <span class="text-xs text-red-400">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Checkboxes --}}
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

                        {{-- Livestream URL --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Livestream URL</label>
                            <input wire:model.lazy="livestream_url" type="url"
                                class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                            @error('livestream_url')
                                <span class="text-xs text-red-400">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Banner --}}
                        <div>
                            <label class="block text-sm font-medium mb-1">Banner (image)</label>
                            <input wire:model="banner" type="file" accept="image/*" />
                            @error('banner')
                                <span class="text-xs text-red-400">{{ $message }}</span>
                            @enderror

                            <div class="mt-3">
                                <template x-if="$wire.banner">
                                    <img :src="$wire.banner ? URL.createObjectURL($wire.banner) : ''"
                                        class="max-h-40 rounded-md" />
                                </template>
                            </div>
                        </div>

                        {{-- Status / Capacity / Submit --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium mb-1">Status</label>
                                <select wire:model="status"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border">
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1">Capacity</label>
                                <input wire:model.lazy="capacity" type="number" min="1"
                                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                            </div>

                            <div class="text-right">
                                <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white">Save
                                    Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Accordion: Add Account -->
            <div class="border rounded-lg overflow-hidden">
                <button type="button" @click="openSection = (openSection === 'account' ? null : 'account')"
                    class="w-full flex justify-between items-center px-4 py-3 bg-zinc-100 dark:bg-zinc-700 text-left">
                    <span class="font-medium text-gray-800 dark:text-gray-200">➕ Add Account</span>
                    <svg :class="{ 'rotate-180': openSection==='account' }"
                        class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="openSection==='account'" x-collapse>
                    <form wire:submit.prevent="addAccount" class="space-y-6 bg-zinc-50 dark:bg-zinc-800 p-6">

                        <!-- Select Existing Account -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Select Existing Account</label>
                            <x-select.styled wire:model="account_id" multiple searchable :options="$accounts
                                ->map(
                                    fn($acc) => [
                                        'label' => $acc->account_name . ' (' . $acc->account_number . ')',
                                        'value' => $acc->id,
                                    ],
                                )
                                ->toArray()" />
                            @error('account_id')
                                <span class="text-xs text-red-400">{{ $message }}</span>
                            @enderror
                        </div>


                        <!-- Submit -->
                        <div class="text-right">
                            <button type="submit"
                                class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white">
                                Add Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </x-modal>


    <x-card class="relative dark:bg-dark-800">
        <x-table :$headers :$rows :filter="['quantity' => 'quantity', 'search' => 'search']" :quantity="[5, 15, 50, 100, 250]" paginate persistent selectable
            wire:model.live="selected">

            <x-slot:header>
                <x-select.native :options="[
                    ['label' => 'Filter by Status', 'value' => null],
                    ['label' => 'Draft', 'value' => 'draft'],
                    ['label' => 'Published', 'value' => 'published'],
                    ['label' => 'Cancelled', 'value' => 'cancelled'],
                    ['label' => 'Archived', 'value' => 'archived'],
                ]" wire:model.live='status' class="mb-4" />
            </x-slot:header>

            @interact('column_action', $row)
                <x-button.circle color="red" icon="trash" wire:click="deleteEvent('{{ $row->id }}')" />

                {{-- @if ($row?->status !== 'published')
                    <x-button wire:click='changeStatus({{ $row->id }}, "published")'>Publish</x-button>
                @endif
                @if ($row?->status !== 'archived')
                    <x-button color="gray" wire:click='changeStatus({{ $row->id }}, "archived")'>Archive</x-button>
                @endif --}}
                <x-button color="blue" icon="pencil"
                    x-on:click="$wire.call('selectedEvent', {{ $row?->id }}).then(() => $modalOpen('event-edit-modal'))">
                    Edit Event
                </x-button>


                <x-button.circle color="green" icon="eye"
                    x-on:click="$modalOpen('event-modal');$wire.call('loadEvent', {{ $row->id }})" />
            @endinteract
        </x-table>
    </x-card>
    @script
        <script>
            Livewire.on('Edited', () => {
                $modalClose('event-edit-modal');
            });
        </script>
    @endscript
</div>
