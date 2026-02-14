<?php

use App\Models\Chapter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.tailwind-layout')] class extends Component {

    public $startLocation = '';
    public $chapters = [];
    public $selectedChapter = null;

    public function mount()
    {
        $this->chapters = Chapter::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get();

        $this->selectedChapter = $this->chapters->first();
    }

    public function selectChapter($chapterId)
    {
        $this->selectedChapter = Chapter::findOrFail($chapterId);
    }

    public function getDirectionsUrl()
    {
        if (!$this->selectedChapter) {
            return '#';
        }

        $destination = urlencode($this->selectedChapter->address ??
            "{$this->selectedChapter->latitude},{$this->selectedChapter->longitude}");

        $origin = $this->startLocation ? urlencode($this->startLocation) : '';

        return "https://www.google.com/maps/dir/?api=1&origin={$origin}&destination={$destination}";
    }

}; ?>

<div class="mx-auto w-full max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-blue-100 bg-white p-6 shadow-[0_24px_60px_-40px_rgba(37,99,235,0.5)] sm:p-8">
        <header class="mb-8 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Visit Us</p>
            <h1 class="mt-3 text-3xl font-bold text-slate-900">Find a Chapter Near You</h1>
            <p class="mt-2 text-sm text-slate-600">Choose a chapter and get turn-by-turn directions.</p>
        </header>

        @if(count($chapters) > 1)
            <div class="mb-8 flex flex-wrap justify-center gap-2">
                @foreach($chapters as $chapter)
                    <button
                        wire:click="selectChapter({{ $chapter->id }})"
                        @class([
                            'rounded-full border px-4 py-2 text-sm font-semibold transition',
                            'border-blue-600 bg-blue-600 text-white' => $selectedChapter && $selectedChapter->id === $chapter->id,
                            'border-blue-200 bg-white text-blue-700 hover:border-blue-300 hover:bg-blue-50' => !$selectedChapter || $selectedChapter->id !== $chapter->id,
                        ])
                    >
                        {{ $chapter->name }}
                    </button>
                @endforeach
            </div>
        @endif

        @if($selectedChapter)
            <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
                <div class="space-y-5">
                    <div class="overflow-hidden rounded-2xl border border-blue-100">
                        <iframe
                            class="h-[340px] w-full"
                            src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q={{ $selectedChapter->latitude }},{{ $selectedChapter->longitude }}&zoom=15"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>

                    <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5">
                        <h2 class="text-lg font-semibold text-slate-900">Get Directions</h2>
                        <div class="mt-4 flex flex-col gap-3 md:flex-row">
                            <input
                                type="text"
                                wire:model="startLocation"
                                placeholder="Enter your starting location..."
                                class="w-full rounded-xl border border-blue-200 bg-white px-4 py-3 text-sm text-slate-900 md:flex-1"
                            />
                            <a
                                href="{{ $this->getDirectionsUrl() }}"
                                target="_blank"
                                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                Get Directions
                            </a>
                        </div>
                    </div>
                </div>

                <aside class="rounded-2xl border border-blue-100 bg-white p-5">
                    <h2 class="text-lg font-semibold text-slate-900">{{ $selectedChapter->name }} Details</h2>
                    <div class="mt-4 space-y-4 text-sm text-slate-600">
                        @if($selectedChapter->address)
                            <div>
                                <p class="font-semibold text-slate-800">Address</p>
                                <p>{{ $selectedChapter->address }}</p>
                            </div>
                        @endif

                        @if($selectedChapter->phone)
                            <div>
                                <p class="font-semibold text-slate-800">Phone</p>
                                <a href="tel:{{ $selectedChapter->phone }}" class="text-blue-700 hover:underline">{{ $selectedChapter->phone }}</a>
                            </div>
                        @endif

                        @if($selectedChapter->email)
                            <div>
                                <p class="font-semibold text-slate-800">Email</p>
                                <a href="mailto:{{ $selectedChapter->email }}" class="text-blue-700 hover:underline">{{ $selectedChapter->email }}</a>
                            </div>
                        @endif

                        <div>
                            <p class="font-semibold text-slate-800">Service Times</p>
                            <p>Sunday: 7:00 AM, 8:30 AM, 10:00 AM, 4:00 PM</p>
                            <p>Thursday: 5:30 PM (Glory Experience)</p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-2 sm:grid-cols-2">
                        <a href="{{ route('events.index') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl border border-blue-200 px-4 py-2.5 text-sm font-medium text-blue-700 hover:bg-blue-50">View Events</a>
                        <a href="{{ route('appointment') }}" wire:navigate class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Book Appointment</a>
                    </div>
                </aside>
            </div>
        @else
            <div class="rounded-2xl border border-dashed border-blue-200 p-10 text-center text-sm text-slate-500">
                No location information available.
            </div>
        @endif
    </section>
</div>
