<?php

use App\Models\{Report, Chapter, Team};
use Livewire\Attributes\{Layout, Url};
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use TallStackUi\Traits\Interactions;

new #[Layout('components.layouts.admin')] class extends Component
{
    use WithFileUploads, Interactions;

    #[Url]
    public $chapter;
    public $report_date;
    public $title;
    public $description;
    public $event_type;
    public $report_path; // temporary uploaded file
    public $content; // rich text report body
    public ?string $level;
    public $chapter_id;
    public $team_id;
    public $created_by;
    public $leadersChapter;
    public $chapters;
    public $teams;
    public $leadersTeam;

    public function mount()
    {
        $this->chapters = Chapter::all();
        $this->teams = Team::all();
        $this->created_by = Auth::user()->name;
        $this->leadersTeam = Auth()->user()->teams->filter(fn($team) => $team->pivot->role_in_team === 'team-lead' || $team->pivot->role_in_team === 'lead-assist')->first();
        if($this->chapter != null){
            $this->leadersChapter = Chapter::where('name', '=', e($this->chapter))->firstOrFail();
        }
    }

    public function saveReport()
    {
        $this->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_type'  => 'nullable|string|max:100',
            'report_path' => 'nullable|image|max:2048', // <= 2MB
            'content'     => 'nullable|string',
            'level'       => 'required|in:team,chapter,hq',
            'chapter_id'  => 'nullable|exists:chapters,id',
            'team_id'     => 'nullable|exists:teams,id',
        ]);

        $path = null;
        if ($this->report_path) {
            $path = $this->report_path->store('reports', 'public'); 
            // stored in storage/app/public/reports
        }
        dd([
            'report_date' =>now(),
            'title'       => $this->title,
            'description' => $this->description,
            'event_type'  => $this->event_type,
            'report_path' => $path,
            'report'      => $this->content,
            'level'       => $this->level,
            'chapter_id'  => $this->chapter_id,
            'team_id'     => $this->team_id,
            'created_by'  => auth()->id(),
        ]);
        try {
            Report::create([
            'report_date' =>now(),
            'title'       => $this->title,
            'description' => $this->description,
            'event_type'  => $this->event_type,
            'report_path' => $path,
            'report'      => $this->content,
            'level'       => $this->level,
            'chapter_id'  => $this->chapter_id,
            'team_id'     => $this->team_id,
            'created_by'  => auth()->id(),
        ]);

        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $this->toast()
            ->success('Done!','Report saved successfully ✅')
            ->send();
        // session()->flash('success', 'Report saved successfully ✅');

        $this->reset(['report_date','title','description','event_type','report_path','content','level','chapter_id','team_id']);
    }

};

?>

<div>

    <form wire:submit.prevent="saveReport" class="space-y-4 mt-6">
        <x-input label="Title" type="text" wire:model="title" />
        <x-textarea label="Description" wire:model="description" />

        {{-- Rich text content --}}
        <x-textarea id="tinymce-editor" label="Report Content" wire:model="content"></x-textarea>
        <p class="text-gray-300 text-xs -mt-2"> This will be used for writting the report, if it has to be written, it can be left blank if PDF or DOCX file will be uploaded instaed</p>


        <x-select label="Event Type" wire:model="event_type">
            <option value="">Select Event Type</option>
            <option value="meeting">Meeting</option>
            <option value="workshop">Workshop</option>
            <option value="conference">Conference</option>
        </x-select>

        {{-- File Upload --}}
        <div>
            <x-input type="file" label="Report File" wire:model="report_path" />
            @error('report_path') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <p class="text-gray-300 text-xs"> This will be used for Uploading report in PDF of Document format, it can be left blank if the report will be written instead</p>
        </div>

        @if(auth()->user()->hasRole('team-lead'))
        <x-select label="Level" wire:model="level">
            <option value="team">Team</option>
            <option value="chapter">Chapter</option>
        </x-select>
        @elseif (auth()->user()->hasAnyRole('lead-assist'))
        <x-select label="Level" wire:model="level">
            <option value="team">Team</option>
        </x-select>
        @else
        <x-select label="Level" wire:model="level">
            <option value="chapter">Chapter</option>
            <option value="hq">HQ</option>
        </x-select>
        @endif

        @if($leadersChapter != null)
        <x-select label="Chapter" wire:model="chapter_id">
            <option value="{{ $leadersChapter->id }}">{{ $leadersChapter->name }}</option>
        </x-select>
        @else
        <x-select label="Chapter" wire:model="chapter_id">
            <option value="">Select a Chapter</option>
            @foreach ($chapters as $chapter)
            <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
            @endforeach
        </x-select>
        @endif


        @if($leadersTeam !== null)
        <x-select label="Team" wire:model="team_id">
            <option value="{{ $leadersTeam->id }}">{{ ucfirst($leadersTeam->name) }}</option>

        </x-select>
        @else
        <x-select label="Team" wire:model="team_id">
            <option value="">Select Team</option>
            @foreach($teams as $team)
            <option value="{{ $team->id }}">{{ $team->name }}</option>
            @endforeach
        </x-select>
        @endif


        <x-input label="Created By" type="text" wire:model="created_by" disabled />

        <x-button type="submit" label="Save Report" class="mt-4">
            save
        </x-button>
    </form>
</div>
