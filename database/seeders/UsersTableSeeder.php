<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
Use DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([

            [
                'name' => 'User 1',
                'email' => 'user1@gmail.com',
                'password' => Hash::make('12345'),

            ],

              [
                'name' => 'User 2',
                'email' => 'user2@gmail.com',
                'password' => Hash::make('12345'),

            ],


            [
                'name' => 'User 3',
                'email' => 'user3@gmail.com',
                'password' => Hash::make('12345'),
            ],

        ]);
    }
}
