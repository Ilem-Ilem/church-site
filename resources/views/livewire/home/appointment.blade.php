<?php
/*
    -TODO: Add authentication check to pre-fill user details if logged in.
    -TODO: Implement email confirmation after booking an appointment.
    -TODO: Add notification system for admins on new appointments.
 */
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{Chapter, AppointmentTeams, Appointment};

new #[Layout('components.layouts.layout')] class extends Component {
    public ?string $name = null;
    public ?string $email = null;

    #[Url]
    public $chapter;
    public $date,
        $title,
        $description,
        $team_id,
        $user = null,
        $appointmentTeams,
        $selectedTeam,
        $freeDays = [],
        $daySelected,
        $startTime,
        $endTime,
        $chapters,
        $selectedChapter,
        $currentChapter = null;

    public function mount()
    {
        $user = auth()->user() ?? null;
        if ($user != null) {
            $this->name = $user->name;
            $this->email = $user->email;
            $this->user = $user;
        }

        $this->appointmentTeams = AppointmentTeams::with('team')
            ->when($this->chapter, function ($q) {
                $q->where('chapter_id', Chapter::where('name', e($this->chapter))->first()->id);
            })
            ->get();

        if ($this->chapter != null) {
            $this->currentChapter = Chapter::where('name', $this->chapter)->first();
            if ($this->currentChapter == null) {
                abort(403, 'Invalid Chapter');
            }
            $this->selectedChapter = $this->currentChapter->id;
        }

        $this->chapters = Chapter::all()->toArray();
    }

    public function updatedselectedChapter()
    {
        $this->appointmentTeams = AppointmentTeams::with('team')->where('chapter_id', $this->selectedChapter)->get();

        if ($this->appointmentTeams->isEmpty()) {
            $this->appointmentTeams = 'empty';
        }
    }

    public function updatedselectedTeam()
    {
        $team_appointment_setting = AppointmentTeams::where('team_id', '=', $this->selectedTeam)->first();
        $free_days = json_decode($team_appointment_setting->free_days);
        $freeDays = [];
        if ($free_days != null) {
            foreach ($free_days as $key => $value) {
                $freeDays[] = $value->day;
            }
        } else {
            $freeDays = null;
        }
        $this->freeDays = $freeDays ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }

    public function updateddaySelected()
    {
        $settings = json_decode(AppointmentTeams::where('team_id', '=', $this->selectedTeam)->first()->free_days, true);
        $setting = [];
        if (!$settings == null) {
            foreach ($settings as $key => $value) {
                if ($value['day'] != $this->daySelected) {
                    continue;
                }
                $setting = $value;
            }
            $this->startTime = $setting['start'];
            $this->endTime = $setting['end'];
        }
    }

    public function save()
    {
        $this->validateAppointment();

        $chapter = Chapter::where('name', $this->chapter)->first();

        Appointment::create([
            'title' => $this->title,
            'description' => $this->description,
            'day' => $this->date, // ✅ Now taken directly from calendar
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'team_id' => $this->selectedTeam,
            'chapter_id' => $this->selectedChapter,
            'user_id' => $this->user?->id,
            'username' => $this->name,
            'email' => $this->email,
            'status' => 'pending',
        ]);

        session()->flash('success', 'Appointment booked successfully!');
        $this->resetExcept(['user', 'chapter', 'name', 'email']);
        $this->redirect(route('home'));
    }

    public function validateAppointment()
    {
        $this->validate(
            [
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:20',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'selectedTeam' => 'required|integer',
                'date' => 'required|date', // ✅ validate date from calendar
                'startTime' => 'required',
                'endTime' => 'required',
            ],
            [
                'title.required' => 'Please provide a title for your appointment.',
                'title.max' => 'The title must not be longer than 255 characters.',

                'description.required' => 'Please enter a reason for the appointment.',
                'description.min' => 'The reason must be at least 20 characters long.',

                'name.required' => 'Your name is required.',
                'name.max' => 'Your name must not exceed 255 characters.',

                'email.required' => 'We need your email address to confirm the appointment.',
                'email.email' => 'Please provide a valid email address.',
                'email.max' => 'The email must not exceed 255 characters.',

                'selectedTeam.required' => 'Please select a team for this appointment.',
                'selectedTeam.integer' => 'Invalid team selection.',

                'date.required' => 'Please pick a date for your appointment.',

                'startTime.required' => 'Start time is required.',
                'endTime.required' => 'End time is required.',
            ],
        );
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

            <!-- Appointment Booking Form -->
            <form id="appointmentForm" class="row g-3" wire:submit.prevent='save'>
                <div class="col-12">
                    <label for="name" class="form-label text-dark">Your Name</label>
                    @if ($name)
                        <input type="text" id="name" class="form-control rounded-3" wire:model.live='name'
                            value="{{ $name }}" disabled>
                    @else
                        <input type="text" id="name" class="form-control rounded-3" wire:model.live='name'>
                    @endif
                </div>

                <div class="col-12">
                    <label for="email" class="form-label text-dark">Email Address</label>
                    @if ($email)
                        <input type="email" id="email" class="form-control rounded-3" wire:model='email'
                            value="{{ $email }}" disabled>
                    @else
                        <input type="email" id="email" class="form-control rounded-3" wire:model='email'>
                    @endif
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
                </div>

                @if ($appointmentTeams == 'empty')
                    <div class="alert alert-warning">
                        Sorry, there are no teams available for appointments in this chapter.
                    </div>
                @else
                    <div class="form-group mt-4">
                        <label for="team" class="form-label text-dark">Team</label>
                        <select class="form-control" wire:model.live="selectedTeam">
                            <option value="">Select A Team</option>
                            @foreach ($appointmentTeams as $appointment_team)
                                <option value="{{ $appointment_team->team->id }}">
                                    {{ $appointment_team->team->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date + Time -->
                    <div class="col-12 row g-3">
                        <div 
                        x-data="{
                            selectedDate: null,
                            enabledDays: {{ Js::from($freeDays) }},
                            init() {
                                const dayMap = {
                                    sunday: 0,
                                    monday: 1,
                                    tuesday: 2,
                                    wednesday: 3,
                                    thursday: 4,
                                    friday: 5,
                                    saturday: 6
                                };
                                const numericDays = this.enabledDays.map(d => dayMap[d.toLowerCase()]);
                    
                                flatpickr(this.$refs.datePicker, {
                                    dateFormat: 'Y-m-d',
                                    disable: [(date) => !numericDays.includes(date.getDay())],
                                    onChange: (selectedDates, dateStr) => {
                                        this.selectedDate = dateStr;
                                        $wire.set('date', dateStr);
                                        $wire.set('daySelected', selectedDates[0].toLocaleDateString('en-US', { weekday: 'long' }).toLowerCase());
                                    }
                                });
                            }
                        }" 
                        class="bg-white p-4 rounded-2xl shadow w-96"
                    >
                        <h2 class="text-xl font-bold mb-3 text-gray-800">Pick an Available Date</h2>
                        <input 
                            x-ref="datePicker"
                            x-model="selectedDate"
                            type="text"
                            class="w-full border rounded-lg p-2"
                            placeholder="Select a date"
                        >
                    </div>
                                       
                        <!-- Start Time -->
                        <div class="col-lg-4 col-md-4">
                            <label class="form-label text-dark">Start Time</label>
                            <input type="time" class="form-control" wire:model='startTime'>
                        </div>

                        <!-- End Time -->
                        <div class="col-lg-4 col-md-4">
                            <label class="form-label text-dark">End Time</label>
                            <input type="time" class="form-control" wire:model='endTime'>
                        </div>
                    </div>

                    <div class="col-12 form-group mt-3">
                        <label class="form-label text-dark">Title:</label>
                        <input type="text" wire:model='title' class="form-control">
                    </div>

                    <div class="col-12 mb-4">
                        <label class="form-label text-dark">Reason for Appointment</label>
                        <textarea rows="3" class="form-control" wire:model='description'></textarea>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3 fw-semibold">
                            Book an Appointment
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>
