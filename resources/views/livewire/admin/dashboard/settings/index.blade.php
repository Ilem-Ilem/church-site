<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.admin')] class extends Component {

}; ?>

<div>
    <x-fancy-header title="Settings" subtitle="Configure system and feature settings" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('admin.dashboard', request()->query())],
        ['label' => 'Settings'],
    ]" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Appointment Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Appointments</h3>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Configure appointment booking settings and availability</p>
                <a href="{{ route('admin.dashboard.settings.appointment', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Configure
                </a>
            </div>
        </div>

        <!-- Prayer Request Teams -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Prayer Request Teams</h3>
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Manage teams that handle prayer requests</p>
                <a href="{{ route('admin.dashboard.settings.request_teams', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Configure
                </a>
            </div>
        </div>

        <!-- Believers Class Settings -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Believers Academy</h3>
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.75 3 9.072 3 12.362m0 3.276c0 3.289 3.5 5.612 9 6.253m0-13c5.5-.641 9-2.964 9-6.253m0 3.276c0 3.289-3.5 5.612-9 6.253M9 19l3 1m3-1l-3 1m-9-4h10m-9 4c-1.105-1-1-4.08-1-4.08s.895-3.08 1-4.08m0 8.16l3 1m3-1l-3 1" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Configure believers academy classes and enrollment</p>
                <a href="{{ route('admin.dashboard.settings.believersclass', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Configure
                </a>
            </div>
        </div>

        <!-- Event Teams -->
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Event Teams</h3>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Manage teams for event organization and coordination</p>
                <a href="{{ route('admin.dashboard.settings.event-teams', request()->query()) }}"
                    class="inline-block px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    Configure
                </a>
            </div>
        </div>
    </div>
</div>
