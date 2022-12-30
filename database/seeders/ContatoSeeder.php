<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('contatos')->insert([
            [
                'site' => 'https://zoit.com.br/consultoria/',
                'remoteip' => '127.0.0.1',
                'datahora' => '2022-12-13 14:50',
                'nome' => 'José da Silva',
                'email'  => 'teste@teste.com',
                'telefone' => '2199999999', 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),       
            ]

        ]

        
        );
    }
}
