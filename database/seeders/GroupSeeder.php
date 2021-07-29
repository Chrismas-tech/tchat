<?php

namespace Database\Seeders;

use App\Models\MessageGroup;
use App\Models\MessageGroupMember;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $group_id = 1;
        $group = [
            'name' => 'Axel Group 3 persons',
            'user_id' => '1',
        ];

        MessageGroup::create($group);

        $group_members = [
            [
                'message_group_id' => $group_id,
                'user_id' => 2,
            ],

            [
                'message_group_id' => $group_id,
                'user_id' => 3,
            ],

        ];

        foreach ($group_members as $member) {
            MessageGroupMember::create($member);
        }
    }
}
