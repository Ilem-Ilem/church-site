<?php

use App\Models\{Events, EventForm};
use Livewire\Attributes\{Layout, Url};
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.layout')] class extends Component
{
    use WithFileUploads, Interactions;

    #[Url]
    public $event_id;

    public $event;
    public $formData = [];
    public $uploadedFiles = [];

    public function mount()
    {
        if (!$this->event_id) {
            abort(404, 'Event not found');
        }

        $this->event = Events::findOrFail($this->event_id);

        // Check if event requires registration
        if (!$this->event->registration_required) {
            abort(403, 'This event does not require registration');
        }

        // Check if event is published
        if ($this->event->status !== 'published') {
            abort(403, 'This event is not currently accepting registrations');
        }

        // Check if event has passed
        // if ($this->event->start_at->isPast()) {
        //     abort(403, 'Registration for this event has closed');
        // }

        // Check capacity
        if ($this->event->capacity) {
            $registrationCount = EventForm::where('event_id', $this->event_id)->count();
            if ($registrationCount >= $this->event->capacity) {
                abort(403, 'This event has reached maximum capacity');
            }
        }
    }

    public function submit()
    {
        // Build validation rules dynamically based on form schema
        $rules = [];
        $messages = [];

        if ($this->event->form_schema) {
            foreach ($this->event->form_schema as $field) {
                $fieldRules = [];

                if ($field['required'] ?? false) {
                    $fieldRules[] = 'required';
                } else {
                    $fieldRules[] = 'nullable';
                }

                // Add type-specific validation
                switch ($field['type']) {
                    case 'email':
                        $fieldRules[] = 'email';
                        break;
                    case 'number':
                        $fieldRules[] = 'numeric';
                        break;
                    case 'date':
                        $fieldRules[] = 'date';
                        break;
                    case 'file':
                        $fieldRules[] = 'file';
                        $fieldRules[] = 'max:10240'; // 10MB
                        break;
                    case 'phone':
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:20';
                        break;
                    default:
                        $fieldRules[] = 'string';
                        $fieldRules[] = 'max:500';
                }

                $rules['formData.' . $field['name']] = implode('|', $fieldRules);
                $messages['formData.' . $field['name'] . '.required'] = 'The ' . strtolower($field['label']) . ' field is required.';
            }
        }

        $this->validate($rules, $messages);

        try {
            // Handle file uploads
            $processedData = [];
            foreach ($this->formData as $key => $value) {
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    $path = $value->store('event-registrations', 'public');
                    $processedData[$key] = $path;
                } else {
                    $processedData[$key] = $value;
                }
            }

            // Extract name and email for top-level fields
            $name = $processedData['name'] ?? $processedData['full_name'] ?? 'Unknown';
            $email = $processedData['email'] ?? null;
            $phone = $processedData['phone'] ?? null;

            EventForm::create([
                'event_id' => $this->event_id,
                'chapter_id' => $this->event->chapter_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'guests' => 0,
                'form' => $this->event->form_schema,
                'answers' => $processedData,
                'status' => 'confirmed',
            ]);

            session()->flash('success', 'Registration successful! We look forward to seeing you at the event.');

            $this->reset(['formData', 'uploadedFiles']);

            $this->toast()
                ->success('Registration Successful!', 'You have been registered for ' . $this->event->title)
                ->send();

            return redirect()->route('home');

        } catch (\Exception $e) {
            $this->toast()
                ->error('Registration Failed', 'An error occurred. Please try again.')
                ->send();
        }
    }
};

?>

<style>
    .registration-page {
        min-height: 100vh;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding-top: 3rem;
        padding-bottom: 3rem;
    }

    .event-header-card {
        border: none;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .event-banner {
        height: 280px;
        object-fit: cover;
        width: 100%;
    }

    .form-card {
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .event-info-icon {
        font-size: 1.25rem;
        color: #667eea;
        margin-right: 0.5rem;
    }

    .form-control, .form-select {
        border-radius: 0.75rem;
        padding: 0.75rem 1rem;
        border: 1px solid #dee2e6;
    }

    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .btn-register {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 0.75rem;
        padding: 0.875rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .back-link {
        color: #667eea;
        text-decoration: none;
        font-weight: 500;
    }

    .back-link:hover {
        text-decoration: underline;
    }
</style>

<div class="registration-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <!-- Event Header -->
                <div class="card event-header-card mb-4">
                    @if($event->banner)
                        <img src="{{ Storage::url($event->banner) }}" alt="{{ $event->title }}" class="event-banner">
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <h1 class="display-5 fw-bold mb-3">{{ $event->title }}</h1>

                        @if($event->description)
                            <p class="text-muted mb-4 fs-6">{{ $event->description }}</p>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-calendar-event event-info-icon"></i>
                                    <span>{{ $event->start_at->format('F j, Y') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="bi bi-clock event-info-icon"></i>
                                    <span>{{ $event->start_at->format('g:i A') }}</span>
                                </div>
                            </div>

                            @if($event->location)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-geo-alt event-info-icon"></i>
                                        <span>{{ $event->location }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($event->is_online)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center" style="color: #667eea;">
                                        <i class="bi bi-camera-video event-info-icon"></i>
                                        <span class="fw-semibold">Online Event</span>
                                    </div>
                                </div>
                            @endif

                            @if($event->chapter)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-building event-info-icon"></i>
                                        <span>{{ $event->chapter->name }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Registration Form -->
                <div class="card form-card">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h3 fw-bold mb-4">Register for this Event</h2>

                        @if(session('success'))
                            <div class="alert alert-success border-0 rounded-3 mb-4" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <form wire:submit.prevent="submit">
                            @if($event->form_schema)
                                @foreach($event->form_schema as $field)
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">
                                            {{ $field['label'] }}
                                            @if($field['required'] ?? false)
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>

                                        @if($field['description'] ?? false)
                                            <p class="text-muted small mb-2">{{ $field['description'] }}</p>
                                        @endif

                                        @switch($field['type'])
                                            @case('textarea')
                                                <textarea
                                                    wire:model="formData.{{ $field['name'] }}"
                                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                                    class="form-control"
                                                    rows="4"
                                                ></textarea>
                                                @break

                                            @case('select')
                                                <select
                                                    wire:model="formData.{{ $field['name'] }}"
                                                    class="form-select"
                                                >
                                                    <option value="">Select an option...</option>
                                                    @if(isset($field['options']))
                                                        @foreach($field['options'] as $option)
                                                            <option value="{{ $option }}">{{ $option }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @break

                                            @case('radio')
                                                <div>
                                                    @if(isset($field['options']))
                                                        @foreach($field['options'] as $option)
                                                            <div class="form-check mb-2">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="radio"
                                                                    wire:model="formData.{{ $field['name'] }}"
                                                                    value="{{ $option }}"
                                                                    id="radio_{{ $field['name'] }}_{{ $loop->index }}"
                                                                >
                                                                <label class="form-check-label" for="radio_{{ $field['name'] }}_{{ $loop->index }}">
                                                                    {{ $option }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                @break

                                            @case('checkbox')
                                                <div>
                                                    @if(isset($field['options']))
                                                        @foreach($field['options'] as $option)
                                                            <div class="form-check mb-2">
                                                                <input
                                                                    class="form-check-input"
                                                                    type="checkbox"
                                                                    wire:model="formData.{{ $field['name'] }}.{{ $loop->index }}"
                                                                    value="{{ $option }}"
                                                                    id="checkbox_{{ $field['name'] }}_{{ $loop->index }}"
                                                                >
                                                                <label class="form-check-label" for="checkbox_{{ $field['name'] }}_{{ $loop->index }}">
                                                                    {{ $option }}
                                                                </label>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                @break

                                            @case('file')
                                                <input
                                                    type="file"
                                                    wire:model="formData.{{ $field['name'] }}"
                                                    class="form-control"
                                                />
                                                <div class="form-text">Maximum file size: 10MB</div>
                                                @break

                                            @default
                                                <input
                                                    type="{{ $field['type'] }}"
                                                    wire:model="formData.{{ $field['name'] }}"
                                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                                    class="form-control"
                                                />
                                        @endswitch

                                        @error('formData.' . $field['name'])
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info border-0 rounded-3">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    No registration form has been configured for this event.
                                </div>
                            @endif

                            @if($event->form_schema)
                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg btn-register">
                                        <i class="bi bi-check-circle me-2"></i>
                                        Complete Registration
                                    </button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Back Link -->
                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="back-link">
                        <i class="bi bi-arrow-left me-1"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
