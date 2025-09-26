@php
    $leadersTeam = auth()->user()
        ->teams
        ->firstWhere(fn($team) => $team->pivot->role_in_team === 'team-lead');

    $chapterId = \App\Models\Chapter::where('name', request('chapter'))->value('id');

    $relations = [
        'appointment_teams'   => \App\Models\AppointmentTeams::class,
        'prayerRequestTeams'  => \App\Models\PrayerRequestTeam::class,
        'believersAcademyTeam'=> \App\Models\BelieversAcademyTeams::class,
        'eventTeams'         => \App\Models\EventTeam::class,
    ];

    foreach ($relations as $var => $model) {
        $$var = $model::where('chapter_id', $chapterId)->pluck('team_id')->all();
    }
@endphp

@if ($leadersTeam && in_array($leadersTeam->id, $appointment_teams))
    <flux:navlist.group expandable heading="Appointments"
        :expanded="request()->routeIs('admin.dashboard.appointments.*') ? 'true' : 'false'">
        <flux:navlist.item :href="route('admin.dashboard.appointments.index', request()->query())" wire:navigate
            :active="request()->routeIs('admin.dashboard.appointments.index') ? 'true' : 'false'">
            All Appointments
        </flux:navlist.item>
        <flux:navlist.item :href="route('admin.dashboard.appointments.settings', request()->query())" wire:navigate>
            Appointment Settings
        </flux:navlist.item>
        <flux:navlist.item :href="route('admin.dashboard.appointments.deleted_appointment', request()->query())"
            wire:navigate
            :active="request()->routeIs('admin.dashboard.appointments.deleted_appointment') ? 'true' : 'false'">
            Deleted Appointment
        </flux:navlist.item>

    </flux:navlist.group>
@endif
@if ($leadersTeam && in_array($leadersTeam->id, $prayerRequestTeams))
    <flux:navlist.group expandable heading="Prayer Requests">
        <flux:navlist.item :href="route('admin.dashboard.prayer_requests.index', request()->query())" wire:navigate>
            View Prayer Request
        </flux:navlist.item>
    </flux:navlist.group>
@endif
@role('admin')
    <flux:navlist.group expandable heading="Team Setting"
        :expanded="request()->routeIs('admin.dashboard.appointments.*') ? 'true' : 'false'">
        <flux:navlist.item :href="route('admin.dashboard.settings.appointment', request()->query())" wire:navigate
            :active="request()->routeIs('admin.dashboard.settings.appointment') ? 'true' : 'false'">
            Appointment teams
        </flux:navlist.item>

        <flux:navlist.item :href="route('admin.dashboard.prayer_requests.request_teams', request()->query())"
            wire:navigate>
            Prayer teams
        </flux:navlist.item>

        <flux:navlist.item :href="route('admin.settings.believersclass', request()->query())" wire:navigate>
            Believers Class Teams
        </flux:navlist.item>
        <flux:navlist.item :href="route('admin.dashboard.settings.event-teams', request()->query())" wire:navigate>
            Event Teams
        </flux:navlist.item>
    </flux:navlist.group>
@endrole
@if (auth()->user()->hasRole('admin') || in_array($leadersTeam->id, $believersAcademyTeam))
    <flux:navlist.group expandable heading="Believer's Academy">
        <flux:navlist.item :href="route('admin.dashboard.believers_class.academy', request()->query())" wire:navigate>
            Academy
        </flux:navlist.item>
        <flux:navlist.item :href="route('admin.dashboard.believers_class.index', request()->query())" wire:navigate>
            Believer's Classes
        </flux:navlist.item>

    </flux:navlist.group>
@else
@endif
<flux:navlist.group expandable heading="Report"
    :expanded="request()->routeIs('admin.dashboard.appointments.*') ? 'true' : 'false'">

    <flux:navlist.item :href="route('admin.dashboard.reports.index', request()->query())" wire:navigate
        :active="request()->routeIs('admin.dashboard.reports.index') ? 'true' : 'false'">
        Report
    </flux:navlist.item>
    <flux:navlist.item :href="route('admin.dashboard.reports.create-report', request()->query())" wire:navigate
        :active="request()->routeIs('admin.dashboard.reports.create-report') ? 'true' : 'false'">
        Create Report
    </flux:navlist.item>

</flux:navlist.group>
@if (auth()->user()->hasRole('admin') || in_array($leadersTeam->id, $eventTeams))
    <flux:navlist.group expandable :expanded="false" heading="Events">
        <flux:navlist.item :href="route('admin.dashboard.events.index', request()->query())" wire:navigate>
            All Events
        </flux:navlist.item>
        <flux:navlist.item :href="route('admin.dashboard.events.create', request()->query())" wire:navigate>
            Create Event
        </flux:navlist.item>

    </flux:navlist.group>
@endif
