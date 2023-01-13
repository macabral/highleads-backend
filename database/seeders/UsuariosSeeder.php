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
                'nome' => 'Administrador',
                'email'  => 'mascabral@outlook.com',
                'password' => '123456',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'nome' => 'Zanelli',
                'email'  => 'ozanelli@zoit.com.br',
                'password' => '123456',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
            ]);
    }
}
