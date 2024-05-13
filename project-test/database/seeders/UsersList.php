<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use App\Models\Roles;

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
                'role_id' => Roles::where('name', 'USER')->first()->id,
                'username' => $faker->userName,
                'email' => $faker->email,
                'password' => Hash::make($faker->password),
                'fullname' => $faker->name,
                'birthdate' => $faker->date,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'avatar' => $faker->imageUrl,
                'last_ip' => $faker->ipv4,
                'language' => $faker->randomElement(['EN', 'ID']),
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
            'role_id' => Roles::where('name', 'ADMIN')->first()->id,
            'username' => 'admin',
            'email' => 'xzybit@yopmail.com',
            'password' => Hash::make('testing123'),
            'fullname' => 'Super Admin',
            'birthdate' => $faker->date,
            'phone' => $faker->phoneNumber,
            'address' => $faker->address,
            'avatar' => $faker->imageUrl,
            'last_ip' => $faker->ipv4,
            'language' => 'EN',
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
