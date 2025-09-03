<?php

use App\Models\Chapter;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Volt\Component;
use TallStackUi\Traits\Interactions;
new #[Layout('components.layouts.admin')] class extends Component {
    use Interactions;

    #[Url]
    public ?string $chapter = null; // chapter slug/name from URL

    public ?int $chapterId = null; // actual chapter_id for DB queries

    public array $teams = [];

    public ?int $selectedTeam = null;

    // Two separate select bindings
    public ?int $selectedUserInTeam = null;
    public ?int $selectedUserOutsideTeam = null;

    public ?User $currentLeader = null;
    public array $teamUsers = [];
    public array $nonTeamUsers = [];

    public function mount(): void
    {
        // Find chapter
        $chapter         = Chapter::where('name', $this->chapter)->firstOrFail();
        $this->chapterId = $chapter->id;

        // Load all teams in this chapter for the select dropdown
        $this->teams = Team::where('chapter_id', $this->chapterId) ->when(
                $this->chapter,
                fn($q) =>
                $q->whereHas(
                    'chapter',
                    fn($qq) =>
                    $qq->where('name', $this->chapter)
                )
            )
            ->get()
            ->map(fn($t) => [
                'value' => $t->id,
                'label' => $t->name,
            ])->toArray();
    }

    public function updatedSelectedTeam($teamId): void
    {
        $team = Team::with('users')->find($teamId);

        if (!$team) {
            $this->reset(['currentLeader', 'teamUsers', 'nonTeamUsers']);
            return;
        }

        // Current leader based on Spatie role
        $this->currentLeader = $team->users()
            ->get() // get the collection first
            ->first(fn($u) => $u->hasRole('team-lead'));

        // Users in team
        $this->teamUsers = $team->users->map(fn($u) => [
            'value' => $u->id,
            'label' => $u->name,
        ])->toArray();

        // Users not in team but in the same chapter
        $this->nonTeamUsers = User::where('chapter_id', $this->chapterId)
            ->whereDoesntHave('teams', fn($q) => $q->where('teams.id', $team->id))
            ->get()
            ->map(fn($u) => [
                'value' => $u->id,
                'label' => $u->name,
            ])->toArray();

        // Reset selections
        $this->reset(['selectedUserInTeam', 'selectedUserOutsideTeam']);
    }


    public function assignLeader(): void
    {
        $userId = $this->selectedUserInTeam ?? $this->selectedUserOutsideTeam;

        if (!$this->selectedTeam || !$userId) {
            return;
        }

        $team = Team::with('users')->find($this->selectedTeam);
        $newLeader = User::find($userId);

        if (!$team || !$newLeader) {
            return;
        }

        // Find current leader in this team (Spatie role)
        $currentLeader = $team->users->first(fn($u) => $u->hasRole('team-lead'));

        if ($currentLeader) {
            // Remove team-lead role from current leader
            $currentLeader->removeRole('team-lead');

            // Update pivot to 'member'
            $team->users()->updateExistingPivot($currentLeader->id, [
                'role_in_team' => 'member',
            ]);
        }

        // Add or update new leader in pivot
        if ($team->users()->where('user_id', $newLeader->id)->exists()) {
            $team->users()->updateExistingPivot($newLeader->id, [
                'role_in_team' => 'team-lead',
            ]);
        } else {
            $team->users()->attach($newLeader->id, [
                'chapter_id' => $this->chapterId,
                'role_in_team' => 'team-lead',
            ]);
        }

        // Assign Spatie role to new leader
        $newLeader->assignRole('team-lead');

        $this->toast()->success(
            'Team Leader Assigned',
            'The selected user is now the team leader.'
        )->send();

        // Refresh lists and current leader
        $this->updatedSelectedTeam($this->selectedTeam);
    }


};
?>
<div>
    <x-fancy-header title="Teams" subtitle="Manage Teams" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard', request()->query())],
        ['label'=>'Teams', 'url'=>route('admin.dashboard.teams', request()->query())],
        ['label' => 'Edit Teams Leader']
    ]" class="mb-4">
      
    </x-fancy-header>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <!-- Select Team -->
        <div>
            <label for="team" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Select Team <span class="float-right">{{ count($teams) }}</span>
            </label>
            <select id="team" wire:model.live="selectedTeam" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 shadow-sm
                       bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                       focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-50">
                <option value="">Choose a team</option>
                @foreach($teams as $team)
                    <option value="{{ $team['value'] }}">{{ $team['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Current Team Leader -->
        <div>
            <label for="currentLeader" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Current Team Leader
            </label>
            <select id="currentLeader" disabled class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 shadow-sm
                       bg-zinc-100 dark:bg-zinc-700 text-zinc-900 dark:text-zinc-200">
                @if($currentLeader)
                    <option>{{ $currentLeader->name }}</option>
                @else
                    <option>No leader assigned</option>
                @endif
            </select>
        </div>

        <!-- Users in Team -->
        <div>
            <label for="teamUsers" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Users in Team <span class="float-right">{{ count($teamUsers) }}</span>
            </label>
            <select id="teamUsers" wire:model.live="selectedUserInTeam" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 shadow-sm
                       bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                       focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-50">
                <option value="">Select a user</option>
                @foreach($teamUsers as $user)
                    <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Users outside Team -->
        <div>
            <label for="nonTeamUsers" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">
                Users outside Team <span class="float-right">{{ count($nonTeamUsers) }}</span>
            </label>
            <select id="nonTeamUsers" wire:model.live="selectedUserOutsideTeam" class="w-full rounded-lg border-zinc-300 dark:border-zinc-700 shadow-sm
                       bg-white dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100
                       focus:border-indigo-500 focus:ring focus:ring-indigo-400 focus:ring-opacity-50">
                <option value="">Select a user</option>
                @foreach($nonTeamUsers as $user)
                    <option value="{{ $user['value'] }}">{{ $user['label'] }}</option>
                @endforeach
            </select>
        </div>

        <!-- Final Selected User Card -->
        <div class="col-span-1 md:col-span-3">
            @if($selectedUserInTeam || $selectedUserOutsideTeam)
                @php
                    $selectedUser = null;
                    if ($selectedUserInTeam) {
                        $selectedUser = collect($teamUsers)->firstWhere('value', $selectedUserInTeam);
                    } elseif ($selectedUserOutsideTeam) {
                        $selectedUser = collect($nonTeamUsers)->firstWhere('value', $selectedUserOutsideTeam);
                    }
                @endphp

                @if($selectedUser)
                    <div class="p-4 bg-white dark:bg-zinc-800 rounded-lg shadow border border-zinc-200 dark:border-zinc-700">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Selected New Leader:</p>
                        <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                            {{ $selectedUser['label'] }}
                        </p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div class="col-span-1 md:col-span-3 mt-4">
        <button wire:click="assignLeader" @if(!$selectedUserInTeam && !$selectedUserOutsideTeam) disabled @endif class="px-4 py-2 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700
               disabled:opacity-50 disabled:cursor-not-allowed">
            Save Leader
        </button>
        <x-button :href="route('admin.dashboard.teams', request()->query())" icon="arrow-long-left" wire:navigate>Back</x-button>
    </div>
</div>