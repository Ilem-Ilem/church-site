<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{SermonSeries, Sermon};
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.layout')] class extends Component {

    #[Url]
    public $id;

    public $series;
    public $sermons;
    public $selectedSermon = null;

    public function mount()
    {
        $this->series = SermonSeries::with(['sermons' => function($q) {
            $q->with('media')->latest('preached_at');
        }])->findOrFail($this->id);

        $this->sermons = $this->series->sermons;
        $this->selectedSermon = $this->sermons->first();
    }

    public function selectSermon($sermonId)
    {
        $this->selectedSermon = Sermon::with('media')->findOrFail($sermonId);
    }

}; ?>

<style>
    .series-detail-container {
        min-height: 100vh;
        background: #f0f2f5;
        padding-top: 85px;
    }

    .series-hero {
        background: linear-gradient(135deg, #357be4 0%, #294dc0 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
    }

    .detail-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px 60px;
    }

    @media (max-width: 968px) {
        .detail-layout {
            grid-template-columns: 1fr;
        }
    }

    .video-player-container {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .video-wrapper {
        position: relative;
        padding-top: 56.25%; /* 16:9 ratio */
        background: #000;
    }

    .video-wrapper video,
    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .sermon-info {
        padding: 25px;
    }

    .sermon-list-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        max-height: 800px;
        overflow-y: auto;
    }

    .sermon-list-header {
        position: sticky;
        top: 0;
        background: white;
        padding: 20px;
        border-bottom: 2px solid #e5e7eb;
        z-index: 10;
    }

    .sermon-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .sermon-item:hover {
        background: #f3f4f6;
    }

    .sermon-item.active {
        background: #eff6ff;
        border-left: 4px solid #357be4;
    }

    .sermon-thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        flex-shrink: 0;
    }

    .sermon-item-info {
        flex: 1;
    }

    .play-icon {
        width: 40px;
        height: 40px;
        background: rgba(53, 123, 228, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sermon-item.active .play-icon {
        background: #357be4;
        color: white;
    }
</style>

<div class="series-detail-container">
    <!-- Series Hero -->
    <div class="series-hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <a href="{{ route('sermons.index') }}" wire:navigate class="btn btn-light btn-sm mb-3">
                        <i class="bi bi-arrow-left"></i> Back to Sermons
                    </a>
                    <h1 class="display-4 fw-bold mb-3">{{ $series->title }}</h1>
                    @if($series->description)
                        <p class="lead">{{ $series->description }}</p>
                    @endif
                    <div class="d-flex gap-3 mt-4">
                        <span class="badge bg-light text-dark px-3 py-2">
                            <i class="bi bi-collection-play"></i> {{ $sermons->count() }} Sermons
                        </span>
                        @if($series->created_at)
                            <span class="badge bg-light text-dark px-3 py-2">
                                <i class="bi bi-calendar"></i> {{ $series->created_at->format('M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
                @if($series->artwork_url)
                    <div class="col-md-4 text-center">
                        <img src="{{ Storage::url($series->artwork_url) }}" alt="{{ $series->title }}"
                             class="img-fluid rounded shadow" style="max-height: 250px;">
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="detail-layout">
        <!-- Video Player & Info -->
        <div>
            @if($selectedSermon)
                <div class="video-player-container mb-4">
                    <div class="video-wrapper">
                        @if($selectedSermon->media && $selectedSermon->media->video_url)
                            @if(str_contains($selectedSermon->media->video_url, 'youtube.com') || str_contains($selectedSermon->media->video_url, 'youtu.be'))
                                @php
                                    $videoId = '';
                                    if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $selectedSermon->media->video_url, $matches)) {
                                        $videoId = $matches[1];
                                    } elseif (preg_match('/youtu\.be\/([^?]+)/', $selectedSermon->media->video_url, $matches)) {
                                        $videoId = $matches[1];
                                    }
                                @endphp
                                @if($videoId)
                                    <iframe src="https://www.youtube.com/embed/{{ $videoId }}"
                                            frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen>
                                    </iframe>
                                @endif
                            @else
                                <video controls controlsList="nodownload">
                                    <source src="{{ $selectedSermon->media->video_url }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            @endif
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-white">
                                <div class="text-center">
                                    <i class="bi bi-play-circle" style="font-size: 4rem;"></i>
                                    <p class="mt-3">No video available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="sermon-info">
                        <h2 class="h4 fw-bold mb-3">{{ $selectedSermon->title }}</h2>

                        @if($selectedSermon->preacher)
                            <p class="text-muted mb-2">
                                <i class="bi bi-person"></i> <strong>Speaker:</strong> {{ $selectedSermon->preacher }}
                            </p>
                        @endif

                        @if($selectedSermon->preached_at)
                            <p class="text-muted mb-2">
                                <i class="bi bi-calendar"></i> {{ $selectedSermon->preached_at->format('F d, Y') }}
                            </p>
                        @endif

                        @if($selectedSermon->scripture_reference)
                            <p class="text-muted mb-3">
                                <i class="bi bi-book"></i> <strong>Scripture:</strong> {{ $selectedSermon->scripture_reference }}
                            </p>
                        @endif

                        @if($selectedSermon->description)
                            <div class="mt-4">
                                <h5 class="fw-bold mb-2">About this sermon</h5>
                                <p class="text-muted">{{ $selectedSermon->description }}</p>
                            </div>
                        @endif

                        <!-- Download Audio -->
                        @if($selectedSermon->media && $selectedSermon->media->audio_url)
                            <div class="mt-4">
                                <a href="{{ $selectedSermon->media->audio_url }}" download
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-download"></i> Download Audio
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sermon List -->
        <div class="sermon-list-container">
            <div class="sermon-list-header">
                <h5 class="fw-bold mb-0">All Sermons in this Series</h5>
                <p class="text-muted small mb-0">{{ $sermons->count() }} sermon(s)</p>
            </div>

            <div>
                @forelse($sermons as $sermon)
                    <div class="sermon-item {{ $selectedSermon && $selectedSermon->id === $sermon->id ? 'active' : '' }}"
                         wire:click="selectSermon({{ $sermon->id }})">

                        @if($sermon->media && $sermon->media->thumbnail_url)
                            <img src="{{ Storage::url($sermon->media->thumbnail_url) }}"
                                 alt="{{ $sermon->title }}"
                                 class="sermon-thumbnail">
                        @else
                            <div class="sermon-thumbnail bg-gradient-to-br from-blue-500 to-purple-600 d-flex align-items-center justify-content-center">
                                <i class="bi bi-play-circle text-white" style="font-size: 2rem;"></i>
                            </div>
                        @endif

                        <div class="sermon-item-info">
                            <h6 class="fw-bold mb-1">{{ $sermon->title }}</h6>
                            <p class="text-muted small mb-0">
                                {{ $sermon->preacher ?? 'Unknown' }} • {{ $sermon->preached_at?->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="play-icon">
                            <i class="bi {{ $selectedSermon && $selectedSermon->id === $sermon->id ? 'bi-pause-fill' : 'bi-play-fill' }}"></i>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-collection-play" style="font-size: 3rem;"></i>
                        <p class="mt-3">No sermons in this series yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
