<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        DB::table('emails')->insert([
            [
                'para' => 'teste@teste.com',
                'cc' => '',
                'bcc' => '',
                'assunto' => 'Teste envio de email',
                'texto'  => '<h1>Teste de envio de emails</h1>',
                'anexos' => '{}',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),       
            ],
        ]

        );
    }
}
