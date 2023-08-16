<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CorrieriTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('corrieri')->delete();
        
        \DB::table('corrieri')->insert(array (
            0 => 
            array (
                'id' => 1,
                'created_at' => '2023-08-16 19:31:12',
                'updated_at' => '2023-08-16 19:31:12',
                'denominazione' => 'Poste Italiane',
                'logo' => NULL,
                'url_tracking' => NULL,
                'abilitato' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'created_at' => '2023-08-16 19:31:21',
                'updated_at' => '2023-08-16 19:31:21',
                'denominazione' => 'Sda',
                'logo' => NULL,
                'url_tracking' => NULL,
                'abilitato' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'created_at' => '2023-08-16 19:31:29',
                'updated_at' => '2023-08-16 19:31:29',
                'denominazione' => 'Brt',
                'logo' => NULL,
                'url_tracking' => NULL,
                'abilitato' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'created_at' => '2023-08-16 19:31:39',
                'updated_at' => '2023-08-16 19:31:39',
                'denominazione' => 'Ups',
                'logo' => NULL,
                'url_tracking' => NULL,
                'abilitato' => 1,
            ),
        ));
        
        
    }
}