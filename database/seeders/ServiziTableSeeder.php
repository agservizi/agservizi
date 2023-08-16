<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiziTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('servizi')->delete();
        
        \DB::table('servizi')->insert(array (
            0 => 
            array (
                'id' => 1,
                'created_at' => '2023-08-16 19:38:21',
                'updated_at' => '2023-08-16 19:38:21',
                'corriere_id' => 3,
                'descrizione' => 'spedizione veloce',
                'abilitato' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'created_at' => '2023-08-16 19:38:30',
                'updated_at' => '2023-08-16 19:38:30',
                'corriere_id' => 3,
                'descrizione' => 'spedizione lenta',
                'abilitato' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'created_at' => '2023-08-16 19:38:41',
                'updated_at' => '2023-08-16 19:38:41',
                'corriere_id' => 3,
                'descrizione' => 'spedizione che si perde',
                'abilitato' => 1,
            ),
        ));
        
        
    }
}