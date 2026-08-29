<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name' => 'Bonnie Green',
                'position' => 'Senior Front-end Developer',
                'description' => 'Supports digital product development and user experience across internal and client-facing platforms.',
                'photo_path' => 'images/team/bonnie-green.svg',
            ],
            [
                'name' => 'Thomas Lean',
                'position' => 'Engineering Manager',
                'description' => 'Coordinates multidisciplinary engineering activities and ensures solutions meet technical requirements.',
                'photo_path' => 'images/team/thomas-lean.svg',
            ],
            [
                'name' => 'Jese Leos',
                'position' => 'Project Engineer',
                'description' => 'Works with project teams from site assessment and design through construction and commissioning.',
                'photo_path' => 'images/team/jese-leos.svg',
            ],
            [
                'name' => 'Leslie Livingston',
                'position' => 'Business Development',
                'description' => 'Helps clients explore practical renewable energy solutions aligned with operational and sustainability goals.',
                'photo_path' => 'images/team/leslie-livingston.svg',
            ],
        ];

        foreach ($members as $index => $member) {
            TeamMember::query()->updateOrCreate(
                ['name' => $member['name']],
                array_merge($member, [
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ])
            );
        }
    }
}
