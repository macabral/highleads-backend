<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklistas')->insert([
            [
                'texto' => 'marcoascabral@gmail.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),       
            ],
            [
                'texto' => 'mascabral@outlook.com',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),       
            ],
        ]

        
        );
    }
}