<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UsersList extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $users[] = [
                'username' => $faker->userName,
                'email' => $faker->email,
                'password' => base64_encode($faker->password),
                'fullname' => $faker->name,
                'birthdate' => $faker->date,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'avatar' => $faker->imageUrl,
                'last_ip' => $faker->ipv4,
                'status' => $faker->randomElement(['INACTIVE', 'ACTIVE', 'BANNED']),
                'login_attempt' => $faker->numberBetween(0, 5),
                'last_login' => $faker->dateTime,
                'created_at' => $faker->dateTime,
                'created_by' => $faker->name,
                'updated_at' => $faker->dateTime,
                'updated_by' => $faker->name,
            ];
        }
        $users[] = [
            'username' => 'admin',
            'email' => 'xzybit@yopmail.com',
            'password' => base64_encode('testing123'),
            'fullname' => 'Super Admin',
            'birthdate' => $faker->date,
            'phone' => $faker->phoneNumber,
            'address' => $faker->address,
            'avatar' => $faker->imageUrl,
            'last_ip' => $faker->ipv4,
            'status' => 'ACTIVE',
            'login_attempt' => $faker->numberBetween(0, 5),
            'last_login' => $faker->dateTime,
            'created_at' => $faker->dateTime,
            'created_by' => $faker->name,
            'updated_at' => $faker->dateTime,
            'updated_by' => $faker->name,
        ];
        DB::table('users')->insert($users);
    }
}
