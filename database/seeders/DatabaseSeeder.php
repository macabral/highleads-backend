<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SitesSeeder::class);
        $this->call(ContatoSeeder::class);
        // $this->call(BlacklistasSeeder::class);
        // $this->call(EmailsSeeder::class);
    }
}
