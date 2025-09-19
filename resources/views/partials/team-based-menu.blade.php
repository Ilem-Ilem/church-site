@php
$leadersTeam = Auth()->user()->teams->filter(fn($team) => $team->pivot->role_in_team === 'team-lead')->first();
$chapterId = \App\Models\Chapter::where('name', '=', request()->get('chapter'))->first()->id;
$appointment_teams = \App\Models\AppointmentTeams::where('chapter_id', $chapterId)->get()->pluck('team_id')->toArray();
@endphp

@if ($leadersTeam && in_array($leadersTeam->id, $appointment_teams))
<flux:navlist.group expandable heading="Appointments" :expanded="request()->routeIs('admin.dashboard.appointments.*') ? 'true' : 'false'">
    <flux:navlist.item :href="route('admin.dashboard.appointments.index', request()->query())" wire:navigate :active="request()->routeIs('admin.dashboard.appointments.index') ? 'true' : 'false'">
        All Appointments
    </flux:navlist.item>
    <flux:navlist.item :href="route('admin.dashboard.appointments.settings', request()->query())" wire:navigate >
        Appointment Settings
    </flux:navlist.item>
    <flux:navlist.item :href="route('admin.dashboard.appointments.deleted_appointment', request()->query())" wire:navigate :active="request()->routeIs('admin.dashboard.appointments.deleted_appointment') ? 'true' : 'false'">
        Deleted Appointment
    </flux:navlist.item>

</flux:navlist.group>
@endif
@role('admin')
<flux:navlist.group expandable heading="Team Setting" :expanded="request()->routeIs('admin.dashboard.appointments.*') ? 'true' : 'false'">
    <flux:navlist.item :href="route('admin.dashboard.settings.appointment', request()->query())" wire:navigate :active="request()->routeIs('admin.dashboard.settings.appointment') ? 'true' : 'false'">
        Appointment teams
    </flux:navlist.item>

</flux:navlist.group>
@endrole
<flux:navlist.group expandable heading="Report" :expanded="request()->routeIs('admin.dashboard.appointments.*') ? 'true' : 'false'">

    <flux:navlist.item :href="route('admin.dashboard.reports.index', request()->query())" wire:navigate :active="request()->routeIs('admin.dashboard.reports.index') ? 'true' : 'false'">
        Report
    </flux:navlist.item>
    <flux:navlist.item :href="route('admin.dashboard.reports.create-report', request()->query())" wire:navigate :active="request()->routeIs('admin.dashboard.reports.create-report') ? 'true' : 'false'">
        Create Report
    </flux:navlist.item>

</flux:navlist.group>
