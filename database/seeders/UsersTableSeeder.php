<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'user_type'=>1,
                'branch_id'=>'1',
            ],
            [
                'name' => 'Agent',
                'email' => 'agent@agent.com',
                'password' => bcrypt('password'),
                'remember_token' => null,
                'user_type'=>0,
                'branch_id'=>'2',
            ],
        ];

        User::insert($users);
    }
}
