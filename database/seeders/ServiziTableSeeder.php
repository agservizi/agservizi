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
                'id' => 2,
                'created_at' => '2023-08-16 23:36:32',
                'updated_at' => '2023-08-16 23:36:32',
                'corriere_id' => 5,
                'descrizione' => 'EXPRESS',
                'abilitato' => 1,
            ),
            1 => 
            array (
                'id' => 3,
                'created_at' => '2023-08-16 23:51:17',
                'updated_at' => '2023-08-16 23:51:17',
                'corriere_id' => 6,
                'descrizione' => 'EXPRESS',
                'abilitato' => 1,
            ),
            2 => 
            array (
                'id' => 4,
                'created_at' => '2023-08-16 23:51:26',
                'updated_at' => '2023-08-16 23:51:26',
                'corriere_id' => 6,
                'descrizione' => 'ECONOMY EXPRESS',
                'abilitato' => 1,
            ),
            3 => 
            array (
                'id' => 7,
                'created_at' => '2023-08-16 23:59:17',
                'updated_at' => '2023-08-16 23:59:17',
                'corriere_id' => 1,
                'descrizione' => 'POSTA1',
                'abilitato' => 1,
            ),
            4 => 
            array (
                'id' => 8,
                'created_at' => '2023-08-16 23:59:31',
                'updated_at' => '2023-08-16 23:59:31',
                'corriere_id' => 1,
                'descrizione' => 'RACCOMANDATA AR',
                'abilitato' => 1,
            ),
            5 => 
            array (
                'id' => 9,
                'created_at' => '2023-08-16 23:59:39',
                'updated_at' => '2023-08-16 23:59:39',
                'corriere_id' => 1,
                'descrizione' => 'RACCOMANDATA SEMPLICE',
                'abilitato' => 1,
            ),
            6 => 
            array (
                'id' => 10,
                'created_at' => '2023-08-16 23:59:52',
                'updated_at' => '2023-08-16 23:59:52',
                'corriere_id' => 2,
                'descrizione' => 'EXPRESS',
                'abilitato' => 1,
            ),
            7 => 
            array (
                'id' => 11,
                'created_at' => '2023-08-16 23:59:57',
                'updated_at' => '2023-08-16 23:59:57',
                'corriere_id' => 2,
                'descrizione' => 'STANDARD',
                'abilitato' => 1,
            ),
        ));
        
        
    }
}