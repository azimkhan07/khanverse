<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TeamMember;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            [
                'name'  => 'Azim Khan',
                'role'  => 'Founder of SkillNest',
                'tagline' => 'Brand Partner with Amtech',
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'name'  => 'Nadeem Mansuri',
                'role'  => 'Co-Founder of SkillNest',
                'tagline' => null,
                'sort_order' => 2,
                'status' => true,
            ],
            [
                'name'  => 'Sayed Mujeeb',
                'role'  => 'Brand Partner with SkillNest',
                'tagline' => 'Director of Amtech',
                'sort_order' => 3,
                'status' => true,
            ],
        ];

        foreach ($members as $member) {
            TeamMember::updateOrCreate(
                ['name' => $member['name']],
                array_merge($member, ['image' => null])
            );
        }
    }
}