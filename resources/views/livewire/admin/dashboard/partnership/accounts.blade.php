<?php

use App\Models\{Accounts, AccountEvent};
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
    public ?string $bulkAction = null;

    // For modals
    public bool $showModal = false;
    public ?int $editingId = null;

    // Form fields (all schema fields)
    public string $account_name = '';
    public string $account_number = '';
    public string $bank_name = '';
    public ?string $bank_code = null;
    public ?string $swift_code = null;
    public ?string $iban = null;
    public string $account_type = 'ministry';
    public string $currency = 'USD';
    public string $region = '';
    public ?string $country = null;
    public ?string $description = null;
    public bool $is_active = true;
    public bool $accepts_online_payments = false;
    public bool $accepts_international = false;
    public array $supported_payment_methods = [];
    public ?float $minimum_amount = null;
    public ?float $maximum_amount = null;
    public ?string $special_instructions = null;
    public ?string $contact_person = null;
    public ?string $contact_email = null;
    public ?string $contact_phone = null;

    #[Url(keep:true)]
    public ?string $filterRegion = null;

    /**
     * Table headers
     */
    public function with(): array
    {
        return [
            'headers' => [
                ['index' => 'account_name', 'label' => 'Account Name'],
                ['index' => 'account_number', 'label' => 'Account Number'],
                ['index' => 'bank_name', 'label' => 'Bank Name'],
                ['index' => 'currency', 'label' => 'Currency'],
                ['index' => 'account_type', 'label' => 'Type'],
                ['index' => 'region', 'label' => 'Region'],
                ['index' => 'is_active', 'label' => 'Active'],
                ['index' => 'action'],
            ],
            'rows' => $this->rows(),
        ];
    }

    /**
     * Query rows with filtering + pagination
     */
    public function rows()
    {
        return Accounts::query()
            ->when($this->search, function ($q) {
                $q->where('account_name', 'like', "%{$this->search}%")
                  ->orWhere('account_number', 'like', "%{$this->search}%")
                  ->orWhere('bank_name', 'like', "%{$this->search}%");
            })
            ->when($this->filterRegion, fn($q) => $q->where('region', $this->filterRegion))
            ->paginate($this->quantity);
    }

    public function ids(): array { return $this->rows()->pluck('id')->toArray(); }
    public function selectAll() { $this->selected = $this->ids(); }

    public function applyBulkAction()
    {
        if (!$this->bulkAction || empty($this->selected)) return;

        match ($this->bulkAction) {
            'delete' => Accounts::whereIn('id', $this->selected)->delete(),
            default => null,
        };

        $this->toast()->success('Done!', 'Bulk action applied successfully!')->flash()->send();
        $this->selected   = [];
        $this->bulkAction = null;
    }

    public function create()
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $account = Accounts::findOrFail($id);
        $this->fill($account->toArray());
        $this->supported_payment_methods = $account->supported_payment_methods ?? [];
        $this->editingId = $id;
        $this->showModal = true;
    }

    public function save()
    {
        $data = $this->validate([
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'bank_code' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'account_type' => 'required|string',
            'currency' => 'required|string|size:3',
            'region' => 'required|string|max:255',
            'country' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'accepts_online_payments' => 'boolean',
            'accepts_international' => 'boolean',
            'supported_payment_methods' => 'array',
            'minimum_amount' => 'nullable|numeric',
            'maximum_amount' => 'nullable|numeric',
            'special_instructions' => 'nullable|string',
            'contact_person' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:255',
        ]);

        if ($this->editingId) {
            Accounts::findOrFail($this->editingId)->update($data);
        } else {
            Accounts::create($data);
        }

        $this->toast()->success('Saved!', 'Account saved successfully!')->send();
        $this->showModal = false;
        $this->dispatch('$refresh');
    }

    public function delete($id)
    {
        Accounts::findOrFail($id)->delete();
        $this->toast()->success('Deleted!', 'Account removed successfully!')->send();
        $this->dispatch('$refresh');
    }

    public function deleteAccount($id)
    {
        $this->dialog()
            ->error('Are you sure you want to delete this account?')
            ->hook(['ok' => ['method' => 'delete', 'params' => [$id]]])
            ->send();
    }

    private function resetForm()
    {
        $this->account_name = '';
        $this->account_number = '';
        $this->bank_name = '';
        $this->bank_code = $this->swift_code = $this->iban = null;
        $this->account_type = 'ministry';
        $this->currency = 'USD';
        $this->region = '';
        $this->country = $this->description = null;
        $this->is_active = true;
        $this->accepts_online_payments = false;
        $this->accepts_international = false;
        $this->supported_payment_methods = [];
        $this->minimum_amount = $this->maximum_amount = null;
        $this->special_instructions = null;
        $this->contact_person = $this->contact_email = $this->contact_phone = null;
    }
};
?>
<div x-data="{ open: @entangle('showModal') }">
    <x-fancy-header title="Accounts" subtitle="Manage Accounts" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Accounts']
    ]">
        <x-button color="blue" wire:click="create">Add New</x-button>
    </x-fancy-header>

    <x-card class="relative dark:bg-dark-800">
        <x-table 
            :$headers 
            :$rows 
            :filter="['quantity' => 'quantity', 'search' => 'search']" 
            :quantity="[5, 15, 50, 100, 250]" 
            paginate 
            persistent 
            selectable 
            wire:model.live="selected">

            @interact('column_action', $row)
                <x-button.circle color="red" icon="trash" wire:click="deleteAccount('{{ $row->id }}')" />
                <x-button.circle color="amber" icon="pencil" wire:click="edit('{{ $row->id }}')" />
            @endinteract
        </x-table>
    </x-card>

    <!-- Modal -->
    <div 
        x-show="open"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-60"
        x-cloak
    >
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg w-full max-w-4xl h-[90vh] overflow-y-auto p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100" 
                x-text="$wire.editingId ? 'Edit Account' : 'Create Account'"></h2>

                <form wire:submit.prevent="save" class="space-y-6">
                    <!-- Account Details -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Account Name</span>
                            <x-input wire:model="account_name" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Account Number</span>
                            <x-input wire:model="account_number" />
                        </label>
                    </div>
                
                    <div class="grid grid-cols-3 gap-4">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Bank Name</span>
                            <x-input wire:model="bank_name" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Bank Code</span>
                            <x-input wire:model="bank_code" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">SWIFT Code</span>
                            <x-input wire:model="swift_code" />
                        </label>
                    </div>
                
                    <div class="grid grid-cols-3 gap-4">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">IBAN</span>
                            <x-input wire:model="iban" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Currency</span>
                            <x-input wire:model="currency" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Type</span>
                            <x-select wire:model="account_type">
                                <option value="checking">Checking</option>
                                <option value="savings">Savings</option>
                                <option value="business">Business</option>
                                <option value="ministry">Ministry</option>
                                <option value="donation">Donation</option>
                            </x-select>
                        </label>
                    </div>
                
                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Region</span>
                            <x-input wire:model="region" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Country</span>
                            <x-input wire:model="country" />
                        </label>
                    </div>
                
                    <label class="block">
                        <span class="text-gray-700 dark:text-gray-300">Description</span>
                        <x-textarea wire:model="description" />
                    </label>
                
                    <!-- Toggles -->
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 dark:border-gray-600">
                            <span class="text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="accepts_online_payments" class="rounded">
                            <span class="text-gray-700 dark:text-gray-300">Accepts Online Payments</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model="accepts_international" class="rounded">
                            <span class="text-gray-700 dark:text-gray-300">Accepts International</span>
                        </label>
                    </div>
                
                    <!-- Payment Settings -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Minimum Amount</span>
                            <x-input type="number" step="0.01" wire:model="minimum_amount" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Maximum Amount</span>
                            <x-input type="number" step="0.01" wire:model="maximum_amount" />
                        </label>
                    </div>
                
                    <label class="block">
                        <span class="text-gray-700 dark:text-gray-300">Special Instructions</span>
                        <x-textarea wire:model="special_instructions" />
                    </label>
                
                    <!-- Contact Info -->
                    <div class="grid grid-cols-3 gap-4">
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Contact Person</span>
                            <x-input wire:model="contact_person" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Contact Email</span>
                            <x-input wire:model="contact_email" />
                        </label>
                        <label class="block">
                            <span class="text-gray-700 dark:text-gray-300">Contact Phone</span>
                            <x-input wire:model="contact_phone" />
                        </label>
                    </div>
                
                    <!-- Actions -->
                    <div class="flex justify-end space-x-2 mt-6">
                        <x-button color="gray" @click="open=false">Cancel</x-button>
                        <x-button type="submit" color="blue">Save</x-button>
                    </div>
                </form>
                
        </div>
    </div>
</div>
