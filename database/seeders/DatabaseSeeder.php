<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {


        $this->call(ElencoNazioniTableSeeder::class);
        $this->call(ElencoProvinceTableSeeder::class);
        $this->call(ElencoComuniTableSeeder::class);

        $this->call(CorrieriTableSeeder::class);


        $this->call(PermessiSeeder::class);
        $this->call(AdminSeeder::class);



        $this->call(ServiziTableSeeder::class);
        $this->call(StatiSpedizioniTableSeeder::class);
    }
}
