<?php

use App\Models\Transport;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.layout')] class extends Component {
    public ?string $name = null;
    public ?string $phone = null;
    public ?string $pickup_location = null;
    public bool $submitted = false;
    public ?string $message = null;
    public ?string $messageType = null;

    public function submitPickupRequest()
    {
        // Validate inputs
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'pickup_location' => 'required|string|max:1000',
        ]);

        try {
            // Create transport request
            Transport::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'pickup_location' => $validated['pickup_location'],
                'status' => 'pending',
            ]);

            $this->messageType = 'success';
            $this->message = '✓ Pickup request submitted successfully. We will contact you soon!';
            $this->submitted = true;

            // Reset form
            $this->reset(['name', 'phone', 'pickup_location']);
            
            // Dispatch event to close modal
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            $this->messageType = 'error';
            $this->message = 'Error: ' . $e->getMessage();
        }
    }
}; ?>

<div class="d-flex flex-column h-100">
    <main class="flex-shrink-0">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-md custom-navbar fixed-top">
            <div class="container-fluid">
                <img src="Img/doxa.PNG" alt="logo" class="logo">

                <!-- Large screen nav links -->
                <div class="collapse navbar-collapse d-none d-md-flex justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('sermons.index') }}" wire:navigate>Message</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>About</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>Cell</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('events.index') }}" wire:navigate>Event</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>Location</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('believers.academy') }}" wire:navigate>Believers academy</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('transport') }}" wire:navigate>Need a Ride</a></li>
                        <li class="nav-item">
                            @auth
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="nav-link bg-transparent border-0 text-white" style="cursor: pointer;">Logout</button>
                                </form>
                            @else
                                <a class="nav-link" href="{{ route('home.login') }}" wire:navigate>Login</a>
                            @endauth
                        </li>
                    </ul>
                </div>

                <!-- Mobile toggle -->
                <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasNav" aria-controls="offcanvasNav">
                    <i class="bi bi-list text-white" style="font-size: 1.8rem;"></i>
                </button>
            </div>
        </nav>

        <!-- Offcanvas menu -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNav" aria-labelledby="offcanvasNavLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-white" id="offcanvasNavLabel">Doxa Commission Global</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav">
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>Home</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('sermons.index') }}" wire:navigate>Message</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>About</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>Cell</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('events.index') }}" wire:navigate>Event</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}" wire:navigate>Location</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('believers.academy') }}" wire:navigate>Believers academy</a></li>
                    </div>
                    <div class="navcon">
                        <li class="nav-item"><a class="nav-link" href="{{ route('transport') }}" wire:navigate>Need a Ride</a></li>
                    </div>
                    <div class="navcon">
                        @auth
                            <li class="nav-item">
                                <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                                    @csrf
                                    <button type="submit" class="nav-link bg-transparent border-0 text-white w-100 text-start" style="cursor: pointer;">Logout</button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item"><a class="nav-link" href="{{ route('home.login') }}" wire:navigate>Login</a></li>
                        @endauth
                    </div>
                </ul>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="container-fluid p-0 mb-5" style="margin-top: 80px;">
            <section class="hero-section rounded-2xl mx-3"
                style="background-image: url('https://placehold.co/1280x450/4f46e5/ffffff?text=Doxa+Commission+Transportation'); 
                        background-size: cover; background-position: center; height: 450px; 
                        display: flex; align-items: center; justify-content: center; text-align: center; 
                        color: white; position: relative; padding: 1.5rem;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                           background-color: rgba(0, 0, 0, 0.5); border-radius: 1rem;"></div>
                <div style="position: relative; z-index: 1;">
                    <h1 class="display-4 fw-bold">Transportation Services</h1>
                    <p class="lead">We're here to help you get to church safely and on time!</p>
                </div>
            </section>
        </div>

        <main class="container">
            <!-- Section Title -->
            <section class="mb-5">
                <div>
                    <h2 class="section-title mb-4">Pickup Locations</h2>
                    <p class="lead text-center" style="max-width: 900px; margin: 0 auto;">
                        We offer a free transportation service from various pickup points
                        around the city. Please arrive at least 10 minutes before the
                        scheduled time. If you have any questions, feel free to contact the
                        designated person for your location.
                    </p>
                </div>
            </section>

            <!-- Locations Grid -->
            <section class="mb-5">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <!-- Location Card 1 -->
                    <div class="col">
                        <div class="card p-4 rounded-2xl h-100 shadow-sm" style="transition: transform 0.2s ease;">
                            <h3 class="card-title fw-semibold mb-2">UNICAL Maingate</h3>
                            <div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="far fa-clock" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Pickup Time: 8:30 AM</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="fas fa-user-alt" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Contact: John Doe</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="fas fa-phone-alt" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Phone: 0801 234 5678</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Card 2 -->
                    <div class="col">
                        <div class="card p-4 rounded-2xl h-100 shadow-sm" style="transition: transform 0.2s ease;">
                            <h3 class="card-title fw-semibold mb-2">UNICAL Hall 8</h3>
                            <div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="far fa-clock" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Pickup Time: 9:00 AM</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="fas fa-user-alt" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Contact: Jane Smith</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="fas fa-phone-alt" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Phone: 0809 876 5432</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Card 3 -->
                    <div class="col">
                        <div class="card p-4 rounded-2xl h-100 shadow-sm" style="transition: transform 0.2s ease;">
                            <h3 class="card-title fw-semibold mb-2">Itagbor</h3>
                            <div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="far fa-clock" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Pickup Time: 8:45 AM</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="fas fa-user-alt" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Contact: Michael Efe</span>
                                </div>
                                <div style="display: flex; align-items: center; margin-bottom: 0.5rem; font-size: 0.9rem; color: #4b5563;">
                                    <i class="fas fa-phone-alt" style="margin-right: 0.5rem; color: #4f46e5;"></i>
                                    <span>Phone: 0811 223 3445</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Get Involved Section -->
            <section class="mb-5">
                <div class="cta-section bg-light-subtle p-5 rounded-2xl text-center shadow-sm">
                    <h3 class="fw-bold fs-2">Need a Pickup from a Different Location?</h3>
                    <p class="lead text-muted mt-3">
                        We're always looking to expand our pickup routes. Fill out a
                        request form to let us know where you are.
                    </p>
                    <button type="button" class="btn btn-primary btn-lg mt-4 px-5 py-3 rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#pickupModal">
                        Request a Pickup
                    </button>
                </div>
            </section>
        </main>
    </main>

    <!-- Modal for Pickup Request Form -->
    <div class="modal fade" id="pickupModal" tabindex="-1" aria-labelledby="pickupModalLabel" aria-hidden="true"
        @close-modal.window="bootstrap.Modal.getInstance(document.getElementById('pickupModal'))?.hide()">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-2xl">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="pickupModalLabel">Request a Pickup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($message)
                        <div class="alert alert-{{ $messageType === 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
                            role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit="submitPickupRequest">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                wire:model="name" placeholder="John Doe" />
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                wire:model="phone" placeholder="0801 234 5678" />
                            @error('phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="pickup-location" class="form-label">Pickup Location/Address</label>
                            <textarea class="form-control @error('pickup_location') is-invalid @enderror" id="pickup-location"
                                wire:model="pickup_location" rows="4"
                                placeholder="e.g. 123 Main Street, Near the City Hall"></textarea>
                            @error('pickup_location')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary mt-3" wire:loading.attr="disabled">
                                <span wire:loading.remove>Submit Request</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status"
                                        aria-hidden="true"></span>
                                    Submitting...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    @include('partials.footer')

    <style>
    body {
        background-color: #f3f4f6;
        line-height: 1.6;
        color: #374151;
    }

    .rounded-2xl {
        border-radius: 1rem;
    }

    .hero-section {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    </style>
</div>
