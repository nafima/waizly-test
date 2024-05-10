<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolesList extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'ADMIN',
                'description' => 'Administrator',
            ],
            [
                'name' => 'USER',
                'description' => 'User',
            ],
        ];
        DB::table('roles')->insert($roles);
    }
}
