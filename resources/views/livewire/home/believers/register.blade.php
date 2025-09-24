<?php
//TODO: work on the spinner on the page
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{BeliversAcademy, Chapter, BelieversAcademyTeams, User, StudentClasses};
use Carbon\Carbon;

new #[Layout('components.layouts.layout')] class extends Component {
    public $academy;
    public $academyTeam;
    public $chapters;
    public $selectedChapter;
    public $statusMessage;
    public $statusType; // 'error' | 'success' | 'countdown'
    public $countdown;
    public $errorMessages = [];

    #[Url(keep: true)]
    public $chapter;

    public ?string $name = null;
    public ?string $email = null;
    public $number;
    public $howDidYouKnowAboutUs;
    public $interest;
    public $chapterId;
    public ?User $user = null;

    public function mount()
    {
        $user = Auth()->user();

        if ($user) {
            $student = StudentClasses::where('user_id', $user->id)->first();
            if($student) {
                session()->flash('info', 'You are already registered for the Believers Academy.');
                $this->redirect(route('home.believers.dashboard', request()->query()));
            }
            $this->name = $user->name;
            $this->email = $user->email;

            $this->user = $user;

        }

        $this->chapters = Chapter::all();
    }

    public function updatedSelectedChapter()
    {
        // Reset
        $this->statusMessage = null;
        $this->statusType = null;
        $this->academyTeam = null;
        $this->academy = null;

        // 1. Check if a team exists4
        $this->academyTeam = BelieversAcademyTeams::where('chapter_id', $this->selectedChapter)->first();

        if (!$this->academyTeam) {
            $this->statusType = 'error';
            $this->statusMessage = 'No team available for this chapter. Registration cannot proceed.';
            return;
        }

        // 2. Check if Academy is open
        $this->academy = BeliversAcademy::where('chapter_id', $this->selectedChapter)->first();

        if (!$this->academy) {
            $this->statusType = 'error';
            $this->statusMessage = 'Believers Academy is not available for this chapter.';
            return;
        }

        $now = Carbon::now();
        $startDate = Carbon::parse($this->academy->start_at);

        if ($now->lt($startDate)) {
            $this->statusType = 'countdown';
            $this->countdown = $startDate->toDateString();
            $this->statusMessage = 'Registration will open soon. Starts in ' . $this->countdown;
            return;
        }

        if ($this->academy->status == 'closed') {
            $this->statusType = 'error';
            $this->statusMessage = 'Registration is closed for this academy.';
            return;
        }

        $this->statusType = 'success';
        $this->statusMessage = 'You can now proceed with registration.';
    }

    public function register()
    {
        // Ensure registration is currently allowed for the selected chapter
        if ($this->statusType !== 'success') {
            session()->flash('error', 'Registration is not open for the selected chapter.');
            return; // prevent invalid registration
        }

        // Validation rules and clean messages
        $phoneRegex = '/^\+?[0-9\s\-\(\)]{7,20}$/';

        $rules = [
            'selectedChapter' => ['required'],
            'howDidYouKnowAboutUs' => ['required'],
            'interest' => ['nullable', 'string', 'max:500'],
            'number' => ['required', "regex:$phoneRegex"],
        ];

        if (!$this->user) {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'min:3'],
                'email' => ['required', 'email', 'exists:users,email'],
            ]);
        }

        $messages = [
            'name.required' => 'Full name is required.',
            'name.min' => 'Full name must be at least 3 characters.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Enter a valid email address.',
            'email.exists' => 'No account found for this email. Please sign in to continue.',
            'number.required' => 'Phone number is required.',
            'number.regex' => 'Enter a valid phone number.',
            'selectedChapter.required' => 'Please select a chapter.',
            'howDidYouKnowAboutUs.required' => 'Please tell us how you heard about us.',
            'interest.max' => 'Your interest must not exceed 500 characters.',
        ];

        $this->validate($rules, $messages);

        // Resolve the user (either authenticated or by provided email)
        if ($this->user) {
            $user = $this->user;
        } else {
            $user = User::where('email', $this->email)->first();
            if (!$user) {
                $this->errorMessages[] = 'The provided email was not found. Please <a href=' . route('login') . ' wire:navigate> sign in</a> to continue.';
                return;
            }
        }

        // Prevent duplicate enrollment
        if (StudentClasses::where('user_id', $user->id)->exists()) {
            $this->errorMessages[] = 'You are already registered for the Believers Academy.';
            return;
        }

        StudentClasses::create([
            'user_id' => $user->id,
            'class_completed' => json_encode([]),
            'status' => 'started',
            'cert' => null,
            'interest' => $this->interest,
            'how_did_you_know_about_us' => $this->howDidYouKnowAboutUs,
            'phone' => $this->number,
        ]);
        $this->reset(['name', 'email', 'number', 'howDidYouKnowAboutUs', 'interest', 'selectedChapter', 'statusMessage', 'statusType', 'academy', 'academyTeam']);
        $this->errorMessages = [];
        session()->flash('success', 'Registration successful!');
        $this->redirect(route('home', request()->query()));
    }
};
?>

<div>
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-con {
            /* max-width: 800px; */
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

        .form-holder {
            margin-top: 8rem;
            display: flex;
            justify-content: center;
            padding: 6px;
        }

        .alert {
            padding: 12px;
            border-radius: 6px;
            margin-top: 10px;
            font-weight: bold;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #842029;
        }

        .alert-success {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #664d03;
        }
    </style>
    <div class="container mt-4">
        <div class="form-holder">
            <div id="registrationModal">
                <div class="dialog">
                    <div class="content">
                        <div class="header">
                            <h5 class="section-title">Believers Academy Registration</h5>
                        </div>
                        @if (!empty($errorMessages) && is_array($errorMessages))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h5 class="alert-heading">Please fix the following:</h5>
                                <ul class="mb-0">
                                    @foreach ($errorMessages as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        @if ($chapters->isEmpty())
                            <div>
                                <p class="text-center text-danger">
                                    There are no chapters to enroll in the Believers Academy.
                                </p>
                            </div>
                        @else
                            <div class="body body-scrollable">
                                <p>Please fill out the form below to register for our upcoming Believers Academy.</p>
                                <form wire:submit.prevent="register">
                                    <div class="mb-3">
                                        <label for="fullName" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="fullName"
                                            placeholder="Your Full Name" wire:model='name'>
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="email"
                                            placeholder="your.email@example.com" wire:model='email'>
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone"
                                            placeholder="+234 801 234 5678" wire:model="number">
                                        @error('number')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="chapterSelect" class="form-label">Pick A Chapter</label>
                                        <select class="form-select" id="chapterSelect"
                                            wire:model.live='selectedChapter'>
                                            <option value="">-- Select Chapter --</option>
                                            @foreach ($chapters as $value)
                                                <option value="{{ $value->id }}">{{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('selectedChapter')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if ($statusType == 'error' || $statusType == 'countdown')
                                        <div
                                            class="alert 
                                            @if ($statusType === 'error') alert-danger 
                                            @elseif ($statusType === 'countdown') alert-warning @endif">
                                            {{ $statusMessage }}
                                        </div>
                                    @else
                                        <div class="mb-3">
                                            <label for="howHeard" class="form-label">How did you hear about Doxa
                                                Church?</label>
                                            <select class="form-select" id="howHeard"
                                                wire:model='howDidYouKnowAboutUs'>
                                                <option disabled>Select an option</option>
                                                <option value="friendsAndFamily">Friend or Family</option>
                                                <option value="Social_media">Social Media</option>
                                                <option value="website">Website</option>
                                                <option value="others">Other</option>
                                            </select>
                                            @error('howDidYouKnowAboutUs')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="questions" class="form-label">Any questions or specific areas of
                                                interest?</label>
                                            <textarea class="form-control" id="questions" rows="3"
                                                placeholder="e.g., I'm interested in learning more about prayer." wire:model='interest'></textarea>
                                            @error('interest')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endif

                                    <button type="submit" class="btn btn-primary mt-3"
                                        @if ($statusType !== 'success') disabled @endif>
                                        <span wire:loading.remove>Register</span>
                                        <span wire:loading wire:target='register'>
                                            <x-spinner-loader-bootstrap color="white" size="xs" /> Loading
                                        </span>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
