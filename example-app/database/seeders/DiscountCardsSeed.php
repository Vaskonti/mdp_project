<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountCardsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection('mysql')->table('discount_cards')->insert([
           'type' => 'Silver',
           'discount' => 0.10
        ]);
        DB::connection('mysql')->table('discount_cards')->insert([
            'type' => 'Gold',
            'discount' => 0.15
        ]);
        DB::connection('mysql')->table('discount_cards')->insert([
            'type' => 'Platinum',
            'discount' => 0.20
        ]);
    }
}
