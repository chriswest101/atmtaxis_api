<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('configs')->insert([
            'name' => 'MINIMUM_DISTANCE',
            'value' => '4'
        ]);
        DB::table('rates')->insert([
            'name' => 'DAY',
            'value' => '1.45'
        ]);
        DB::table('price_multipliers')->insert([
            'name' => 'LOWER',
            'value' => '0.85'
        ]);
        DB::table('price_multipliers')->insert([
            'name' => 'UPPER',
            'value' => '1.4'
        ]);
    }
}
