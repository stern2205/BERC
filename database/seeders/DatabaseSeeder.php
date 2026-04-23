<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin Co-Chair',
                'email' => 'admin@test.com',
                'email_verified_at' => '2026-02-11 01:07:07',
                'password' => Hash::make('admin'), // hashed password
                'remember_token' => Str::random(60),
                'created_at' => '2026-02-11 01:07:07',
                'updated_at' => '2026-02-11 06:49:16',
                'role' => 'chair',
                'profile_image' => null, // can set default image here
                'is_first_login' => true,
            ],
        ]);
    }
}
