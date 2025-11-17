<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\SermonSeries;
use Livewire\WithPagination;

new #[Layout('components.layouts.layout')] class extends Component {
    use WithPagination;

    public $search = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function series()
    {
        return SermonSeries::query()
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(12);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
}; ?>

<div>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold">Sermon Series</h1>
                <p class="text-muted">Explore our collection of sermon series</p>
            </div>
            <div class="col-md-4">
                <input type="text" wire:model.live="search" class="form-control rounded-pill" 
                       placeholder="Search series...">
            </div>
        </div>

        <div class="row g-4">
            @forelse($this->series as $serie)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm hover-shadow-lg transition">
                        @if($serie->image)
                            <img src="{{ asset('storage/' . $serie->image) }}" class="card-img-top" alt="{{ $serie->title }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-music-note-beamed text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $serie->title }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($serie->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary">{{ $serie->sermons->count() }} Sermons</span>
                                <a href="{{ route('sermons.index', ['series_id' => $serie->id]) }}" wire:navigate class="btn btn-sm btn-outline-primary">
                                    View Series
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No series found. Try adjusting your search.</p>
                </div>
            @endforelse
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $this->series->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
