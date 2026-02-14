<?php

use App\Models\Transport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.tailwind-layout')] class extends Component {
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $pickup_location = null;
    public bool $submitted = false;
    public ?string $message = null;
    public ?string $messageType = null;

    public function submitPickupRequest()
    {
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'pickup_location' => 'required|string|max:1000',
        ]);

        try {
            Transport::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'pickup_location' => $validated['pickup_location'],
                'status' => 'pending',
            ]);

            $this->messageType = 'success';
            $this->message = 'Pickup request submitted successfully. We will contact you soon!';
            $this->submitted = true;
            $this->reset(['name', 'phone', 'pickup_location']);
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            $this->messageType = 'error';
            $this->message = 'Error: ' . $e->getMessage();
        }
    }
}; ?>

<div x-data="{ openPickup: false }" @close-modal.window="openPickup = false" class="mx-auto w-full max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
    @php
        $pickupPoints = [
            [
                'name' => 'UNICAL Maingate',
                'time' => '8:30 AM',
                'contact' => 'John Doe',
                'phone' => '0801 234 5678',
            ],
            [
                'name' => 'UNICAL Hall 8',
                'time' => '9:00 AM',
                'contact' => 'Jane Smith',
                'phone' => '0809 876 5432',
            ],
            [
                'name' => 'Itagbor',
                'time' => '8:45 AM',
                'contact' => 'Michael Efe',
                'phone' => '0811 223 3445',
            ],
        ];
    @endphp

    <section class="overflow-hidden rounded-3xl border border-blue-100 bg-white shadow-[0_24px_60px_-40px_rgba(37,99,235,0.45)]">
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-6 py-12 text-center text-white sm:px-10">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-100">Need a Ride</p>
            <h1 class="mt-3 text-3xl font-bold sm:text-4xl">Transportation Services</h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-blue-100 sm:text-base">
                We are here to help you get to church safely and on time.
            </p>
        </div>

        <div class="space-y-10 px-6 py-8 sm:px-10">
            <header class="text-center">
                <h2 class="text-2xl font-bold text-slate-900">Pickup Locations</h2>
                <p class="mx-auto mt-2 max-w-3xl text-sm text-slate-600">
                    Please arrive at least 10 minutes before the scheduled pickup time.
                </p>
            </header>

            <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach($pickupPoints as $point)
                    <article class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                        <h3 class="text-lg font-semibold text-slate-900">{{ $point['name'] }}</h3>
                        <ul class="mt-3 space-y-2 text-sm text-slate-600">
                            <li><span class="font-medium text-slate-800">Pickup Time:</span> {{ $point['time'] }}</li>
                            <li><span class="font-medium text-slate-800">Contact:</span> {{ $point['contact'] }}</li>
                            <li><span class="font-medium text-slate-800">Phone:</span> {{ $point['phone'] }}</li>
                        </ul>
                    </article>
                @endforeach
            </div>

            <div class="rounded-2xl border border-blue-100 bg-white p-6 text-center">
                <h3 class="text-2xl font-bold text-slate-900">Need Pickup From Another Location?</h3>
                <p class="mt-2 text-sm text-slate-600">Tell us your location and we will review your request.</p>
                <button type="button" x-on:click="openPickup = true" class="mt-4 inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">
                    Request a Pickup
                </button>
            </div>
        </div>
    </section>

    <div
        x-cloak
        x-show="openPickup"
        x-transition.opacity
        class="fixed inset-0 z-[80] flex items-center justify-center bg-slate-900/60 p-4"
    >
        <div @click.outside="openPickup = false" class="w-full max-w-xl rounded-2xl border border-blue-100 bg-white p-6 shadow-2xl">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900">Request a Pickup</h2>
                    <p class="text-sm text-slate-600">Fill this form and our transport team will contact you.</p>
                </div>
                <button type="button" x-on:click="openPickup = false" class="rounded-lg border border-blue-100 px-3 py-1 text-sm text-slate-600 hover:bg-blue-50">Close</button>
            </div>

            @if ($message)
                <div class="mb-4 rounded-xl border px-4 py-3 text-sm {{ $messageType === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700' }}">
                    {{ $message }}
                </div>
            @endif

            <form wire:submit="submitPickupRequest" class="space-y-4">
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Your Name</label>
                    <input type="text" id="name" wire:model="name" class="w-full rounded-xl border border-blue-100 px-4 py-3 text-sm text-slate-900" placeholder="John Doe">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Phone Number</label>
                    <input type="tel" id="phone" wire:model="phone" class="w-full rounded-xl border border-blue-100 px-4 py-3 text-sm text-slate-900" placeholder="0801 234 5678">
                    @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="pickup-location" class="mb-2 block text-sm font-medium text-slate-700">Pickup Location / Address</label>
                    <textarea id="pickup-location" rows="4" wire:model="pickup_location" class="w-full rounded-xl border border-blue-100 px-4 py-3 text-sm text-slate-900" placeholder="e.g. 123 Main Street, Near City Hall"></textarea>
                    @error('pickup_location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700" wire:loading.attr="disabled">
                    <span wire:loading.remove>Submit Request</span>
                    <span wire:loading>Submitting...</span>
                </button>
            </form>
        </div>
    </div>
</div>
