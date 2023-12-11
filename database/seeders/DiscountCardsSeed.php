<?php

declare(strict_types = 1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class DiscountCardsSeed extends Seeder
{

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::connection('mysql')->table('discount_cards')->insert([
           'discount' => 0.10,
           'type' => 'Silver',
        ]);
        DB::connection('mysql')->table('discount_cards')->insert([
            'discount' => 0.15,
            'type' => 'Gold',
        ]);
        DB::connection('mysql')->table('discount_cards')->insert([
            'discount' => 0.20,
            'type' => 'Platinum',
        ]);
    }

}
