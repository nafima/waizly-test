<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivitiesList extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activities = [
            [
                'code' => 'LOGIN_APP',
                'name' => 'LOGIN',
                'description' => 'Login to application',
            ],
            [
                'code' => 'LOGOUT_APP',
                'name' => 'LOGOUT',
                'description' => 'Logout from application',
            ],
            [
                'code' => 'CREATE_USER',
                'name' => 'CREATE USER DATA',
                'description' => 'Create user data',
            ],
            [
                'code' => 'UPDATE_USER',
                'name' => 'UPDATE USER DATA',
                'description' => 'Update user data',
            ],
            [
                'code' => 'DELETE_USER',
                'name' => 'DELETE USER DATA',
                'description' => 'Delete user data',
            ],
            [
                'code' => 'CREATE_ROLE',
                'name' => 'CREATE ROLE DATA',
                'description' => 'Create role data',
            ],
            [
                'code' => 'UPDATE_ROLE',
                'name' => 'UPDATE ROLE DATA',
                'description' => 'Update role data',
            ],
            [
                'code' => 'DELETE_ROLE',
                'name' => 'DELETE ROLE DATA',
                'description' => 'Delete role data',
            ],
            [
                'code' => 'CREATE_ACTIVITY',
                'name' => 'CREATE ACTIVITY DATA',
                'description' => 'Create activity data',
            ],
            [
                'code' => 'UPDATE_ACTIVITY',
                'name' => 'UPDATE ACTIVITY DATA',
                'description' => 'Update activity data',
            ],
            [
                'code' => 'DELETE_ACTIVITY',
                'name' => 'DELETE ACTIVITY DATA',
                'description' => 'Delete activity data',
            ]
        ];
        DB::table('activities')->insert($activities);
    }
}
