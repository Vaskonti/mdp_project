<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('mysql')->table('categories')->insert([
            'category' => 'A',
            'created_at' => now()
        ]);
        DB::connection('mysql')->table('categories')->insert([
            'category' => 'B',
            'created_at' => now()
        ]);
        DB::connection('mysql')->table('categories')->insert([
            'category' => 'C',
            'created_at' => now()
        ]);
    }
}
