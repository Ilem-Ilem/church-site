<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.admin')] class extends Component {

    #[Url]
    public $chapter;

    public $teamFunctions;
    public $team;

    public $functions;

    public function mount()
    {

    }

}; ?>

<div>
    <x-fancy-header title="Assign Functions Team" subtitle="Manage team and the roles they play on the platform"
        :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Teams', 'url' => route('admin.dashboard.teams')],
        ['label' => 'Team Function']
    ]">
    </x-fancy-header>
    <x-card>
     
    </x-card>
</div>