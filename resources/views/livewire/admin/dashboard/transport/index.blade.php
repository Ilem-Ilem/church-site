<?php

use App\Models\Transport;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions, WithPagination;

    public ?int $quantity = 10;
    public ?string $search = null;
    public array $selected = [];
    public ?string $status = null;
    public $transport = null;

    #[Url]
    public $chapter;

    // Form properties
    public $name;
    public $phone;
    public $pickup_location;
    public $notes;

    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'name', 'label' => 'Name'],
                ['index' => 'phone', 'label' => 'Phone'],
                ['index' => 'pickup_location', 'label' => 'Pickup Location'],
                ['index' => 'status', 'label' => 'Status'],
                ['index' => 'created_at', 'label' => 'Requested'],
                ['index' => 'action', 'label' => 'Action']
            ],
            'rows' => $this->rows(),
        ];
    }

    public function rows()
    {
        return Transport::latest()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%")
                  ->orWhere('pickup_location', 'like', "%{$this->search}%")
            )
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->paginate($this->quantity)
            ->withQueryString();
    }

    public function ids(): array
    {
        return $this->rows()->pluck('id')->toArray();
    }

    public function selectAll()
    {
        $this->selected = $this->ids();
    }

    public function loadTransport(int $id)
    {
        $this->transport = Transport::findOrFail($id);
        $this->fill([
            'name' => $this->transport->name,
            'phone' => $this->transport->phone,
            'pickup_location' => $this->transport->pickup_location,
            'notes' => $this->transport->notes,
        ]);
    }

    public function saveTransport()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'pickup_location' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->transport->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'pickup_location' => $this->pickup_location,
            'notes' => $this->notes,
        ]);

        $this->toast()->success('Updated', 'Transport request updated successfully')->send();
        $this->dispatch('$refresh');
        $this->dispatch('$closeModal', 'transport-modal');
    }

    public function changeStatus(int $id, string $status)
    {
        $transport = Transport::findOrFail($id);
        $transport->status = $status;
        $transport->processed_at = now();
        $transport->save();

        $this->toast()
            ->success('Done!', "Transport request marked as {$status}.")
            ->send();
        $this->dispatch('$refresh');
    }

    public function delete($id)
    {
        $transport = Transport::findOrFail($id);
        $transport->delete();

        $this->toast()->success('Done!', 'Transport request deleted successfully!')->send();
        $this->dispatch('$refresh');
    }

    public function deleteTransport($id)
    {
        $this->dialog()
            ->error('Are you sure you want to delete this transport request?')
            ->hook([
                'ok' => [
                    'method' => 'delete',
                    'params' => [$id],
                ],
            ])
            ->send();
    }
}; ?>

<div>
    <x-fancy-header title="Transportation Requests" subtitle="Manage pickup location requests" :breadcrumbs="[['label' => 'Home', 'url' => route('admin.dashboard', request()->query())], ['label' => 'Transportation']]" />

    <x-modal id="transport-modal" title="Transport Request Details" size="xl">
        <form wire:submit.prevent="saveTransport" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Name</label>
                    <input wire:model.lazy="name" type="text"
                        class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                    @error('name')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Phone</label>
                    <input wire:model.lazy="phone" type="tel"
                        class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" />
                    @error('phone')
                        <span class="text-xs text-red-400">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Pickup Location</label>
                <textarea wire:model.lazy="pickup_location" rows="3"
                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border"></textarea>
                @error('pickup_location')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Notes</label>
                <textarea wire:model.lazy="notes" rows="3"
                    class="w-full px-3 py-2 rounded-lg bg-white dark:bg-zinc-900 border" placeholder="Additional notes..."></textarea>
                @error('notes')
                    <span class="text-xs text-red-400">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" @click="$modalClose('transport-modal')"
                    class="px-4 py-2 rounded-lg bg-zinc-300 dark:bg-zinc-600 hover:bg-zinc-400 dark:hover:bg-zinc-500 text-black dark:text-white">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white">
                    Save Changes
                </button>
            </div>
        </form>
    </x-modal>

    <x-card class="relative dark:bg-dark-800">
        <x-table :$headers :$rows :filter="['quantity' => 'quantity', 'search' => 'search']" :quantity="[5, 15, 50, 100]" paginate persistent selectable
            wire:model.live="selected">

            <x-slot:header>
                <x-select.native :options="[
                    ['label' => 'Filter by Status', 'value' => null],
                    ['label' => 'Pending', 'value' => 'pending'],
                    ['label' => 'Processed', 'value' => 'processed'],
                    ['label' => 'Rejected', 'value' => 'rejected'],
                ]" wire:model.live='status' class="mb-4" />
            </x-slot:header>

            @interact('column_action', $row)
                <div class="flex items-center space-x-2">
                    <x-button.circle color="green" icon="eye"
                        x-on:click="$modalOpen('transport-modal'); $wire.call('loadTransport', {{ $row->id }})"
                        title="View Details" />

                    @if($row->status === 'pending')
                        <x-button color="blue" sm
                            wire:click="changeStatus({{ $row->id }}, 'processed')">
                            Mark Processed
                        </x-button>

                        <x-button color="orange" sm
                            wire:click="changeStatus({{ $row->id }}, 'rejected')">
                            Reject
                        </x-button>
                    @else
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400">
                            {{ ucfirst($row->status) }}
                        </span>
                    @endif

                    <x-button.circle color="red" icon="trash" wire:click="deleteTransport('{{ $row->id }}')"
                        title="Delete Request" />
                </div>
            @endinteract
        </x-table>
    </x-card>

    @script
        <script>
            Livewire.on('saved', () => {
                $modalClose('transport-modal');
            });
        </script>
    @endscript
</div>
