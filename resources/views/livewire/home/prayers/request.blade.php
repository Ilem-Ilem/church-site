<?php
/**
   TODO: notify missions team when prayer request is sent
 */
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{Chapter};

new #[Layout('components.layouts.layout')] class extends Component {
    public ?string $name = null;
    public ?string $email = null;

    #[Url]
    public $chapter;
    public $date,
        $request,
        $user = null,
        $appointmentTeams,
        $chapters,
        $selectedChapter,
        $currentChapter = null;

    public function mount()
    {
        if ($this->chapter != null) {
            $this->currentChapter = Chapter::where('name', $this->chapter)->first();
            if ($this->currentChapter == null) {
                abort(403, 'Invalid Chapter');
            }
            $this->selectedChapter = $this->currentChapter->id;
        }

        $this->chapters = Chapter::all()->toArray();
    }

    public function save()
    {
        $this->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'request' => 'required|string|max:1000',
            'selectedChapter' => 'required|exists:chapters,id',
        ]);

        $request = new \App\Models\PrayerRequest();
        $request->name = $this->name;
        $request->email = $this->email;
        $request->request = $this->request;
        $request->chapter_id = $this->selectedChapter;
        $request->save();
        session()->flash('message', 'Prayer request submitted successfully.');
        $this->reset(['name', 'email', 'request', 'selectedChapter']);
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
    </style>

    <div class="container card-con">
        <div class="container container-bg shadow-lg rounded-4 p-4 p-sm-5 mb-5 page-content">
            <header class="text-center mb-4">
                <h1 class="fs-2 fw-bold text-dark mb-2">Book An Appointment</h1>
                <p class="text-secondary">Schedule your time with the church staff.</p>
            </header>

            <!-- Prayer Request Form -->
            <form class="row g-3" wire:submit.prevent="save">
                <div class="col-12">
                    <label for="name" class="form-label text-dark">Your Name</label>
                    <input type="text" id="name" class="form-control rounded-3" wire:model.live='name'
                        placeholder="Name">
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                    {{-- <small class="text-muted">Leave Blank to stay anonymous</small> --}}
                </div>

                <div class="col-12">
                    <label for="email" class="form-label text-dark">Email Address</label>
                    <input type="email" id="email" class="form-control rounded-3" wire:model='email'
                        placeholder="Email">
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-4">
                    <label for="chapter" class="form-label text-dark">Pick A Chapter</label>
                    @if ($currentChapter != null)
                        <select class="form-control" wire:model.live="selectedChapter" disabled>
                            <option value="{{ $currentChapter->id }}" selected>{{ $currentChapter->name }}</option>
                        </select>
                    @else
                        <select class="form-control" wire:model.live="selectedChapter">
                            <option value="">Select A Chapter</option>
                            @foreach ($chapters as $chapter)
                                <option value="{{ $chapter['id'] }}">{{ $chapter['name'] }}</option>
                            @endforeach
                        </select>
                    @endif
                    @error('selectedChapter')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="request" class="form-label text-dark mt-4">Your Request</label>
                    <textarea id="request" class="form-control rounded-3" rows="5" wire:model.live='request'></textarea>
                    @error('request')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button class="btn btn-sm btn-primary">Send Request</button>
            </form>
        </div>
    </div>

</div>
