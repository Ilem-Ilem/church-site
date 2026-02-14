<?php
//TODO: fix the classes dashboard for updating a class
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout};
use App\Models\{BeliversAcademy, Chapter, BelieversAcademyTeams, User, StudentClasses, AcademyClases};
use Illuminate\Support\Facades\Storage;

new #[Layout('components.layouts.tailwind-layout')] class extends Component {
    public $classes;
    public $student_classes;
    public $classDone;
    public $classNotDone;
    public $completionDate;

    public function mount()
    {
        if (!auth()->check()) {
            return redirect()->route('home.login');
        }

        $this->classes = AcademyClases::all();
        $this->student_classes = StudentClasses::where('user_id', auth()->user()->id)->get();
        $this->student = StudentClasses::where('user_id', auth()->user()->id)->first();
        $this->classDone = json_decode($this->student->class_completed ?? '[]');

        $this->classNotDone = array_diff($this->classes->pluck('id')->toArray(), $this->classDone);
    }
}; ?>

<div class="mx-auto w-full max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
    <section class="rounded-3xl border border-blue-100 bg-white p-6 shadow-[0_24px_60px_-40px_rgba(37,99,235,0.45)] sm:p-8">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-600">Believers Academy</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Class Progress</h1>
                <p class="mt-2 text-sm text-slate-600">Track your learning milestones and completion status.</p>
            </div>

            @if(count($classNotDone) == 0)
                <a href="{{ route('certificate.generate', ['name' => auth()->user()->name, 'date' => $completionDate ?? now()->toDateString()]) }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700" target="_blank">
                    Download Certificate
                </a>
            @endif
        </div>

        <div class="overflow-x-auto rounded-2xl border border-blue-100">
            <table class="min-w-full divide-y divide-blue-100 text-sm">
                <thead class="bg-blue-50 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">#</th>
                        <th class="px-4 py-3 text-left font-semibold">Course</th>
                        <th class="px-4 py-3 text-left font-semibold">Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-left font-semibold">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-blue-50">
                    @foreach ($classes as $key => $class)
                        <tr class="hover:bg-blue-50/40">
                            <td class="px-4 py-3 text-slate-600">{{ $key + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-slate-900">{{ $class->name }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $class->date }}</td>
                            <td class="px-4 py-3">
                                @if (in_array($class->id, $classDone))
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Completed</span>
                                @else
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" class="rounded-lg border border-blue-200 px-3 py-1.5 text-xs font-semibold text-blue-700 transition hover:bg-blue-50">
                                    Request Update
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
