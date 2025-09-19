<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url, };
use App\Models\{Chapter, AppointmentTeams};

new #[Layout('components.layouts.layout')] class extends Component {
    public ?string $name = null;

    public ?string $email = null;

    #[Url]
    public $chapter;
    public $date;
    public $title;
    public $description;
    public $team_id;
    public $user = null;
    public $appointmentTeams;
    public $selectedTeam;
    public $freeDays = [];
    public $daySelected;
    public $startTime;
    public $endTime;
    

    public function mount()
    {
        $user = auth()->user() ?? null;
        if ($user != null) {
            $this->name  = $user->name;
            $this->email = $user->email;
            $this->user  = $user;
        }

        $this->appointmentTeams = AppointmentTeams::with('team')->when($this->chapter, function ($q) {
            $q->where('chapter_id', Chapter::where('slug', $this->chapter)->first()->id);
        })->get();


    }

    public function updatedselectedTeam()
    {
        $team_appointment_setting = AppointmentTeams::where('team_id', '=', $this->selectedTeam)->first();
        $free_days = json_decode($team_appointment_setting->free_days);
        $freeDays  = [];
        if($free_days != null){
            foreach ($free_days as $key => $value) {
                $freeDays[] = $value->day;
            }
        }else{
            $freeDays = null;
        }
        $this->freeDays = $freeDays ?? ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    }

    public function updateddaySelected()
    {
        $settings = json_decode(AppointmentTeams::where('team_id', '=', $this->selectedTeam)->first()->free_days, true);
        $setting = [];
        if(!$settings == null){
            foreach ($settings as $key => $value) {
                if($value['day'] != $this->daySelected){
                    continue;
                }
                $setting = $value;
            }
            $this->startTime = $setting['start'];
            $this->endTime = $setting['end'];

            // dump($this->startTime, $this->endTime);
        }
    }

    public function save()
    {
        $this->validate([
            'title'=>'required|string',
            'description'=>'required|string|min:225',
            'name'=>'required|string',
            'email'=>'required|email'
        ]);

        
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

    <div class="container card-con">
        <div class="container container-bg shadow-lg rounded-4 p-4 p-sm-5 mb-5 page-content">
            <header class="text-center mb-4">
                <h1 class="fs-2 fs-sm-1 fw-bold text-dark mb-2">Book An Appointment</h1>
                <p class="text-secondary">Schedule your time with the church staff.</p>
            </header>

            <!-- Appointment Booking Form -->
            <div id="booking-section" class="mb-5">
                <h2 class="fs-4 fw-semibold text-dark mb-3">Book a New Appointment</h2>
                <form id="appointmentForm" class="row g-3" wire:submit.prevent='save'>
                    <div class="col-12">
                        <label for="name" class="form-label text-dark">Your Name</label>
                        @if ($name)
                        <input type="text" id="name" name="name" required class="form-control rounded-3" wire:model.live='name' value="{{ $name ?? ''}}" disabled>
                        @else
                        <input type="text" id="name" name="name" required class="form-control rounded-3" wire:model.live='name'>
                        @endif

                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label text-dark">Email Address</label>
                        @if ($email)
                        <input type="email" id="email" name="email" required class="form-control rounded-3" wire:model='email' value="{{ $email }}" disabled>
                        @else
                        <input type="email" id="email" name="email" required class="form-control rounded-3" wire:model='email'>
                        @endif

                    </div>
                    <div class="form-groupmt-4">
                        <label for="reason" class="form-label text-dark">Team</label>

                        <select class="form-control" wire:model.live="selectedTeam" id="selectTeam" name="selectedTeam">
                            <option value="">Select A Team</option>
                            @foreach ($appointmentTeams as $appointment_team)
                            <option value="{{ $appointment_team->team->id }}">{{ $appointment_team->team->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date and Time select bars in a 2-column grid -->
                    <div class="col-12 row g-3">

                        <div class="col-lg-4 col-md-4">
                            <label class="block text-sm font-medium text-gray-300">Day</label>
                            <select class="form-control" wire:model.live='daySelected'>
                                <option value="">Select A Day</option>
                                @foreach ($freeDays as $day)
                                <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                                @endforeach
                            </select>

                        </div>

                        <!-- Start Time -->
                        <div class="col-lg-4 col-md-4">
                            <label class="block text-sm font-medium text-gray-300">Start Time</label>
                            @if($startTime)
                            <input type="time" class="form-control" value="{{ $startTime }}" wire:model='startTime' disabled>
                            @else
                            <input type="time" class="form-control" wire:model='startTime'>
                            @endif

                        </div>

                        <!-- End Time -->
                        <div class="col-lg-4 col-md-4">
                            <label class="block text-sm font-medium text-gray-300">End Time</label>
                            @if($endTime)
                            <input type="time" class="form-control" value="{{ $endTime }}" disabled  wire:model='endTime'>
                            @else
                            <input type="time" class="form-control" wire:model='endTime'>
                            @endif

                        </div>
                    </div>

                    <div class="col-12 form-group mt-3">
                        <label for="title" class="form-label text-dark">Title:</label>
                        <input type="text" wire:model='title' class="form-control w-full">
                    </div>
                    <div class="col-12 mb-4">
                        <label for="reason" class="form-label text-dark">Reason for Appointment</label>
                        <textarea id="reason" name="reason" rows="3" required class="form-control rounded-3" wire:model='description'></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3 fw-semibold" wire:click='save'>Book an
                            Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



</div>
