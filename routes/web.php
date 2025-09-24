<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'home.landing')->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Volt::route('appointments', 'home.appointment')->name('appointment');
Volt::route('prayer_request', 'home.prayers.request')->name('prayer.request');
Volt::route('believers_academy', 'home.believers.index')->name('believers.academy');
Volt::route('belivers_academy/register', 'home.believers.register')->name('believer_academy.register');
Volt::route('belivers_academy/dashbaord', 'home.believers.dashboard')->name('home.believers.dashboard');

include __DIR__ .'/super_admin_route.php';

include __DIR__ .'/admin_route.php';

require __DIR__ . '/auth.php';
