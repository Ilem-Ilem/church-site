<?php
//TODO: create the notification to the team lead for the registration of  new user
// TODO:create the track my progress page

//TODO: allow student to lay complain and take permission if they will be absent and the reason
//TODO: allow the team lead to accept the permission when needed or reject
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{BeliversAcademy, Chapter, BelieversAcademyTeams, StudentClasses};

new #[Layout('components.layouts.layout')] class extends Component {
    #[Url(keep: true)]
    public $chapter;

    public $user;
    public bool $isRegistered = false;

    public function mount()
    {
        $this->user = auth()->user();
      
        if ($this->user) {
            $this->chapter = Chapter::where('id', $this->user->chapter_id)->first()->name ?? 'No Chapter';

            $student = StudentClasses::where('user_id', $this->user->id)->first();
            if ($student) {
                $this->isRegistered = true;
            }
            $this->student = $student;
        } 
    }
}; ?>

<div>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-con {
            max-width: 800px;
            margin: 100px auto;
        }

        .section-title {
            font-size: 23px;
        }

        .navbar {
            background: linear-gradient(to right, #357be4, #294dc0) !important;
            height: 85px;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .status-box {
            padding: 1.5rem;
            border-radius: 0.5rem;
            border-left: 5px solid;
        }

        .status-box-open {
            border-color: #28a745;
            background-color: #e2f0e7;
        }

        .status-box-closed {
            border-color: #dc3545;
            background-color: #f8d7da;
        }

        .status-box-open .status-text {
            font-weight: bold;
            color: #28a745;
            margin-bottom: 0.5rem;
        }

        .status-box-closed .status-text {
            font-weight: bold;
            color: #dc3545;
            margin-bottom: 0.5rem;
        }

        .link-text a {
            color: #007bff;
            text-decoration: underline;
        }

        .modal-body-scrollable {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="sermon.html">Message</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="about_us.html">About</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="cell.html">Cell</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="event.html">Event</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="map.html">Location</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="belivers.html">Believers academy</a></li>
                </div>
                <div class="navcon">
                    <li class="nav-item"><a class="nav-link" href="transport.html">Need a Ride</a></li>
                </div>

            </ul>
        </div>
    </div>
    <div class="container card-con">
        @if ($isRegistered)
            <div class="card shadow-lg border-0" style="max-width: 28rem; width: 100%;">
                <div class="position-relative">
                    <!-- Inline SVG header -->
                    <svg class="card-img-top d-block w-100" role="img" aria-label="DOXA — Belieers Academy"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 400" preserveAspectRatio="xMidYMid slice"
                        style="height: 200px; display: block; object-fit: cover;">
                        <defs>
                            <linearGradient id="bgGrad" x1="0" x2="1" y1="0" y2="1">
                                <stop offset="0%" stop-color="#0d6efd" />
                                <stop offset="100%" stop-color="#2563eb" />
                            </linearGradient>

                            <!-- subtle overlay gradient for contrast -->
                            <linearGradient id="overlay" x1="0" x2="0" y1="0" y2="1">
                                <stop offset="0%" stop-color="rgba(0,0,0,0.35)" />
                                <stop offset="100%" stop-color="rgba(0,0,0,0.05)" />
                            </linearGradient>
                        </defs>

                        <!-- background -->
                        <rect width="1200" height="400" fill="url(#bgGrad)" />

                        <!-- decorative circles for subtle pattern -->
                        <g opacity="0.08" fill="#fff">
                            <circle cx="1100" cy="50" r="120" />
                            <circle cx="220" cy="80" r="90" />
                            <circle cx="900" cy="280" r="140" />
                        </g>

                        <!-- overlay for better text contrast -->
                        <rect width="1200" height="400" fill="url(#overlay)" />

                        <!-- Centered brand text -->
                        <g transform="translate(0,0)" text-anchor="middle" class="svg-brand-text">
                            <!-- DOXA -->
                            <text x="50%" y="48%" fill="#ffffff" font-size="120" font-weight="800" letter-spacing="2"
                                dominant-baseline="middle">DOXA</text>

                            <!-- Belieers Academy (smaller) -->
                            <text x="50%" y="66%" fill="rgba(255,255,255,0.92)" font-size="36" font-weight="600"
                                letter-spacing="1" dominant-baseline="middle">Belieers Academy</text>
                        </g>
                    </svg>

                    <!-- gradient overlay using absolute div (keeps same style as before) -->
                    <div class="position-absolute top-0 start-0 w-100 h-100"
                        style="background: linear-gradient(to top, rgba(0,0,0,0.15), transparent); pointer-events:none;">
                    </div>
                </div>

                <div class="card-body text-center">
                    <h1 class="card-title fw-bold text-primary">You are registered!</h1>
                    <p class="card-text text-muted mt-3">
                        Welcome to our community! You're all set to explore and enjoy the features
                        of our platform. Click below to access your dashboard and start your journey.
                    </p>
                    <a href="{{ route('home.believers.dashboard', request()->query()) }}" class="btn btn-primary btn-lg w-100 mt-4">
                        View Dashboard
                    </a>
                </div>
            </div>
        @else
            <div class="card p-4 p-md-5">
                <h1 class="mb-3 fw-bold">Believers Academy</h1>
                <p class="lead">Our Believers Class is a foundational course designed for new converts and anyone
                    seeking
                    to
                    deepen their understanding of core Christian doctrines. It's a welcoming environment where you can
                    ask
                    questions, grow in faith, and connect with other believers.</p>

                <p class="mt-4 fw-semibold">In this class, you will learn about:</p>
                <ul class="list-style-type-disc">
                    <li>The fundamentals of salvation and new birth</li>
                    <li>The person and work of the Holy Spirit</li>
                    <li>The importance of prayer and Bible study</li>
                    <li>Understanding the church and fellowship</li>
                    <li>Living a victorious Christian life</li>
                </ul>

                <p class="mt-4">We believe that a strong foundation in these truths is essential for every believer
                    to
                    thrive in their walk with God.</p>
                <p class="link-text mb-0">New classes begin every first Sunday of the month. Click <a
                        href="{{ route('believer_academy.register') }}"
                        class="text-decoration-underline text-primary fw-bold" wire:navigate>here</a> to register.</p>
            </div>
        @endif
    </div>


</div>
