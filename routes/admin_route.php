<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::prefix('admin/dashboard')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        // -----------------------------------------------------------------
        //              SETTINGS SECTION
        //-------------------------------------------------------------------
        volt::route('settings/appointment', 'admin.dashboard.settings.appointment')->name('admin.dashboard.settings.appointment');
        Volt::route('prayer-request-teams', 'admin.dashboard.settings.request_teams')->name('admin.dashboard.prayer_requests.request_teams');
        Volt::route('believers-academy', 'admin.dashboard.settings.believersclass')->name('admin.settings.believersclass');
        // -----------------------------------------------------------------
        //              MEMBERS SECTION
        //-------------------------------------------------------------------
        Volt::route('/', 'admin.dashboard.dashboard')->name('admin.dashboard');
        Volt::route('members', 'admin.dashboard.members.index')->name('admin.dashboard.members');
        Volt::route('members_create', 'admin.dashboard.members.create')->name('admin.dashboard.members.create');
        Volt::route('members/{member}/edit', 'admin.dashboard.members.edit')->name('admin.dashboard.members.edit');
        Volt::route('members/{member}/edit_teams', 'admin.dashboard.members.edit-team')->name('admin.dashboard.edit-team');
        Volt::route('members/add-to-team', 'admin.dashboard.members.add-to-team')->name('admin.members.add-to-team');
        // -----------------------------------------------------------------
        //              TEAMS SECTION
        //-------------------------------------------------------------------
        Volt::route('teams', 'admin.dashboard.teams.index')->name('admin.dashboard.teams');
        Volt::route('team_create', 'admin.dashboard.teams.create')->name('admin.dashboard.teams.create');
        Volt::route('teams/{team}/edit', 'admin.dashboard.teams.edit')->name('admin.dashboard.teams.edit');
        Volt::route('teams/edit_lead', 'admin.dashboard.teams.leader')->name('admin.dashboard.teams.edit-leader');
        // -----------------------------------------------------------------
        //              PRAYER REQUEST SECTION
        //-------------------------------------------------------------------
        Volt::route('prayer-requests', 'admin.dashboard.prayer_request.index')->name('admin.dashboard.prayer_requests.index');
        // -----------------------------------------------------------------
        //              BELIEVER'S ACADEMY
        //-------------------------------------------------------------------
        Volt::route('believers_academy', 'admin.dashboard.believers_class.academy')->name('admin.dashboard.believers_class.academy');
        Volt::route('believers_academy/classes', 'admin.dashboard.believers_class.index')->name('admin.dashboard.believers_class.index');
        // Reports Routes
        Volt::route('reports', 'admin.dashboard.reports.index')->name('admin.dashboard.reports.index');
        Volt::route('create-report', 'admin.dashboard.reports.create-report')->name('admin.dashboard.reports.create-report');
        Volt::route('reports/compile-report', 'admin.dashboard.reports.compile-report')->name('admin.dashboard.reports.compile-report');
        Volt::route('reports/report-sent-to-hq', 'admin.dashboard.reports.report-sent-to-hq')->name('admin.dashboard.reports.report-sent-to-hq');

        // Finance Routes
        Volt::route('finance', 'admin.dashboard.finance.index')->name('admin.dashboard.finance.index');
        Volt::route('finance/payment-details', 'admin.dashboard.finance.payment-details')->name('admin.dashboard.finance.payment-details');
        Volt::route('finance/givings', 'admin.dashboard.finance.givings')->name('admin.dashboard.finance.givings');
        Volt::route('finance/add-givings-details', 'admin.dashboard.finance.add-givings-details')->name('admin.dashboard.finance.add-givings-details');

        // Appointments Routes
        Volt::route('appointments', 'admin.dashboard.appointments.index')->name('admin.dashboard.appointments.index');
        Volt::route('deleted_appointement', 'admin.dashboard.appointments.deleted_appointment')->name('admin.dashboard.appointments.deleted_appointment');
        Volt::route('appointment_settings', 'admin.dashboard.appointments.settings')->name('admin.dashboard.appointments.settings');

        // Resource Inventory Routes
        Volt::route('resource/inventory', 'admin.dashboard.resource.inventory.index')->name('admin.dashboard.resource.inventory.index');
        Volt::route('resource/inventory/add', 'admin.dashboard.resource.inventory.add')->name('admin.dashboard.resource.inventory.add');
        Volt::route('resource/inventory/edit/{id}', 'admin.dashboard.resource.inventory.edit')->name('admin.dashboard.resource.inventory.edit');

        // Medicals Routes
        Volt::route('medicals', 'admin.dashboard.medicals.index')->name('admin.dashboard.medicals.index');
        Volt::route('medicals/card', 'admin.dashboard.medicals.card')->name('admin.dashboard.medicals.card');
        Volt::route('medicals/card-payment', 'admin.dashboard.medicals.card-payment')->name('admin.dashboard.medicals.card-payment');
        Volt::route('medicals/card-record', 'admin.dashboard.medicals.card-record')->name('admin.dashboard.medicals.card-record');

        // Scribes Routes
        Volt::route('scribes', 'admin.dashboard.scribes.index')->name('admin.dashboard.scribes.index');
        Volt::route('scribes/general-report', 'admin.dashboard.scribes.general-report')->name('admin.dashboard.scribes.general-report');
        Volt::route('scribes/reports', 'admin.dashboard.scribes.reports')->name('admin.dashboard.scribes.reports');
        Volt::route('scribes/doxa-update', 'admin.dashboard.scribes.doxa-update')->name('admin.dashboard.scribes.doxa-update');

        // Properties Routes
        Volt::route('properties', 'admin.dashboard.properties.index')->name('admin.dashboard.properties.index');
        Volt::route('properties/inventory', 'admin.dashboard.properties.inventory')->name('admin.dashboard.properties.inventory');
        Volt::route('properties/add-inventory', 'admin.dashboard.properties.add-inventory')->name('admin.dashboard.properties.add-inventory');
        Volt::route('properties/edit-inventory/{id}', 'admin.dashboard.properties.edit-inventory')->name('admin.dashboard.properties.edit-inventory');

        // Missions Routes
        Volt::route('missions', 'admin.dashboard.missions.index')->name('admin.dashboard.missions.index');
        Volt::route('missions/report', 'admin.dashboard.missions.report')->name('admin.dashboard.missions.report');
        Volt::route('missions/new-members', 'admin.dashboard.missions.new-members')->name('admin.dashboard.missions.new-members');
        Volt::route('missions/out-reach-details', 'admin.dashboard.missions.out-reach-details')->name('admin.dashboard.missions.out-reach-details');
        Volt::route('missions/outreach-report', 'admin.dashboard.missions.outreach-report')->name('admin.dashboard.missions.outreach-report');
    });
