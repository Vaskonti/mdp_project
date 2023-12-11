<?php

declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class AdminSeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('mysql')->table('users')->insert([
            'email' => 'vasil.hristov@lab08.com',
            'name' => 'Admin',
            'password' => Hash::make('passwd'),
        ]);
    }

}
