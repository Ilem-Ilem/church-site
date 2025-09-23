<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['super-admin'])->group(function () {
    Volt::route('super-admin/dashboard', 'admin.superadmin.dashboard')->name('admin.super-admin.dashboard');
    Volt::route('super-admin', 'admin.superadmin.dashboard')->name('admin.super-admin.dashboard');
    //----------------------------------------------------------------------------------
    //               CONCALVE SECTION
    //----------------------------------------------------------------------------------
    Volt::route('super-admin/conclaves', 'admin.superadmin.conclave.index')->name('super-admin.conclaves');
    Volt::route('super-admin/create-conclaves', 'admin.superadmin.conclave.create')->name('super-admin.conclaves.create');
    Volt::route('super-admin/edit-conclaves/{conclave}/edit', 'admin.superadmin.conclave.edit')->name('super-admin.conclaves.edit');
    Volt::route('super-admin/conclave/add-admin', 'admin.superadmin.conclave.admin')->name('super-admin.conclaves.add-admin');
    //----------------------------------------------------------------------------------
    //               SETTINGS SECTION
    //----------------------------------------------------------------------------------
    Volt::route('super-admin/settings', 'admin.superadmin.settings.global')->name('super-admin.settings');
    Volt::route('super-admin/settings/landing', 'admin.superadmin.settings.landing')->name('super-admin.settings.landing');
});