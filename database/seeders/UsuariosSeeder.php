<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('usuarios')->insert([
            [
                'nome' => 'Teste',
                'email'  => 'teste@gmail.com',
                'password' => '123456',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
            ]);
    }
}
