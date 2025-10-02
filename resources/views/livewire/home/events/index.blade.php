<?php
//TODO : filter events by filter
//TODO: add advanced filter by date, location
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{Chapter, Events};

new #[Layout('components.layouts.layout')] class extends Component {
    public $chapters;

    public $events;
    #[Url(keep:true)]
    public $sc;

    public function mount()
    {
        $this->chapters = Chapter::all();
        $this->events = Events::with('chapter')->get();
    }

    public function filterChapter($id)
    {
        if ($id != 0) {
            $this->sc = $id;
            $this->events = Events::with('chapter')->where('chapter_id', $id)->get();
        }else{
            $this->sc = '';
            $this->events = Events::with('chapter')->get();
        }
    }
}; ?>

<div>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --dark-gradient: linear-gradient(135deg, #0c0c0c 0%, #1a1a1a 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #1a1a1a;
            --text-secondary: #6b7280;
            --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
            --shadow-intense: 0 20px 60px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            overflow-x: hidden;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }

        .animated-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(248, 250, 252, 0.95);
            backdrop-filter: blur(100px);
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Floating Particles */
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(102, 126, 234, 0.6);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        .particle:nth-child(2) {
            left: 20%;
            animation-delay: -5s;
        }

        .particle:nth-child(3) {
            left: 40%;
            animation-delay: -10s;
        }

        .particle:nth-child(4) {
            left: 60%;
            animation-delay: -15s;
        }

        .particle:nth-child(5) {
            left: 80%;
            animation-delay: -20s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 1;
            }

            90% {
                opacity: 1;
            }

            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Header */
        .hero-section {
            position: relative;
            height: 100vh;
            min-height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.6)),
                url('https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&h=600&fit=crop') center/cover;
            background-attachment: fixed;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8), rgba(118, 75, 162, 0.8));
            mix-blend-mode: overlay;
        }

        .hero-content {
            position: relative;
            z-index: 10;
            max-width: 800px;
            padding: 0 1rem;
        }

        .hero-content h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: slideInUp 1s ease-out;
        }

        .hero-content p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 400;
            margin-bottom: 2.5rem;
            animation: slideInUp 1s ease-out 0.2s both;
        }

        .event-type-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            animation: slideInUp 1s ease-out 0.4s both;
        }

        .event-type-btn {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50px;
            color: white;
            padding: 0.8rem 1.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .event-type-btn:hover,
        .event-type-btn.active {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .event-type-btn.active {
            background: var(--primary-gradient);
            border-color: transparent;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateX(-50%) translateY(0);
            }

            40% {
                transform: translateX(-50%) translateY(-10px);
            }

            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        /* Filter Section */
        .filter-section {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto 4rem;
            position: relative;
            z-index: 100;
            box-shadow: var(--shadow-soft);
        }

        .filter-section .form-select,
        .filter-section .form-control {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 12px;
            padding: 0.8rem 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-section .form-select:focus,
        .filter-section .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        /* Event Cards */
        .events-grid {
            perspective: 1000px;
        }

        .event-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: var(--shadow-soft);
            position: relative;
        }

        .event-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .event-card:hover::before {
            left: 100%;
        }

        .event-card:hover {
            transform: translateY(-20px) rotateX(5deg);
            box-shadow: var(--shadow-intense);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .event-card img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .event-card:hover img {
            transform: scale(1.1);
        }

        .event-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary-gradient);
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 10;
        }

        .event-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .event-meta::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #667eea;
            border-radius: 50%;
        }

        .btn-modern {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-modern:hover::before {
            left: 100%;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        /* CTA Section */
        .cta-section {
            background-color: #232d3b;
            border-radius: 30px;
            padding: 4rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin: 4rem 0;
        }

        .cta-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: rotate 10s linear infinite;
        }

        .cta-section .cta-content {
            position: relative;
            z-index: 10;
        }

        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .cta-section h3 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-section p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }

        .email-form {
            max-width: 500px;
            margin: 0 auto;
            position: relative;
        }

        .email-form input {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 1rem 1.5rem;
            color: white;
            width: 100%;
            padding-right: 140px;
        }

        .email-form input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .email-form .btn-submit {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--secondary-gradient);
            border: none;
            border-radius: 50px;
            padding: 0.8rem 2rem;
            font-weight: 600;
            color: white;
        }

        /* Footer */
        .modern-footer {
            background: var(--dark-gradient);
            color: white;
            padding: 3rem 0;
            position: relative;
        }

        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            text-decoration: none;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-gradient);
            transform: translateY(-5px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-section {
                height: 100vh;
            }

            .event-type-selector {
                gap: 0.5rem;
            }

            .event-type-btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.85rem;
            }

            .filter-section {
                padding: 1.5rem;
                margin: 2rem 1rem 2rem;
            }

            .event-card:hover {
                transform: translateY(-10px);
            }

            .cta-section h3 {
                font-size: 2rem;
            }
        }

        /* Load Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s ease forwards;
        }

        .fade-in:nth-child(1) {
            animation-delay: 0.1s;
        }

        .fade-in:nth-child(2) {
            animation-delay: 0.2s;
        }

        .fade-in:nth-child(3) {
            animation-delay: 0.3s;
        }

        .fade-in:nth-child(4) {
            animation-delay: 0.4s;
        }

        .fade-in:nth-child(5) {
            animation-delay: 0.5s;
        }

        .fade-in:nth-child(6) {
            animation-delay: 0.6s;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>


    <!-- Animated Background -->
    <div class="animated-bg"></div>

    <!-- Floating Particles -->
    <div class="particle" style="left: 10%;"></div>
    <div class="particle" style="left: 20%;"></div>
    <div class="particle" style="left: 40%;"></div>
    <div class="particle" style="left: 60%;"></div>
    <div class="particle" style="left: 80%;"></div>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content " style="margin-top:6rem;">
            <h1>Discover Amazing Events</h1>
            <p>Find and join the perfect events that match your interests and passions</p>
            <p wire:loading wire:target="filterChapter">Loading</p>
            <div class="event-type-selector">
                <button class="event-type-btn @if($sc == 0)
                    active
                @endif" data-type="all" x-on:click="$wire.call('filterChapter', 0)">All
                    Events</button>
                @foreach ($chapters as $chapter)
                    <button class="event-type-btn  @if($sc == $chapter->id)
                    active
                @endif" x-on:click="$wire.call('filterChapter', {{ $chapter->id }})"
                        data-type="{{ $chapter->name }}">{{ $chapter->name }}</button>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <div class="container">
        <div class="filter-section">
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <select class="form-select">
                        <option selected>Event Type</option>
                        <option>Service</option>
                        <option>Conference</option>
                        <option>Outreach</option>
                        <option>Special Program</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <select class="form-select">
                        <option selected>Date Range</option>
                        <option>This Week</option>
                        <option>Next Month</option>
                        <option>All Upcoming</option>
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <input type="text" class="form-control" placeholder="📍 Location">
                </div>
                <div class="col-md-3 col-6">
                    <input type="text" class="form-control" placeholder="🔍 Search events...">
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="container events-grid mb-5">
        <div class="row g-4">
            @foreach ($events as $key => $event)
                <!-- Event Card 1 -->
                <div class="col-lg-4 col-md-6">
                    <div class="card event-card fade-in">
                        <div class="position-relative">
                            <img src="{{ asset($event->banner) }}" class="card-img-top" alt="{{ $event->title }} Image">
                            <span class="event-badge">{{ $event->chapter->name }}</span>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3">{{ $event->title }}</h5>
                            <div class="event-meta">{{ \Carbon\Carbon::parse($event->start_at)->format('M d Y') }} •
                                {{ \Carbon\Carbon::parse($event->start_at)->format('h:i A') }}</div>
                            <div class="event-meta">{{ $event->location ?? 'DOXA COSMOS' }}</div>
                            <div class="event-meta">Dr. Sarah Johnson</div>
                            <p class="card-text mt-3 text-muted">{{ \Str($event->description)->substr(0, 20) }}..</p>
                            <button class="btn btn-modern mt-3 view-details-btn" data-bs-toggle="modal"
                                data-bs-target="#eventDetailModal" data-title="{{ $event->title }}"
                                data-date="{{ \Carbon\Carbon::parse($event->start_at)->format('M d Y • h:i A') }}"
                                data-location="{{ $event->location ?? 'DOXA COSMOS' }}"
                                data-description="{{ $event->description }}" data-image="{{ asset($event->banner) }}">
                                View Details
                            </button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <button class="btn btn-modern btn-lg">Load More Events</button>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="container">
        <div class="cta-section">
            <div class="cta-content">
                <h3>Never Miss an Event</h3>
                <p>Get personalized event recommendations and early access to exclusive gatherings</p>
                <form class="email-form">
                    <input type="email" placeholder="Enter your email address" required>
                    <button type="submit" class="btn-submit">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
    <!-- Event Details Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="eventDetailModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <img id="eventImage" src="" alt="Event Image" class="img-fluid rounded mb-3">
                    <h3 id="eventTitle" class="fw-bold"></h3>
                    <div id="eventDate" class="text-muted mb-2"></div>
                    <div id="eventLocation" class="text-muted mb-3"></div>
                    <p id="eventDescription"></p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-modern" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const detailModal = document.getElementById('eventDetailModal');
            detailModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget; // Button that triggered modal

                // Get data from button
                const title = button.getAttribute('data-title');
                const date = button.getAttribute('data-date');
                const location = button.getAttribute('data-location');
                const description = button.getAttribute('data-description');
                const image = button.getAttribute('data-image');

                // Update modal content
                detailModal.querySelector('#eventTitle').textContent = title;
                detailModal.querySelector('#eventDate').textContent = date;
                detailModal.querySelector('#eventLocation').textContent = location;
                detailModal.querySelector('#eventDescription').textContent = description;
                detailModal.querySelector('#eventImage').src = image;
            });
        });
    </script>


</div>
