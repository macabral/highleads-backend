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
                'pagina' => 'minhapagina.com.br',
                'responsavel' => 'Menu Nome',
                'email'  => 'meuemail@dominio.com',
                'telefone' => '21999999999', 
                'ativo' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),       
            ],
        ]

        
        );
    }
}
