<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $branches = [
            [
                'branch_name' => 'Aizawl',
            ],
            [
                'branch_name'=> 'Serkawn',
            ],
        ];
        Branch::insert($branches);
    }
}
