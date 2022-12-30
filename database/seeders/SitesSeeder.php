<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sites')->insert([
            [
                'pagina' => 'https://zoit.com.br/consultoria/',
                'responsavel' => 'José da Silva',
                'email'  => 'teste@teste.com',
                'telefone' => '21999999999', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),       
            ]
        ]

        
        );
    }
}
