<?php
//TODO: create the notification to the team lead for the registration of  new user
// TODO:create the track my progress page

//TODO: allow student to lay complain and take permission if they will be absent and the reason
//TODO: allow the team lead to accept the permission when needed or reject
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Url};
use App\Models\{BeliversAcademy, Chapter, BelieversAcademyTeams, StudentClasses, AcademyClases};

new #[Layout('components.layouts.tailwind-layout')] class extends Component {
    #[Url(keep: true)]
    public $chapter;

    public $user;
    public bool $isRegistered = false;
    public $classes;
    public $chapters;
    public $selectedChapter;

    public function mount()
    {
        $this->user = auth()->user();
        $this->chapters = Chapter::all();

        if ($this->user) {
            if ($this->user->hasRole('super-admin')) {
                $this->selectedChapter = request('chapter') ? Chapter::where('name', request('chapter'))->first()->id ?? $this->chapters->first()->id : $this->chapters->first()->id;
            } else {
                $this->selectedChapter = $this->user->chapter_id;
            }
            $this->chapter = Chapter::find($this->selectedChapter)->name ?? 'No Chapter';

            $student = StudentClasses::where('user_id', $this->user->id)->first();
            if ($student) {
                $this->isRegistered = true;
            }
            $this->student = $student;
        } else {
            $this->selectedChapter = request('chapter') ? Chapter::where('name', request('chapter'))->first()->id ?? $this->chapters->first()->id : $this->chapters->first()->id;
            $this->chapter = Chapter::find($this->selectedChapter)->name ?? 'No Chapter';
        }

        $this->loadClasses();
    }

    public function updatedSelectedChapter()
    {
        $this->chapter = Chapter::find($this->selectedChapter)->name ?? 'No Chapter';
        $this->loadClasses();
    }

    private function loadClasses()
    {
        $academy = BeliversAcademy::where('chapter_id', $this->selectedChapter)->first();
        if ($academy) {
            $this->classes = AcademyClases::where('academy_id', $academy->id)->get();
        } else {
            $this->classes = collect();
        }
    }
}; ?>

<div class="font-poppins" style="font-family: 'Poppins', sans-serif;">

    <!-- Hero Section -->
    <section class="relative min-h-[70vh] flex items-center justify-center px-5 py-20 bg-gradient-to-br from-blue-900 to-indigo-900 overflow-hidden mobile-section">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1534337621606-e3df5ee0e97f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80')] bg-cover bg-center"></div>
        <div class="absolute inset-0 bg-black/40"></div>

        <div class="relative z-10 w-full max-w-4xl glass rounded-2xl shadow-glass p-10 md:p-12 text-center animate-fade-in mx-4">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 animate-slide-up">Believers Academy</h1>
            <p class="text-xl md:text-2xl text-white/90 mb-8 animate-slide-up animation-delay-200 max-w-3xl mx-auto">Grow in the Word. Build foundations. Become established.</p>
            <p class="text-lg text-white/80 italic animate-slide-up animation-delay-400">"But grow in the grace and knowledge of our Lord and Savior Jesus Christ." - 2 Peter 3:18</p>
        </div>

        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <a href="#overview" class="text-white/80 hover:text-white transition-colors">
                <i class="fas fa-chevron-down text-2xl"></i>
            </a>
        </div>
    </section>

    <!-- Academy Overview Section -->
    <section id="overview" class="py-20 px-5 bg-white mobile-section">
        <div class="max-w-6xl mx-auto">
            <!-- Chapter Selector -->
            <div class="mb-8 text-center">
                <label class="block text-gray-700 font-medium mb-2">Select Branch</label>
                <select wire:model.live="selectedChapter" class="form-input max-w-xs mx-auto" {{ $user && !$user->hasRole('super-admin') ? 'disabled' : '' }}>
                    @foreach($chapters as $chap)
                        <option value="{{ $chap->id }}" {{ $selectedChapter == $chap->id ? 'selected' : '' }}>{{ $chap->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate-scale-in">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-800">Deepen Your Faith Journey</h2>
                    <p class="text-gray-600 mb-6 text-lg leading-relaxed">
                        Believers Academy is our comprehensive discipleship program designed to help you build a strong biblical foundation, understand core Christian doctrines, and develop a vibrant, growing relationship with Jesus Christ.
                    </p>
                    <p class="text-gray-600 mb-8 text-lg leading-relaxed">
                        Through systematic teaching, practical application, and community support, you'll gain the knowledge and tools needed to live out your faith confidently and impact your world for Christ.
                    </p>
                    <div class="bg-blue-50 rounded-xl p-5 border border-blue-200 mb-8">
                        <p class="text-blue-700 italic text-lg">"Then you will know the truth, and the truth will set you free." - John 8:32</p>
                    </div>
                    @if(!$isRegistered)
                        <a href="{{ route('believers_academy.register') }}" class="bg-primary text-white px-10 py-4 rounded-full font-bold hover:bg-primary-dark transition-colors duration-300 shadow-md text-lg inline-block" wire:navigate>Register Now</a>
                    @else
                        <a href="{{ route('home.believers.dashboard', request()->query()) }}" class="bg-green-500 text-white px-10 py-4 rounded-full font-bold hover:bg-green-600 transition-colors duration-300 shadow-md text-lg inline-block" wire:navigate>View Dashboard</a>
                    @endif
                </div>
                <div class="animate-scale-in animation-delay-200">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl p-8 border border-blue-100 h-full">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-book-bible text-blue-500 text-2xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2">Biblical Foundation</h3>
                                <p class="text-gray-600 text-sm">Understand Scripture and core doctrines</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-pray text-green-500 text-2xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2">Spiritual Growth</h3>
                                <p class="text-gray-600 text-sm">Develop prayer life and spiritual disciplines</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-people-group text-purple-500 text-2xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2">Community</h3>
                                <p class="text-gray-600 text-sm">Learn and grow with fellow believers</p>
                            </div>
                            <div class="text-center">
                                <div class="w-16 h-16 bg-amber-100 rounded-xl flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-hands-holding text-amber-500 text-2xl"></i>
                                </div>
                                <h3 class="font-bold text-gray-800 mb-2">Practical Ministry</h3>
                                <p class="text-gray-600 text-sm">Apply your faith in everyday life</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Learning Path Section -->
    <section class="py-20 px-5 bg-gradient-to-br from-gray-50 to-blue-50 mobile-section">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-6 text-gray-800 mobile-heading">Learning Path</h2>
            <p class="text-xl text-center text-gray-600 mb-12 max-w-2xl mx-auto leading-relaxed">Structured courses designed for spiritual growth at every stage</p>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mobile-grid-gap">
                @foreach($classes as $index => $class)
                    <div class="bg-white rounded-2xl shadow-soft p-8 border border-gray-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-2 mobile-card">
                        <div class="flex items-center mb-6">
                            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-graduation-cap text-blue-500 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800">{{ $class->name }}</h3>
                                <span class="text-blue-600 font-medium">Level {{ $index + 1 }} • Duration TBD</span>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6 leading-relaxed">{{ $class->description ?? 'Class description coming soon.' }}</p>
                        @if($class->study_material)
                            <div class="space-y-3 mb-6">
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-book text-blue-500 mr-3"></i>
                                    <span>Study Materials Available</span>
                                </div>
                            </div>
                        @endif
                        @if(!$isRegistered)
                            <a href="{{ route('believers_academy.register') }}" class="w-full bg-blue-500 text-white py-3 rounded-xl font-medium hover:bg-blue-600 transition-colors inline-block text-center" wire:navigate>Enroll Now</a>
                        @else
                            <span class="w-full bg-gray-400 text-white py-3 rounded-xl font-medium inline-block text-center">Already Enrolled</span>
                        @endif
                    </div>
                @endforeach
                @if($classes->isEmpty())
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500 text-lg">Classes will be available soon.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Call-to-Action Section -->
    <section class="py-20 px-5 bg-gradient-to-br from-blue-900 to-indigo-900 text-white mobile-section">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Begin Your Discipleship Journey</h2>
            <p class="text-xl mb-8 max-w-2xl mx-auto leading-relaxed">
                Take the next step in your spiritual growth and become established in God's Word through our comprehensive discipleship program.
            </p>
            @if(!$isRegistered)
                <a href="{{ route('believers_academy.register') }}" class="bg-accent text-white px-12 py-5 rounded-full font-bold hover:bg-accent-light transition-all duration-300 transform hover:-translate-y-1 text-lg shadow-lg inline-block" wire:navigate>Join the Next Class</a>
            @else
                <a href="{{ route('home.believers.dashboard', request()->query()) }}" class="bg-green-500 text-white px-12 py-5 rounded-full font-bold hover:bg-green-600 transition-all duration-300 transform hover:-translate-y-1 text-lg shadow-lg inline-block" wire:navigate>Continue Your Journey</a>
            @endif
            <p class="text-white/70 mt-6">Next session begins soon • Limited spots available</p>
        </div>
    </section>
</div>
