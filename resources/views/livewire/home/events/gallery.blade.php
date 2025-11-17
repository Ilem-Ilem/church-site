<?php

use App\Models\Events;
use App\Models\EventGallery;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.layout')] class extends Component
{
    public $event;
    public $event_id;
    public $galleryImages = [];
    public $selectedImage = null;

    public function mount($event)
    {
        // Handle both slug and ID
        if (is_numeric($event)) {
            $this->event = Events::findOrFail($event);
        } else {
            $this->event = Events::where('slug', $event)->firstOrFail();
        }

        // Check if event has started
        if (!$this->event->hasStarted()) {
            abort(403, 'The event gallery will be available once the event starts.');
        }

        // Gallery is now public - anyone can view after event starts
        // No registration or authentication required

        // Load gallery images
        $this->loadGalleryImages();
    }

    public function loadGalleryImages()
    {
        $this->galleryImages = EventGallery::where('event_id', $this->event->id)
            ->orderBy('order_column', 'asc')
            ->get();
    }

    public function openImageModal($imageId)
    {
        $this->selectedImage = EventGallery::findOrFail($imageId);
        $this->dispatch('show-image-modal');
    }

    public function closeImageModal()
    {
        $this->selectedImage = null;
        $this->dispatch('hide-image-modal');
    }
}; ?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-intense: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    .gallery-header {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        padding: 3rem 2rem;
        border-radius: 20px;
        margin-bottom: 3rem;
        text-align: center;
    }

    .gallery-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .gallery-header p {
        color: #6b7280;
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        background: white;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
    }

    .gallery-item:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-intense);
    }

    .gallery-image {
        width: 100%;
        height: 280px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .gallery-item:hover .gallery-image {
        transform: scale(1.08);
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(102, 126, 234, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    .overlay-icon {
        font-size: 3rem;
        color: white;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover .overlay-icon {
        transform: scale(1.2);
    }

    .gallery-info {
        padding: 1.5rem;
    }

    .gallery-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .gallery-date {
        font-size: 0.9rem;
        color: #667eea;
        font-weight: 500;
    }

    /* Modal Styles */
    .image-modal-content {
        background: white;
        border-radius: 20px;
        overflow: hidden;
    }

    .modal-image-container {
        position: relative;
        background: #000;
        max-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-image {
        max-width: 100%;
        max-height: 70vh;
        object-fit: contain;
        padding: 1rem;
    }

    .modal-image-info {
        padding: 2rem;
        text-align: center;
    }

    .modal-image-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .modal-image-date {
        color: #6b7280;
        font-size: 0.95rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #9ca3af;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .gallery-grid {
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .gallery-header h1 {
            font-size: 2rem;
        }

        .gallery-header {
            padding: 2rem 1rem;
        }

        .modal-image-container {
            max-height: 50vh;
        }

        .modal-image {
            max-height: 50vh;
        }
    }
</style>

<div class="container py-5">
    <!-- Gallery Header -->
    <div class="gallery-header">
        <h1>{{ $event->title }} Gallery</h1>
        <p>Moments from our event</p>
        <small class="text-muted d-block mt-2">
            <i class="bi bi-calendar-event"></i> 
            {{ $event->start_at->format('F d, Y') }}
        </small>
    </div>

    <!-- Gallery Grid -->
    @if($galleryImages->count() > 0)
        <div class="gallery-grid">
            @foreach($galleryImages as $image)
                <div class="gallery-item" wire:click="openImageModal({{ $image->id }})">
                    <img 
                        src="{{ Storage::url($image->file_path) }}" 
                        alt="{{ $image->title ?? 'Gallery Image' }}"
                        class="gallery-image"
                        onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                    >
                    <div class="gallery-overlay">
                        <i class="bi bi-zoom-in overlay-icon"></i>
                    </div>
                    @if($image->title)
                        <div class="gallery-info">
                            <p class="gallery-title">{{ $image->title }}</p>
                            <p class="gallery-date">
                                <i class="bi bi-calendar"></i> 
                                {{ $image->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-image"></i>
            </div>
            <h3>No Images Yet</h3>
            <p>The event gallery is currently empty. Check back soon for photos from the event.</p>
        </div>
    @endif

    <!-- Back Button -->
    <div class="text-center mt-5">
        <a href="{{ route('events.index') }}" class="btn btn-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 0.875rem 2rem;">
            <i class="bi bi-arrow-left me-2"></i> Back to Events
        </a>
    </div>
</div>

<!-- Image Modal -->
@if($selectedImage)
    <div wire:ignore.self class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="image-modal-content">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" style="z-index: 1000;" data-bs-dismiss="modal" aria-label="Close"></button>
                
                <div class="modal-image-container">
                    <img 
                        src="{{ Storage::url($selectedImage->file_path) }}" 
                        alt="{{ $selectedImage->title ?? 'Gallery Image' }}"
                        class="modal-image"
                        onerror="this.src='{{ asset('images/placeholder.jpg') }}'"
                    >
                </div>

                <div class="modal-image-info">
                    @if($selectedImage->title)
                        <p class="modal-image-title">{{ $selectedImage->title }}</p>
                    @endif
                    <p class="modal-image-date">
                        <i class="bi bi-calendar"></i> 
                        {{ $selectedImage->created_at->format('F d, Y \\a\\t h:i A') }}
                    </p>
                    <p class="text-muted small mt-3">
                        <i class="bi bi-file-image"></i>
                        {{ number_format($selectedImage->size / 1024 / 1024, 2) }} MB
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-image-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('imageModal'));
            modal.show();
        });

        Livewire.on('hide-image-modal', () => {
            const modalEl = document.getElementById('imageModal');
            if (modalEl) {
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
            }
        });
    });
</script>
