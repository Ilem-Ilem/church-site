<?php
//TODO: set notification,
// TODO allow for upload of student certificate
//TODO allow for upload of class materials
//TODO implement bulk action
//TODO:: allow filter by date to allow print of curent month students

use App\Models\{BeliversAcademy, StudentClasses, AcademyClases, Chapter};
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions, WithPagination;

    public ?int $quantity = 10;
    public ?string $search = null;
    public array $selected = [];
    public ?string $bulkAction = null;
    public $academy;
    public $allClasses;
    public $classesNotDone;
    public ?array $studentProgress = [];
    public $student;
    // public ?int $selectedUser;

    #[Url(keep: true)]
    public ?string $chapter;

    public function mount()
    {
        $this->academy = BeliversAcademy::where('chapter_id', Chapter::where('name', e($this->chapter))->first()->id)->first();
        $this->allClasses = AcademyClases::where('academy_id', $this->academy->id)->get();
    }

    public function selectedUser($id)
    {
        $student = StudentClasses::where('user_id', $id)->where('academy_id', $this->academy->id)->first();
        $this->student = $student;
        $this->studentProgress = json_decode($student->class_completed ?? '[]', true) ?? [];
    }

    public function loadClasses()
    {
        $this->allClasses = AcademyClases::where('academy_id', $this->academy->id)->get();
    }

    public function loadStudentProgress()
    {
        if ($this->student) {
            $this->studentProgress = json_decode($this->student->class_completed ?? '[]', true) ?? [];
        }
    }
    /**
     * Table headers
     */
    public function with(): array
    {
        return [
            'headers' => [['index' => 'user.name', 'label' => 'Team Name'], ['index' => 'phone', 'label' => 'Phone Number'], ['index' => 'status', 'label' => 'Status'], ['index' => 'action', 'label' => 'Action']],
            'rows' => $this->rows(),
        ];
    }

    /**
     * Query rows with filtering + pagination
     */
    public function rows()
    {
        return StudentClasses::with('user')
            ->where('academy_id', $this->academy->id) // Count members
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->paginate($this->quantity);
    }

    /**
     * Get all row IDs for Select All
     */
    public function ids(): array
    {
        return $this->rows()->pluck('id')->toArray();
    }

    /**
     * Select all rows
     */
    public function selectAll()
    {
        $this->selected = $this->ids();
    }

    public function addToStudentCompleteClasses($id)
    {
        $student = $this->student;
        $student->class_completed = json_encode(array_merge($this->studentProgress, [$id]));
        $student->save();
        $this->student = $student;

        $this->loadClasses();
        $this->loadStudentProgress();
    }
};
?>

<div>


    <x-card class="relative dark:bg-dark-800">
        <x-table :$headers :$rows :filter="['quantity' => 'quantity', 'search' => 'search']" :quantity="[5, 15, 50, 100, 250]" paginate persistent selectable
            wire:model.live="selected">

            @interact('column_action', $row)
                {{-- Delete Team --}}
                <x-button.circle color="red" icon="trash" wire:click="deleteTeam('{{ $row->id }}')" />

                {{-- Edit Team --}}
                <button class="px-3 rounded py-1 bg-blue-800"
                    x-on:click="$wire.call('selectedUser', {{ $row->user_id }}).then(() => $modalOpen('modal-id'))">
                    Check Progress
                </button>
            @endinteract
        </x-table>
    </x-card>
    <x-modal title="Academy Classes Progress" z-index="z-10" id="modal-id">

        @if ($allClasses != null)

            <div class="space-y-4">
                @foreach ($allClasses as $class)
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-zinc-900 p-3 rounded-lg shadow-sm">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ in_array($class->id, $studentProgress) ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $class->name }}
                        </span>

                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ in_array($class->id, $studentProgress) ? now()->format('Y-m-d H:i') : 'Pending' }}
                        </span>

                        <button
                            class="px-3 py-1 rounded text-xs font-semibold
                            {{ in_array($class->id, $studentProgress) ? 'bg-gray-600 text-white' : 'bg-green-600 text-white' }}"
                            @click="$wire.call('addToStudentCompleteClasses', {{ $class->id }})">
                            {{ in_array($class->id, $studentProgress) ? 'Completed' : 'Mark Done' }}
                        </button>
                    </div>
                @endforeach
            </div>
        @else
            <x-spinner-loader size="xl" color="white"></x-spinner-loader>
        @endif

    </x-modal>

</div>
