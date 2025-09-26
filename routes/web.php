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
//-----------------------------------------------------------------------------------
//BELIEVERS ACADEMY ROUTE
//------------------------------------------------------------------------------------
Volt::route('believers_academy', 'home.believers.index')->name('believers.academy');
Volt::route('belivers_academy/register', 'home.believers.register')->name('believer_academy.register');
Volt::route('belivers_academy/dashbaord', 'home.believers.dashboard')->name('home.believers.dashboard');
//-----------------------------------------------------------------------------------
//pARTNERSHIP ROUTES
//------------------------------------------------------------------------------------
Volt::route('partnership', 'home.partnership.index')->name('home.partnership.index');
//------------------------------------------------------------------------------------
//EVENT ROUTES
//====================================================================================
// Event Index
Volt::route('/events', 'home.events.index')
    ->name('events.index');

// Event Registration (takes event slug or id)
Volt::route('/events/{event}/register', 'home.events.register')
    ->name('events.register');

// Event Gallery (takes event slug or id)
Volt::route('/events/{event}/gallery', 'home.events.gallery')
    ->name('events.gallery');
include __DIR__ .'/super_admin_route.php';

include __DIR__ .'/admin_route.php';

require __DIR__ . '/auth.php';
