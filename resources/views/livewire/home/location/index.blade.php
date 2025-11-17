<?php

use App\Models\Chapter;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.layout')] class extends Component {

    public $startLocation = '';
    public $chapters = [];
    public $selectedChapter = null;

    public function mount()
    {
        // Load all chapters with their location details
        $this->chapters = Chapter::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('name')
            ->get();

        // Default to first chapter if available
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

<style>
    .page-container {
        background: linear-gradient(135deg, #f0f4f8 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding-top: 100px;
        padding-bottom: 60px;
    }

    .location-card {
        max-width: 1100px;
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border: 3px solid #bfdbfe;
    }

    .map-container {
        position: relative;
        width: 100%;
        padding-top: 56.25%; /* 16:9 Aspect Ratio */
        border-radius: 16px;
        overflow: hidden;
    }

    .map-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .chapter-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .chapter-btn {
        padding: 10px 20px;
        border: 2px solid #357be4;
        background: white;
        color: #357be4;
        border-radius: 50px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .chapter-btn:hover {
        background: #357be4;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(53, 123, 228, 0.3);
    }

    .chapter-btn.active {
        background: linear-gradient(to right, #357be4, #294dc0);
        color: white;
        box-shadow: 0 5px 15px rgba(53, 123, 228, 0.4);
    }

    .info-card {
        background: linear-gradient(135deg, #357be4 0%, #294dc0 100%);
        color: white;
        padding: 25px;
        border-radius: 16px;
        margin-top: 25px;
    }

    .info-item {
        display: flex;
        align-items: start;
        margin-bottom: 15px;
    }

    .info-item i {
        font-size: 1.3rem;
        margin-right: 15px;
        margin-top: 3px;
    }

    .btn-directions {
        background: linear-gradient(to right, #357be4, #294dc0);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-directions:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(53, 123, 228, 0.4);
        color: white;
    }
</style>

<div class="page-container">
    <div class="container">
        <div class="location-card p-4 p-md-5 mx-auto">

            <!-- Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary mb-3">
                    <i class="bi bi-geo-alt-fill"></i> Visit Us
                </h1>
                <p class="lead text-secondary">
                    Find a Doxa Commission Global chapter near you
                </p>
            </div>

            <!-- Chapter Selector -->
            @if(count($chapters) > 1)
                <div class="chapter-selector justify-content-center">
                    @foreach($chapters as $chapter)
                        <button
                            wire:click="selectChapter({{ $chapter->id }})"
                            class="chapter-btn {{ $selectedChapter && $selectedChapter->id === $chapter->id ? 'active' : '' }}">
                            <i class="bi bi-building"></i> {{ $chapter->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            @if($selectedChapter)
                <!-- Map Section -->
                <div class="map-container shadow-lg mb-4">
                    <iframe
                        src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q={{ $selectedChapter->latitude }},{{ $selectedChapter->longitude }}&zoom=15"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <!-- Directions Section -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h3 class="h5 fw-bold text-primary mb-3">
                        <i class="bi bi-compass"></i> Get Directions
                    </h3>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <input
                                type="text"
                                wire:model="startLocation"
                                placeholder="Enter your starting location..."
                                class="form-control form-control-lg border-2"
                            />
                        </div>
                        <div class="col-md-4">
                            <a
                                href="{{ $this->getDirectionsUrl() }}"
                                target="_blank"
                                class="btn btn-directions w-100 btn-lg">
                                <i class="bi bi-arrow-right-circle"></i> Get Directions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Location Details -->
                <div class="info-card">
                    <h3 class="h4 fw-bold mb-4">
                        <i class="bi bi-info-circle"></i> {{ $selectedChapter->name }} Details
                    </h3>

                    @if($selectedChapter->address)
                        <div class="info-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <div>
                                <strong>Address:</strong><br>
                                {{ $selectedChapter->address }}
                            </div>
                        </div>
                    @endif

                    @if($selectedChapter->phone)
                        <div class="info-item">
                            <i class="bi bi-telephone-fill"></i>
                            <div>
                                <strong>Phone:</strong><br>
                                <a href="tel:{{ $selectedChapter->phone }}" class="text-white text-decoration-none">
                                    {{ $selectedChapter->phone }}
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($selectedChapter->email)
                        <div class="info-item">
                            <i class="bi bi-envelope-fill"></i>
                            <div>
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ $selectedChapter->email }}" class="text-white text-decoration-none">
                                    {{ $selectedChapter->email }}
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="info-item mb-0">
                        <i class="bi bi-clock-fill"></i>
                        <div>
                            <strong>Service Times:</strong><br>
                            Sunday: 7:00 AM, 8:30 AM, 10:00 AM, 4:00 PM<br>
                            Thursday: 5:30 PM (Glory Experience)
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <a href="{{ route('home') }}" wire:navigate class="btn btn-outline-primary w-100">
                            <i class="bi bi-house"></i> Back to Home
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('events.index') }}" wire:navigate class="btn btn-outline-primary w-100">
                            <i class="bi bi-calendar-event"></i> View Events
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('appointment') }}" wire:navigate class="btn btn-outline-primary w-100">
                            <i class="bi bi-calendar-check"></i> Book Appointment
                        </a>
                    </div>
                </div>

            @else
                <!-- No Chapter Selected -->
                <div class="text-center py-5">
                    <i class="bi bi-geo text-muted" style="font-size: 5rem;"></i>
                    <p class="text-muted mt-3">No location information available</p>
                </div>
            @endif

        </div>
    </div>
</div>
