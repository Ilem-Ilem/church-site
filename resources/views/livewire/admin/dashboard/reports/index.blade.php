<?php

use App\Models\Chapter;
use App\Models\Report;
use Livewire\Attributes\{Layout, Url};
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions;

    #[Url]
    public $chapter;

    public $leadersTeam;

    public function mount()
    {
        // Get logged-in user's lead role team (if any)
        $this->leadersTeam = Auth::user()->teams->filter(fn($team) => in_array($team->pivot->role_in_team, ['team-lead', 'lead-assist']))->first();
    }
    public function getReports()
    {
        $query = Report::with(['createdBy', 'team']);
        $user = Auth::user();

        // Team-lead / lead-assist (non-admin): see ALL their team's reports (any level)
        $query->when($this->leadersTeam && !$user->hasRole('admin'), function ($q) {
            $q->where('team_id', $this->leadersTeam->id);
        });

        // Admin: see everything
        $query->when($user->hasRole('admin'), function ($q) {
            // If you want admins to see all reports:
            // nothing needed, they already do
            // If you want them limited to their team + chapter, uncomment:
            /*
        if ($this->leadersTeam) {
            $q->where(function ($sub) {
                $sub->where('team_id', $this->leadersTeam->id)
                    ->orWhere('level', 'chapter');
            });
        }
        */
        });

        // Optional filter by chapter param
        $query->when($this->chapter, function ($q) {
            $chapter = Chapter::where('name', e($this->chapter))->firstOrFail();
            $q->where('chapter_id', $chapter->id);
        });

        return $query->latest()->paginate(10);
    }

    public function with()
    {
        return [
            'headers' => [['index' => 'title', 'label' => 'Title'], ['index' => 'report_date', 'label' => 'Date'], ['index' => 'team.name', 'label' => 'Team'], ['index' => 'level', 'label' => 'Report Level'], ['index' => 'createdBy.name', 'label' => 'Created By'], ['index' => 'actions', 'label' => 'Actions']],
            'rows' => $this->getReports(),
        ];
    }

    public function changeLevel(int $id, string $level)
    {
        $report = Report::findOrFail($id);
        $user = Auth::user();

        $nextLevels = [
            'team' => 'chapter',
            'chapter' => 'hq',
        ];

        $currentLevel = $report->level;
        $allowedNext = $nextLevels[$currentLevel] ?? null;

        // 🚨 Stop invalid or backward changes
        if ($allowedNext !== $level) {
            $this->toast()->error('Not allowed', 'You cannot change report level this way ❌')->send();
            return;
        }

        // ✅ Rules:
        // - team-lead / lead-assist can only move team -> chapter for their own reports
        if (in_array($currentLevel, ['team']) && $level === 'chapter') {
            if (!$user->hasRole('admin') && $report->team_id !== $this->leadersTeam?->id) {
                $this->toast()->error('Unauthorized', 'You can only push reports from your own team')->send();
                return;
            }
        }

        // - only admin can push chapter -> hq
        if ($currentLevel === 'chapter' && $level === 'hq' && !$user->hasRole('admin')) {
            $this->toast()->error('Unauthorized', 'Only admins can escalate to HQ')->send();
            return;
        }

        // Save promotion
        $report->level = $level;
        $report->save();

        if ($level === 'chapter') {
            $this->toast()->success('Done', 'Report sent to Chapter Admin ✅')->send();
        } elseif ($level === 'hq') {
            $this->toast()->success('Done', 'Report escalated to HQ 🚀')->send();
        }
    }

    public function deleteReport(int $id)
    {
        $report = Report::findOrFail($id);

        // Check if report is less than 24 hours old
        if ($report->report_date && \Carbon\Carbon::parse($report->report_date)->greaterThan(now()->subDay())) {
            $report->delete();
            $this->toast()->success('Deleted', 'Report deleted successfully')->send();
        } else {
            $this->toast()->error('Not Allowed', 'Reports older than 24 hours cannot be deleted ❌')->send();
        }
    }
}; ?>

<div>
    <x-table :$headers :$rows>
        {{-- Format Report Date --}}
        @interact('column_report_date', $row)
            <p>{{ \Carbon\Carbon::parse($row->report_date)->format('M d, Y @ h:i A') }}</p>
        @endinteract

        {{-- Report Level (Admins see as text, leaders can escalate) --}}
        @interact('column_level', $row)
            @php
                $colors = [
                    'team' => 'bg-green-500 text-white',
                    'chapter' => 'bg-blue-500 text-white',
                    'hq' => 'bg-purple-600 text-white',
                ];
                $badgeClass =
                    'px-3 py-1 rounded-full text-xs font-semibold ' .
                    ($colors[$row->level] ?? 'bg-gray-400 text-white');

                $nextLevels = [
                    'team' => 'chapter',
                    'chapter' => 'hq',
                ];
                $nextLevel = $nextLevels[$row->level] ?? null;

                $tooltip = $nextLevel
                    ? 'Click to upgrade to ' . ucfirst($nextLevel)
                    : ucfirst($row->level) . ' (final level)';
            @endphp

            @if (Auth::user()->hasRole('admin'))
                {{-- Admin can push chapter -> HQ --}}
                @if ($row->level === 'chapter')
                    <button class="{{ $badgeClass }} hover:opacity-80 transition" title="{{ $tooltip }}"
                        wire:click="changeLevel({{ $row->id }}, 'hq')">
                        Push to Hq
                    </button>
                @else
                    <span class="{{ $badgeClass }}" title="{{ ucfirst($row->level) }} level">
                        {{ ucfirst($row->level) }}
                    </span>
                @endif
            @elseif($row->level === 'team' && $row->team_id === $leadersTeam?->id)
                {{-- Team lead/assist can only push their own reports to chapter --}}
                <button class="{{ $badgeClass }} hover:opacity-80 transition" title="{{ $tooltip }}"
                    wire:click="changeLevel({{ $row->id }}, 'chapter')">
                    Push To Chapter
                </button>
            @else
                {{-- Otherwise just static --}}
                <span class="{{ $badgeClass }}" title="{{ ucfirst($row->level) }} level">
                    {{ ucfirst($row->level) }}
                </span>
            @endif

        @endinteract

        {{-- Actions --}}
        @interact('column_actions', $row)
            <x-button.circle color="red" icon="trash" label="View" wire:click='deleteReport({{ $row->id }})'
                class="mr-2" />
        @endinteract
    </x-table>
</div>
