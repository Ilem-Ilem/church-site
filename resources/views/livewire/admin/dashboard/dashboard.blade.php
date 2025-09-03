<?php

use App\Models\Chapter;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {

    public $total_members = 0;

    public function mount()
    {
        $user = Auth::user();
        if ($user->hasRole('team-lead')) {
            // Get the IDs of the teams where the user is the team-lead
            $teamIds = $user->teams
                ->filter(fn($team) => $team->pivot->role_in_team === 'team-lead')
                ->pluck('id');

            // Count all users who belong to these teams
            $this->total_members = User::whereHas('teams', fn($q) => $q->whereIn('teams.id', $teamIds))->count();
        }else{
            $this->total_members = User::where('chapter_id', '=', Chapter::where('name', '=', request()->query('chapter'))->first()->id)->count();
        }
    }
}; ?>

<div class="dark:bg-zinc-800">
    <x-card header="Members" minimize class="dark:bg-zinc-900 dark:text-gray-200 text-zinc-900">
        <div class="text-5xl m-3 font-[montserat]">{{ $total_members }}</div>
        <span><small>there are {{  $total_members }} members In Your Team view all??</small></span>
        <x-link :href="route('admin.dashboard.members', ['chapter' => e(request()->query('chapter'))])" text="Add More"
            icon="arrow-up-right" position="right" wire:navigate />
    </x-card>
</div>