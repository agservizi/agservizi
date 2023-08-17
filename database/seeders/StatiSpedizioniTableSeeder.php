<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StatiSpedizioniTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('stati_spedizioni')->delete();
        
        \DB::table('stati_spedizioni')->insert(array (
            0 => 
            array (
                'id' => 'consegnato',
                'created_at' => '2023-08-16 23:29:55',
                'updated_at' => '2023-08-17 19:16:35',
                'nome' => 'Consegnato',
                'colore_hex' => '#77bb41',
                'primo_stato' => 0,
            ),
            1 => 
            array (
                'id' => 'in-gestione',
                'created_at' => '2023-08-16 23:29:27',
                'updated_at' => '2023-08-17 19:16:51',
                'nome' => 'In Agenzia',
                'colore_hex' => '#0061ff',
                'primo_stato' => 1,
            ),
            2 => 
            array (
                'id' => 'partito',
                'created_at' => '2023-08-16 23:29:37',
                'updated_at' => '2023-08-17 22:42:50',
                'nome' => 'Ritardo',
                'colore_hex' => '#e30d0d',
                'primo_stato' => 0,
            ),
            3 => 
            array (
                'id' => 'ritirato-dal-corriere',
                'created_at' => '2023-08-16 23:30:01',
                'updated_at' => '2023-08-17 22:39:50',
                'nome' => 'Ritirato Dal Corriere',
                'colore_hex' => '#0de79f',
                'primo_stato' => 0,
            ),
        ));
        
        
    }
}