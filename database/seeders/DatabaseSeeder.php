<?php

namespace Database\Seeders;

use App\Models\Team;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Chapter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {
        $faker = Faker::create();

        // -------------------------------
        // Roles Seeding
        // -------------------------------
        $roles = [
            'super-admin',
            'admin',
            'team-lead',
            'lead_assist',
            'unit_head',
            'member',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // -------------------------------
        // Super Admin User (chapter_id = null)
        // -------------------------------
        $user = User::create([
            'name' => 'ilem',
            'email' => 'ilem@gmail.com',
            'password' => 'password'
        ]);

        if (!$user->hasRole('super_admin')) {
            $user->assignRole('super-admin');
        }
        // -------------------------------
        // Chapters
        // -------------------------------
        $Chapters = [
            ['id' => 'Calabar', 'name' => 'Calabar Branch'],
            ['id' => 'Lagos', 'name' => 'Lagos'],
            ['id' => 'hq', 'name' => 'DOXA CITY HEADQUARTERS'],
        ];

        foreach ($Chapters as $Chapter_data) {
            $Chapter = Chapter::firstOrCreate(
                [
                    'name' => $Chapter_data['name'],
                    'data' => $Chapter_data
                ]
            );

            // -------------------------------
            // Teams per Chapter
            // -------------------------------
            $teams = [
                ['name' => 'COHTECH APOSTOLIC CENTER', 'short' => 'COHTECH', 'banner' => 'teams/cohtech.png', 'has_team_lead' => true],
                ['name' => 'UNICROSS APOSTOLIC CENTER', 'short' => 'UNICROSS', 'banner' => 'teams/unicross.png', 'has_team_lead' => true],
                ['name' => 'DOXA MUSIC MINISTRY', 'short' => 'MUSIC', 'banner' => 'teams/music.png', 'has_team_lead' => true],
                ['name' => 'PROTOCOL', 'short' => 'PROTO', 'banner' => 'teams/protocol.png', 'has_team_lead' => true],
                ['name' => 'SCRIBE', 'short' => 'SCRIBE', 'banner' => 'teams/scribe.png', 'has_team_lead' => true],
                ['name' => 'MISSIONS', 'short' => 'MISSION', 'banner' => 'teams/missions.png', 'has_team_lead' => true],
                ['name' => 'USHERING', 'short' => 'USH', 'banner' => 'teams/ushering.png', 'has_team_lead' => true],
                ['name' => 'MEDIA', 'short' => 'MEDIA', 'banner' => 'teams/media.png', 'has_team_lead' => true],
                ['name' => 'COUNSELING', 'short' => 'COUNSEL', 'banner' => 'teams/counseling.png', 'has_team_lead' => true],
                ['name' => 'TECHNICAL', 'short' => 'TECH', 'banner' => 'teams/technical.png', 'has_team_lead' => true],
                ['name' => 'DOXA PROPERTIES TEAM', 'short' => 'DPT', 'banner' => 'teams/properties.png', 'has_team_lead' => true],
                ['name' => 'HOSPITALITY', 'short' => 'HOSP', 'banner' => 'teams/hospitality.png', 'has_team_lead' => true],
                ['name' => 'SANCTUARY', 'short' => 'SANCT', 'banner' => 'teams/sanctuary.png', 'has_team_lead' => true],
                ['name' => 'PHOS THEATRE', 'short' => 'PHOS', 'banner' => 'teams/phos.png', 'has_team_lead' => true],
                ['name' => 'MEDICAL TEAM', 'short' => 'MED', 'banner' => 'teams/medical.png', 'has_team_lead' => true],
                ['name' => 'TRANSPORT', 'short' => 'TRANS', 'banner' => 'teams/transport.png', 'has_team_lead' => true],
                ['name' => 'SPORTS', 'short' => 'SPORTS', 'banner' => 'teams/sports.png', 'has_team_lead' => true],
                ['name' => 'Doxa Believers Academy', 'short' => 'DBA', 'banner' => 'teams/dba.png', 'has_team_lead' => true],
                ['name' => 'CONTENT CREATION TEAM', 'short' => 'CCT', 'banner' => 'teams/content.png', 'has_team_lead' => false],
                ['name' => 'SOCIAL MEDIA', 'short' => 'SOCIAL', 'banner' => 'teams/social.png', 'has_team_lead' => false],
                ['name' => 'LIGHTNING', 'short' => 'LIGHT', 'banner' => 'teams/light.png', 'has_team_lead' => false],
                ['name' => 'DOXA KIDS CHURCH', 'short' => 'KIDS', 'banner' => 'teams/kids.png', 'has_team_lead' => false],
            ];

            foreach ($teams as $teamData) {
                Team::updateOrCreate(
                    ['name' => $teamData['name'], 'chapter_id' => $Chapter->id],
                    array_merge($teamData, ['chapter_id' => $Chapter->id])
                );
            }

            // -------------------------------
            // Seed 5-10 Chapter users
            // -------------------------------
            $userCount = rand(5, 10);
            for ($i = 0; $i < $userCount; $i++) {
                $user = User::firstOrCreate(
                    ['email' => $faker->unique()->safeEmail()],
                    [
                        'name' => $faker->name(),
                        'password' => Hash::make('password'),
                        'chapter_id' => $Chapter->id,
                    ]
                );
            }
        }
    }
}
