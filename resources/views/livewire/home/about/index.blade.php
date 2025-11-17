<?php

use App\Models\{AboutUs, ChurchLeader, Conclave, Chapter};
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.layout')] class extends Component {
    public $aboutUs;
    public $leaders;
    public $conclaves;
    public $selectedConclave = null;

    public function mount()
    {
        // Get the current chapter's about us content
        $chapterName = request()->query('chapter', 'default');
        $chapter = Chapter::where('name', $chapterName)->first();

        if ($chapter) {
            $this->aboutUs = AboutUs::where('chapter_id', $chapter->id)
                ->where('is_active', true)
                ->first();

            $this->leaders = ChurchLeader::where('chapter_id', $chapter->id)
                ->where('is_active', true)
                ->orderBy('order_column')
                ->get();
        }

        $this->conclaves = Conclave::where('is_active', true)->get();
    }

    public function selectConclave($id)
    {
        $this->selectedConclave = Conclave::find($id);
    }
}; ?>

<style>
    :root {
        --primary-color: #1a3d6d;
        --secondary-color: #f4f4f4;
        --accent-color: #78aaf1;
        --text-color: #333;
    }

    .about-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 100px 0 50px;
        color: white;
        text-align: center;
    }

    .about-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
    }

    .icon-card {
        text-align: center;
        padding: 30px;
        border-radius: 10px;
        background: var(--secondary-color);
        transition: all 0.3s ease;
        height: 100%;
    }

    .icon-card:hover {
        background: var(--accent-color);
        color: white;
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .icon-card i {
        font-size: 2.5rem;
        margin-bottom: 15px;
        color: var(--primary-color);
    }

    .icon-card:hover i {
        color: white;
    }

    .timeline {
        border-left: 3px solid var(--accent-color);
        padding-left: 30px;
        margin-left: 20px;
    }

    .timeline-item {
        margin-bottom: 30px;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -38px;
        top: 5px;
        width: 15px;
        height: 15px;
        background: var(--accent-color);
        border-radius: 50%;
        border: 3px solid white;
    }

    .timeline-year {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .leader-card {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .leader-card:hover {
        transform: translateY(-10px);
    }

    .leader-card img {
        width: 100%;
        height: 300px;
        object-fit: cover;
    }

    .leader-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        color: white;
        padding: 20px;
        transform: translateY(60%);
        transition: transform 0.3s ease;
    }

    .leader-card:hover .leader-overlay {
        transform: translateY(0);
    }

    .social-links a {
        color: white;
        font-size: 1.2rem;
        margin: 0 8px;
        transition: color 0.3s ease;
    }

    .social-links a:hover {
        color: var(--accent-color);
    }

    .service-day {
        background: var(--secondary-color);
        border-left: 4px solid var(--accent-color);
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 8px;
    }

    .service-day h4 {
        color: var(--primary-color);
        margin-bottom: 10px;
    }
</style>

<div>
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <h1 data-aos="fade-down">About Us</h1>
            @if($aboutUs)
                <p class="lead" data-aos="fade-up">{{ $aboutUs->title }}</p>
            @else
                <p class="lead" data-aos="fade-up">Welcome to Doxa Church</p>
            @endif
        </div>
    </section>

    <!-- Main About Section -->
    @if($aboutUs)
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6" data-aos="fade-right">
                    @if($aboutUs->hero_image)
                        <img src="{{ Storage::url($aboutUs->hero_image) }}" alt="Church" class="img-fluid rounded shadow-lg">
                    @else
                        <img src="https://via.placeholder.com/600x400?text=Church+Image" alt="Church" class="img-fluid rounded shadow-lg">
                    @endif
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <h2 class="section-title">Who We Are</h2>
                    <div style="max-height: 400px; overflow-y: auto; padding-right: 15px;">
                        <p>{!! nl2br(e($aboutUs->description)) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Mission & Vision Section -->
    @if($aboutUs && ($aboutUs->mission || $aboutUs->vision || $aboutUs->core_values))
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Our Foundation</h2>
            <div class="row g-4">
                @if($aboutUs->mission)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="icon-card">
                        <i class="bi bi-bullseye"></i>
                        <h4>Our Mission</h4>
                        <p>{{ $aboutUs->mission }}</p>
                    </div>
                </div>
                @endif

                @if($aboutUs->vision)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="icon-card">
                        <i class="bi bi-eye"></i>
                        <h4>Our Vision</h4>
                        <p>{{ $aboutUs->vision }}</p>
                    </div>
                </div>
                @endif

                @if($aboutUs->core_values)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="icon-card">
                        <i class="bi bi-heart"></i>
                        <h4>Core Values</h4>
                        <p>{{ $aboutUs->core_values }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    <!-- History Timeline -->
    @if($aboutUs && $aboutUs->history_timeline)
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Our Journey</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="timeline" data-aos="fade-up">
                        @foreach($aboutUs->history_timeline as $event)
                            <div class="timeline-item">
                                <div class="timeline-year">{{ $event['year'] }}</div>
                                <p>{{ $event['event'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif

    <!-- Leadership Section -->
    @if($leaders->count() > 0)
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Our Leadership</h2>
            <div class="row g-4">
                @foreach($leaders as $leader)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="leader-card">
                        @if($leader->photo)
                            <img src="{{ Storage::url($leader->photo) }}" alt="{{ $leader->name }}">
                        @else
                            <img src="https://via.placeholder.com/300x300?text={{ urlencode($leader->name) }}" alt="{{ $leader->name }}">
                        @endif
                        <div class="leader-overlay">
                            <h5 class="mb-1">{{ $leader->name }}</h5>
                            <p class="mb-2">{{ $leader->position }}</p>
                            @if($leader->bio)
                                <p class="small">{{ Str::limit($leader->bio, 100) }}</p>
                            @endif
                            <div class="social-links">
                                @if($leader->facebook_url)
                                    <a href="{{ $leader->facebook_url }}" target="_blank"><i class="bi bi-facebook"></i></a>
                                @endif
                                @if($leader->twitter_url)
                                    <a href="{{ $leader->twitter_url }}" target="_blank"><i class="bi bi-twitter"></i></a>
                                @endif
                                @if($leader->instagram_url)
                                    <a href="{{ $leader->instagram_url }}" target="_blank"><i class="bi bi-instagram"></i></a>
                                @endif
                                @if($leader->linkedin_url)
                                    <a href="{{ $leader->linkedin_url }}" target="_blank"><i class="bi bi-linkedin"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Services Schedule -->
    <section class="py-5">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Service Times</h2>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="service-day" data-aos="fade-up">
                        <h4><i class="bi bi-calendar3 me-2"></i>Sunday Service</h4>
                        <p class="mb-0"><i class="bi bi-clock me-2"></i>First Service: 7:00 AM - 9:00 AM</p>
                        <p class="mb-0"><i class="bi bi-clock me-2"></i>Second Service: 9:30 AM - 11:30 AM</p>
                    </div>

                    <div class="service-day" data-aos="fade-up" data-aos-delay="100">
                        <h4><i class="bi bi-calendar3 me-2"></i>Thursday Service</h4>
                        <p class="mb-0"><i class="bi bi-clock me-2"></i>Bible Study: 6:00 PM - 8:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Conclaves Section -->
    @if($conclaves->count() > 0)
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center mb-5" data-aos="fade-up">Our Conclaves</h2>
            <div class="row g-4">
                @foreach($conclaves as $conclave)
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                    <div class="card h-100 shadow-sm border-0">
                        @if($conclave->image)
                            <img src="{{ Storage::url($conclave->image) }}" class="card-img-top" alt="{{ $conclave->name }}" style="height: 200px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $conclave->name }}</h5>
                            <p class="card-text text-muted">{{ $conclave->location }}</p>
                            @if($conclave->description)
                                <p class="card-text small">{{ Str::limit($conclave->description, 100) }}</p>
                            @endif
                            <button wire:click="selectConclave({{ $conclave->id }})" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#conclaveModal">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Call to Action -->
    <section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div class="container text-center">
            <h2 class="mb-4" data-aos="fade-up">Join Our Community</h2>
            <p class="lead mb-4" data-aos="fade-up" data-aos-delay="100">Experience the love of Christ in a welcoming environment</p>
            <div data-aos="fade-up" data-aos-delay="200">
                <a href="{{ route('home') }}" class="btn btn-light btn-lg me-2">Visit Us</a>
                <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">Get Connected</a>
            </div>
        </div>
    </section>

    <!-- Conclave Modal -->
    @if($selectedConclave)
    <div wire:ignore.self class="modal fade" id="conclaveModal" tabindex="-1" aria-labelledby="conclaveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="conclaveModalLabel">{{ $selectedConclave->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if($selectedConclave->image)
                        <img src="{{ Storage::url($selectedConclave->image) }}" class="img-fluid rounded mb-3" alt="{{ $selectedConclave->name }}">
                    @endif
                    <h6><i class="bi bi-geo-alt me-2"></i>Location</h6>
                    <p>{{ $selectedConclave->location }}</p>

                    @if($selectedConclave->description)
                        <h6><i class="bi bi-info-circle me-2"></i>About</h6>
                        <p>{{ $selectedConclave->description }}</p>
                    @endif

                    @if($selectedConclave->address)
                        <h6><i class="bi bi-house me-2"></i>Address</h6>
                        <p>{{ $selectedConclave->address }}</p>
                    @endif

                    @if($selectedConclave->phone)
                        <h6><i class="bi bi-telephone me-2"></i>Phone</h6>
                        <p><a href="tel:{{ $selectedConclave->phone }}">{{ $selectedConclave->phone }}</a></p>
                    @endif

                    @if($selectedConclave->email)
                        <h6><i class="bi bi-envelope me-2"></i>Email</h6>
                        <p><a href="mailto:{{ $selectedConclave->email }}">{{ $selectedConclave->email }}</a></p>
                    @endif

                    @if($selectedConclave->latitude && $selectedConclave->longitude)
                        <div class="mt-3">
                            <iframe
                                width="100%"
                                height="300"
                                frameborder="0"
                                scrolling="no"
                                marginheight="0"
                                marginwidth="0"
                                src="https://maps.google.com/maps?q={{ $selectedConclave->latitude }},{{ $selectedConclave->longitude }}&output=embed">
                            </iframe>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });
</script>
