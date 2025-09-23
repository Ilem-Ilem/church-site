<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
{{-- 
TODO: Add Active route indicators
--}}
<head>
    <tallstackui:script />
    {{-- <script src="/tinymce/js/tinymce/tinymce.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>

    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group :heading="__('Platform')" class="grid">
                <flux:navlist.item icon="home" :href="route('admin.dashboard', request()->query())"
                    :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>
        <flux:navlist variant="outline">

            @role(['admin', 'team-lead', 'lead-assist'])
            <flux:navlist.group expandable heading="Members"
                :expanded="request()->routeIs('admin.dashboard.members.*') ? 'true' : 'false'">
                <flux:navlist.item :href="route('admin.dashboard.members', ['chapter' => request()->get('chapter')])"
                    wire:navigate :active=" request()->routeIs('admin.dashboard.members') ? 'true' : 'false' ">
                    All Members
                </flux:navlist.item>

                <flux:navlist.item
                    :href="route('admin.dashboard.members.create', ['chapter' => request()->get('chapter')])"
                    wire:navigate :active=" request()->routeIs('admin.dashboard.members.create') ? 'true' : 'false' ">
                    Create New Member
                </flux:navlist.item>
                @role('team-lead')
                <flux:navlist.item :href="route('admin.members.add-to-team', ['chapter' => request()->get('chapter')])"
                    wire:navigate :active=" request()->routeIs('admin.members.add-to-team') ? 'true' : 'false' ">
                    Add Member To Team
                </flux:navlist.item>
                @endrole
            </flux:navlist.group>

            {{-- Report Group --}}
            
            @endrole
            @role('admin')
            {{-- Teams Group --}}
            <flux:navlist.group expandable heading="Teams"
                :expanded=" request()->routeIs('admin.dashboard.teams.*') ? 'true' : 'false' ">
                <flux:navlist.item :href="route('admin.dashboard.teams', request()->query())" wire:navigate
                    :active="request()->routeIs('admin.dashboard.teams') ? 'true' : 'false' ">
                    All Teams
                </flux:navlist.item>

                <flux:navlist.item :href="route('admin.dashboard.teams.create', request()->query())" wire:navigate
                    :active="request()->routeIs('admin.dashboard.teams.create') ? 'true' : 'false'">
                    Create New Team
                </flux:navlist.item>

                <flux:navlist.item :href="route('admin.dashboard.teams.edit-leader', request()->query())"
                    :active="request()->routeIs('admin.dashboard.teams.leader') ? 'true' : 'false'" wire:navigate>
                    Team Leader
                </flux:navlist.item>
            </flux:navlist.group>

            @endrole
            @include('partials.team-based-menu')

            
        </flux:navlist>
    
       
        <flux:spacer />

        

        <!-- Desktop User Menu -->
        <flux:dropdown class="hidden lg:block" position="bottom" align="start">
            <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                icon:trailing="chevrons-up-down" />

            <flux:menu class="w-[220px]">
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header sticky class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>
    <flux:main class="dark:bg-zinc-800">
        <x-toast />
        <x-dialog />
        {{ $slot }}
    </flux:main>


    @fluxScripts
</body>

</html>