<?php

declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use function now;

final class CategorySeeder extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('mysql')->table('categories')->insert([
            'category' => 'A',
            'created_at' => now(),
        ]);
        DB::connection('mysql')->table('categories')->insert([
            'category' => 'B',
            'created_at' => \now(),
        ]);
        DB::connection('mysql')->table('categories')->insert([
            'category' => 'C',
            'created_at' => \now(),
        ]);
    }

}
