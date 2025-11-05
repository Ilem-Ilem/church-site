<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Sermons;
use App\Models\SermonSeries;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.layout')] class extends Component {
    use WithPagination;

    public $search = '';
    public $selectedSeries = null;

    public function with(): array
    {
        $sermons = Sermons::with(['series', 'media'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->selectedSeries, fn($q) => $q->where('series_id', $this->selectedSeries))
            ->latest('preached_at')
            ->paginate(12);

        $series = SermonSeries::withCount('sermons')->latest()->take(6)->get();
        $latestSermon = Sermons::with(['series', 'media'])->latest('preached_at')->first();

        return [
            'sermons' => $sermons,
            'series' => $series,
            'latestSermon' => $latestSermon,
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedSeries()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedSeries = null;
        $this->resetPage();
    }
};
?>

<style>
    /* Custom styling for the volume slider track and thumb */
    input[type="range"]::-webkit-slider-runnable-track {
        background: #4b5563;
        border-radius: 9999px;
        height: 4px;
    }

    input[type="range"]::-moz-range-track {
        background: #4b5563;
        border-radius: 9999px;
        height: 4px;
    }

    input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        height: 14px;
        width: 14px;
        background-color: #dc3545;
        border-radius: 50%;
        margin-top: -5px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    }

    input[type="range"]::-moz-range-thumb {
        height: 14px;
        width: 14px;
        background-color: #dc3545;
        border-radius: 50%;
        border: none;
    }

    /* Custom styles for the seek bar track and thumb */
    #seek-bar {
        -webkit-appearance: none;
        appearance: none;
        background: transparent;
        cursor: pointer;
        width: 100%;
    }

    #seek-bar::-webkit-slider-runnable-track {
        background: #e5e7eb;
        border-radius: 9999px;
        height: 6px;
    }

    #seek-bar::-moz-range-track {
        background: #e5e7eb;
        border-radius: 9999px;
        height: 6px;
    }

    #seek-bar::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        height: 14px;
        width: 14px;
        background-color: #dc3545;
        border-radius: 50%;
        margin-top: -4px;
        box-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
    }

    #seek-bar::-moz-range-thumb {
        height: 14px;
        width: 14px;
        background-color: #dc3545;
        border-radius: 50%;
        border: none;
    }

    /* PIP Player Styles */
    .audio-player-bar {
        transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        z-index: 1050;
        max-width: 100%;
    }

    .audio-player-bar.hidden {
        transform: translateY(100%);
        opacity: 0;
        pointer-events: none;
    }

    .audio-player-bar.visible {
        transform: translateY(0);
        opacity: 1;
    }

    .audio-player-bar.pip-mode {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 350px;
        max-width: calc(100% - 40px);
        border-radius: 15px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 576px) {
        .audio-player-bar.pip-mode {
            width: calc(100% - 40px);
            right: 20px;
        }
    }

    /* Style for the overlay play button */
    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }

    .sermon-card:hover .play-overlay {
        opacity: 1;
    }

    .btn-hover-white:hover {
        color: #fff !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .sermon-card {
        position: relative;
    }

    .sermon-card * {
        pointer-events: none;
    }

    .sermon-card {
        pointer-events: auto;
    }

    .image-wrapper {
        position: relative;
        overflow: hidden;
    }

    .image-wrapper img {
        transition: transform 0.3s ease;
    }

    .sermon-card:hover .image-wrapper img {
        transform: scale(1.05);
    }
</style>

<div>
    <!-- Header -->
    <header class="bg-dark py-5">
        <div class="container px-5">
            <div class="row gx-5 align-items-center">
                <!-- Text Section -->
                <div class="col-lg-6 col-xl-7 col-xxl-6 order-xl-first order-first">
                    <div class="my-5 text-center text-xl-start">
                        <h1 class="display-5 fw-bolder text-white mb-2">Doxa Messages</h1>
                        <p class="lead fw-normal text-white-50 mb-4">Experience life-transforming messages that
                            bring hope, healing, and breakthrough</p>
                        <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xl-start">
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">
                                    <svg width="24" height="24" fill="currentColor" viewBox="0 0 256 256">
                                        <path
                                            d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z">
                                        </path>
                                    </svg>
                                </span>
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search message, series or speaker"
                                    class="form-control bg-light border-0 p-3">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Featured Card Stack -->
                <div class="col-lg-6 col-xxl-6 d-flex justify-content-center order-xl-last order-last">
                    @if($latestSermon)
                    <div class="card bg-dark text-white border-0 shadow-lg" style="max-width: 400px;">
                        <img src="{{ $latestSermon->image_path ? Storage::url($latestSermon->image_path) : 'https://via.placeholder.com/400x300' }}"
                             class="card-img" alt="{{ $latestSermon->title }}" style="height: 250px; object-fit: cover;">
                        <div class="card-img-overlay d-flex flex-column justify-content-end" style="background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            <span class="badge bg-primary mb-2" style="width: fit-content;">Latest Release</span>
                            <h3 class="h4 mb-2">{{ $latestSermon->title }}</h3>
                            <p class="small mb-2">{{ $latestSermon->series->title ?? 'No Series' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Series Section -->
    <div class="py-5" style="width: 90%; max-width: 1400px; margin: 0 auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Series</h2>
        </div>

        <div class="row align-items-stretch">
            @forelse($series as $item)
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card card-container h-100" wire:click="$set('selectedSeries', {{ $item->id }})" style="cursor: pointer;">
                    <div class="image-wrapper">
                        <img src="{{ $item->image ? Storage::url($item->image) : 'https://via.placeholder.com/400x300' }}"
                             class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                        <div class="play-button-overlay">
                            <i class="fas fa-folder fa-3x text-white"></i>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text">{{ Str::limit($item->description, 100) }}</p>
                        <div class="text-muted small">
                            <i class="fas fa-list"></i> {{ $item->sermons_count }} sermon(s)
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">No series available</p>
            </div>
            @endforelse
        </div>

        @if($selectedSeries)
        <div class="mt-3">
            <button wire:click="clearFilters" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-times"></i> Clear Series Filter
            </button>
        </div>
        @endif
    </div>

    <!-- Sermons Section -->
    <div class="py-5" style="width: 90%; max-width: 1400px; margin: 0 auto;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">{{ $selectedSeries ? 'Filtered Sermons' : 'Latest Sermons' }}</h2>
        </div>

        <div class="row align-items-stretch">
            @forelse($sermons as $sermon)
            <div class="sermon-card col-lg-4 col-md-6 col-sm-12 mb-4 cursor-pointer"
                data-id="{{ $sermon->id }}"
                data-title="{{ $sermon->title }}"
                data-artist="{{ $sermon->series->title ?? 'Unknown' }}"
                data-audio-src="{{ $sermon->media->where('type', 'audio')->first() ? Storage::url($sermon->media->where('type', 'audio')->first()->file_path) : '' }}"
                data-image-src="{{ $sermon->image_path ? Storage::url($sermon->image_path) : 'https://via.placeholder.com/400x300' }}">
                <div class="card card-container h-100">
                    <div class="image-wrapper">
                        <img src="{{ $sermon->image_path ? Storage::url($sermon->image_path) : 'https://via.placeholder.com/400x300' }}"
                             class="card-img-top" alt="{{ $sermon->title }}" style="height: 200px; object-fit: cover;">
                        <div class="play-overlay">
                            @if($sermon->media->where('type', 'audio')->first())
                            <i class="fas fa-play-circle fa-4x text-white"></i>
                            @else
                            <span class="badge bg-warning">No Audio</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $sermon->title }}</h5>
                        <p class="card-text flex-grow-1">{{ Str::limit($sermon->description, 100) }}</p>
                        <div class="card-details mt-auto">
                            <div class="detail-item small text-muted mb-1">
                                <i class="fas fa-folder"></i> <span>{{ $sermon->series->title ?? 'No Series' }}</span>
                            </div>
                            <div class="detail-item small text-muted">
                                <i class="fas fa-calendar-alt"></i> <span>{{ $sermon->preached_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">No sermons found</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $sermons->links() }}
        </div>
    </div>

    <!-- PIP Audio Player -->
    <div id="audio-player-bar"
        class="audio-player-bar fixed-bottom hidden bg-dark text-white py-3 px-4 shadow-lg">

        <!-- Desktop View -->
        <div class="d-none d-md-flex align-items-center justify-content-between">
            <!-- Album Art & Info -->
            <div class="d-flex align-items-center" style="min-width: 250px;">
                <div id="player-album-art" class="bg-secondary rounded me-3"
                    style="width: 3.5rem; height: 3.5rem; flex-shrink: 0;">
                    <img src="" class="w-100 h-100 object-fit-cover rounded" alt="Album Art">
                </div>
                <div class="d-flex flex-column text-truncate">
                    <span id="player-title" class="fw-bold text-truncate">Select a Sermon</span>
                    <span id="player-artist" class="text-secondary small mt-1 text-truncate">--</span>
                </div>
            </div>

            <!-- Controls -->
            <div class="d-flex flex-column align-items-center flex-grow-1 mx-4">
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <button id="prev-btn" class="btn btn-link text-secondary btn-hover-white p-2">
                        <i class="fas fa-step-backward"></i>
                    </button>
                    <button id="play-pause-btn" class="btn btn-link text-white btn-hover-white p-2 mx-3">
                        <i id="play-icon" class="fas fa-play-circle fa-2x"></i>
                        <i id="pause-icon" class="fas fa-pause-circle fa-2x d-none"></i>
                    </button>
                    <button id="next-btn" class="btn btn-link text-secondary btn-hover-white p-2">
                        <i class="fas fa-step-forward"></i>
                    </button>
                </div>
                <div class="d-flex align-items-center w-100">
                    <span id="current-time" class="small text-secondary me-2" style="min-width: 45px;">0:00</span>
                    <input id="seek-bar" type="range" min="0" max="100" value="0" step="0.1" class="form-range flex-grow-1">
                    <span id="duration" class="small text-secondary ms-2" style="min-width: 45px;">0:00</span>
                </div>
            </div>

            <!-- Volume & Actions -->
            <div class="d-flex align-items-center" style="min-width: 200px; justify-content: flex-end;">
                <div class="d-flex align-items-center me-3">
                    <i class="fas fa-volume-up text-secondary me-2"></i>
                    <input id="volume-slider" type="range" min="0" max="1" step="0.01" value="1" class="form-range" style="width: 100px;">
                </div>
                <button id="pip-toggle-btn" class="btn btn-link text-secondary btn-hover-white p-2" title="Picture-in-Picture">
                    <i class="fas fa-compress-alt"></i>
                </button>
                <button id="close-player-btn" class="btn btn-link text-secondary btn-hover-white p-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="d-md-none">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center flex-grow-1 me-2">
                    <div id="player-album-art-mobile" class="bg-secondary rounded me-2"
                        style="width: 2.5rem; height: 2.5rem; flex-shrink: 0;">
                        <img src="" class="w-100 h-100 object-fit-cover rounded" alt="Album Art">
                    </div>
                    <div class="d-flex flex-column text-truncate">
                        <span id="player-title-mobile" class="fw-bold small text-truncate">Select a Sermon</span>
                        <span id="player-artist-mobile" class="text-secondary" style="font-size: 0.75rem;">--</span>
                    </div>
                </div>
                <button id="close-player-btn-mobile" class="btn btn-link text-secondary btn-hover-white p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="d-flex justify-content-center align-items-center mb-2">
                <button id="prev-btn-mobile" class="btn btn-link text-secondary btn-hover-white p-2">
                    <i class="fas fa-step-backward"></i>
                </button>
                <button id="play-pause-btn-mobile" class="btn btn-link text-white btn-hover-white p-2 mx-4">
                    <i id="play-icon-mobile" class="fas fa-play-circle fa-3x"></i>
                    <i id="pause-icon-mobile" class="fas fa-pause-circle fa-3x d-none"></i>
                </button>
                <button id="next-btn-mobile" class="btn btn-link text-secondary btn-hover-white p-2">
                    <i class="fas fa-step-forward"></i>
                </button>
            </div>

            <div class="d-flex align-items-center mb-2">
                <span id="current-time-mobile" class="small text-secondary me-2" style="min-width: 40px; font-size: 0.75rem;">0:00</span>
                <input id="seek-bar-mobile" type="range" min="0" max="100" value="0" step="0.1" class="form-range flex-grow-1">
                <span id="duration-mobile" class="small text-secondary ms-2" style="min-width: 40px; font-size: 0.75rem;">0:00</span>
            </div>

            <div class="d-flex align-items-center justify-content-center">
                <i class="fas fa-volume-up text-secondary me-2 small"></i>
                <input id="volume-slider-mobile" type="range" min="0" max="1" step="0.01" value="1" class="form-range" style="width: 120px;">
                <button id="pip-toggle-btn-mobile" class="btn btn-link text-secondary btn-hover-white p-2 ms-2" title="Picture-in-Picture">
                    <i class="fas fa-compress-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <audio id="player-audio" src="" preload="metadata"></audio>

    <script>
        // Use immediate execution and also listen for Livewire load
        (function() {
            let initialized = false;

            function initAudioPlayer() {
                if (initialized) {
                    console.log('Audio player already initialized, skipping...');
                    return;
                }
                console.log('Initializing audio player...');
                initialized = true;

                // Get all relevant DOM elements
                const audioPlayer = document.getElementById('player-audio');
                const audioPlayerBar = document.getElementById('audio-player-bar');

                if (!audioPlayer || !audioPlayerBar) {
                    console.error('Audio player elements not found!');
                    return;
                }

                // Add error handling for audio loading
                audioPlayer.addEventListener('error', function(e) {
                    console.error('Audio loading error:', e);
                    console.error('Audio source:', audioPlayer.src);
                    console.error('Error code:', audioPlayer.error ? audioPlayer.error.code : 'unknown');
                });


                // Desktop elements
            const playPauseBtn = document.getElementById('play-pause-btn');
            const playIcon = document.getElementById('play-icon');
            const pauseIcon = document.getElementById('pause-icon');
            const playerTitle = document.getElementById('player-title');
            const playerArtist = document.getElementById('player-artist');
            const playerAlbumArt = document.getElementById('player-album-art').querySelector('img');
            const volumeSlider = document.getElementById('volume-slider');
            const currentTimeEl = document.getElementById('current-time');
            const durationEl = document.getElementById('duration');
            const seekBar = document.getElementById('seek-bar');
            const closePlayerBtn = document.getElementById('close-player-btn');
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const pipToggleBtn = document.getElementById('pip-toggle-btn');

            // Mobile elements
            const playPauseBtnMobile = document.getElementById('play-pause-btn-mobile');
            const playIconMobile = document.getElementById('play-icon-mobile');
            const pauseIconMobile = document.getElementById('pause-icon-mobile');
            const playerTitleMobile = document.getElementById('player-title-mobile');
            const playerArtistMobile = document.getElementById('player-artist-mobile');
            const playerAlbumArtMobile = document.getElementById('player-album-art-mobile').querySelector('img');
            const volumeSliderMobile = document.getElementById('volume-slider-mobile');
            const currentTimeElMobile = document.getElementById('current-time-mobile');
            const durationElMobile = document.getElementById('duration-mobile');
            const seekBarMobile = document.getElementById('seek-bar-mobile');
            const closePlayerBtnMobile = document.getElementById('close-player-btn-mobile');
            const prevBtnMobile = document.getElementById('prev-btn-mobile');
            const nextBtnMobile = document.getElementById('next-btn-mobile');
            const pipToggleBtnMobile = document.getElementById('pip-toggle-btn-mobile');

            const sermonCards = document.querySelectorAll('.sermon-card');
            console.log('Found sermon cards:', sermonCards.length);

            let currentSermonIndex = -1;
            let isPipMode = false;

            if (sermonCards.length === 0) {
                console.warn('No sermon cards found on the page');
            }

            // Function to format time in minutes:seconds
            function formatTime(seconds) {
                if (isNaN(seconds)) return '0:00';
                const min = Math.floor(seconds / 60);
                const sec = Math.floor(seconds % 60);
                return `${min}:${sec < 10 ? '0' : ''}${sec}`;
            }

            // Update both desktop and mobile displays
            function updateTimeDisplays() {
                const currentTime = formatTime(audioPlayer.currentTime);
                const duration = formatTime(audioPlayer.duration);

                currentTimeEl.textContent = currentTime;
                durationEl.textContent = duration;
                currentTimeElMobile.textContent = currentTime;
                durationElMobile.textContent = duration;
            }

            function updateSeekBars() {
                if (!isSeeking) {
                    const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
                    seekBar.value = progress;
                    seekBarMobile.value = progress;
                }
            }

            // Play/Pause functionality (desktop and mobile)
            function togglePlayPause() {
                if (audioPlayer.paused) {
                    if (audioPlayer.src) {
                        audioPlayer.play();
                    }
                } else {
                    audioPlayer.pause();
                }
            }

            playPauseBtn.addEventListener('click', togglePlayPause);
            playPauseBtnMobile.addEventListener('click', togglePlayPause);

            // Update icons on play/pause events
            audioPlayer.addEventListener('play', () => {
                playIcon.classList.add('d-none');
                pauseIcon.classList.remove('d-none');
                playIconMobile.classList.add('d-none');
                pauseIconMobile.classList.remove('d-none');
            });

            audioPlayer.addEventListener('pause', () => {
                playIcon.classList.remove('d-none');
                pauseIcon.classList.add('d-none');
                playIconMobile.classList.remove('d-none');
                pauseIconMobile.classList.add('d-none');
            });

            // Update the current time display and seek bar
            audioPlayer.addEventListener('timeupdate', () => {
                updateTimeDisplays();
                updateSeekBars();
            });

            // Update the total duration when the audio loads metadata
            audioPlayer.addEventListener('loadedmetadata', () => {
                updateTimeDisplays();
                seekBar.max = 100;
                seekBarMobile.max = 100;
            });

            // Also handle duration change event for better compatibility
            audioPlayer.addEventListener('durationchange', () => {
                updateTimeDisplays();
            });

            // Click event listeners for sermon cards
            sermonCards.forEach((card, index) => {
                card.addEventListener('click', (e) => {
                    try {
                        const audioSrc = card.dataset.audioSrc;

                        if (!audioSrc || audioSrc.trim() === '') {
                            console.warn('No audio source for this sermon');
                            return;
                        }

                        const title = card.dataset.title;
                        const artist = card.dataset.artist;
                        const imageSrc = card.dataset.imageSrc;

                        // Show the audio player bar
                        audioPlayerBar.classList.remove('hidden');
                        audioPlayerBar.classList.add('visible');

                        // Update player info (both desktop and mobile)
                        playerTitle.textContent = title;
                        playerArtist.textContent = artist;
                        playerAlbumArt.src = imageSrc;

                        playerTitleMobile.textContent = title;
                        playerArtistMobile.textContent = artist;
                        playerAlbumArtMobile.src = imageSrc;

                        // Update audio source and play
                        audioPlayer.src = audioSrc;
                        audioPlayer.load(); // Explicitly load the audio

                        // Play with error handling (silence AbortError as it's expected when switching tracks)
                        audioPlayer.play().catch(error => {
                            if (error.name !== 'AbortError') {
                                console.error('Playback failed:', error);
                            }
                        });

                        currentSermonIndex = index;
                    } catch (error) {
                        console.error('Error in click handler:', error);
                    }
                });
            });

            // Next button functionality
            function playNext() {
                if (currentSermonIndex !== -1 && currentSermonIndex < sermonCards.length - 1) {
                    sermonCards[currentSermonIndex + 1].click();
                } else if (currentSermonIndex === sermonCards.length - 1) {
                    sermonCards[0].click();
                }
            }

            nextBtn.addEventListener('click', playNext);
            nextBtnMobile.addEventListener('click', playNext);

            // Previous button functionality
            function playPrev() {
                if (currentSermonIndex > 0) {
                    sermonCards[currentSermonIndex - 1].click();
                } else if (currentSermonIndex === 0) {
                    sermonCards[sermonCards.length - 1].click();
                }
            }

            prevBtn.addEventListener('click', playPrev);
            prevBtnMobile.addEventListener('click', playPrev);

            // Volume slider functionality (sync both)
            volumeSlider.addEventListener('input', (event) => {
                audioPlayer.volume = event.target.value;
                volumeSliderMobile.value = event.target.value;
            });

            volumeSliderMobile.addEventListener('input', (event) => {
                audioPlayer.volume = event.target.value;
                volumeSlider.value = event.target.value;
            });

            // Close button functionality
            function closePlayer() {
                audioPlayer.pause();
                audioPlayerBar.classList.remove('visible', 'pip-mode');
                audioPlayerBar.classList.add('hidden');
                isPipMode = false;
            }

            closePlayerBtn.addEventListener('click', closePlayer);
            closePlayerBtnMobile.addEventListener('click', closePlayer);

            // PIP Toggle function
            function togglePipMode() {
                isPipMode = !isPipMode;
                if (isPipMode) {
                    audioPlayerBar.classList.add('pip-mode');
                    pipToggleBtn.innerHTML = '<i class="fas fa-expand-alt"></i>';
                    if (pipToggleBtnMobile) {
                        pipToggleBtnMobile.innerHTML = '<i class="fas fa-expand-alt"></i>';
                    }
                } else {
                    audioPlayerBar.classList.remove('pip-mode');
                    pipToggleBtn.innerHTML = '<i class="fas fa-compress-alt"></i>';
                    if (pipToggleBtnMobile) {
                        pipToggleBtnMobile.innerHTML = '<i class="fas fa-compress-alt"></i>';
                    }
                }
            }

            // PIP Toggle
            pipToggleBtn.addEventListener('click', togglePipMode);
            if (pipToggleBtnMobile) {
                pipToggleBtnMobile.addEventListener('click', togglePipMode);
            }

            // Seek bar functionality
            let isSeeking = false;

            function setupSeekBar(seekBarElement) {
                seekBarElement.addEventListener('mousedown', () => {
                    isSeeking = true;
                });

                seekBarElement.addEventListener('touchstart', () => {
                    isSeeking = true;
                });

                seekBarElement.addEventListener('mouseup', () => {
                    isSeeking = false;
                });

                seekBarElement.addEventListener('touchend', () => {
                    isSeeking = false;
                });

                seekBarElement.addEventListener('input', () => {
                    const time = (seekBarElement.value / 100) * audioPlayer.duration;
                    audioPlayer.currentTime = time;
                    // Sync the other seek bar
                    if (seekBarElement === seekBar) {
                        seekBarMobile.value = seekBarElement.value;
                    } else {
                        seekBar.value = seekBarElement.value;
                    }
                });
            }

                setupSeekBar(seekBar);
                setupSeekBar(seekBarMobile);
            }

            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initAudioPlayer);
            } else {
                initAudioPlayer();
            }

            // Also reinitialize after Livewire navigation
            document.addEventListener('livewire:navigated', initAudioPlayer);
        })();
    </script>
</div>
