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
                'created_at' => '2023-08-16 21:31:12',
                'updated_at' => '2023-08-17 22:31:16',
                'denominazione' => 'Poste Italiane',
                'logo' => '/loghi_corrieri/1774502098842766.png',
                'url_tracking' => 'https://www.poste.it/cerca/index.html#/risultati-spedizioni/',
                'abilitato' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'created_at' => '2023-08-16 21:31:21',
                'updated_at' => '2023-08-17 22:32:01',
                'denominazione' => 'Sda',
                'logo' => '/loghi_corrieri/1774502146143581.png',
                'url_tracking' => 'https://www.poste.it/cerca/index.html#/risultati-spedizioni/',
                'abilitato' => 1,
            ),
            2 => 
            array (
                'id' => 5,
                'created_at' => '2023-08-16 23:35:00',
                'updated_at' => '2023-08-17 22:31:31',
                'denominazione' => 'Brt',
                'logo' => '/loghi_corrieri/1774502115096821.png',
                'url_tracking' => 'https://services.brt.it/it/tracking?OP=N&CD=',
                'abilitato' => 1,
            ),
            3 => 
            array (
                'id' => 6,
                'created_at' => '2023-08-16 23:50:54',
                'updated_at' => '2023-08-17 22:31:47',
                'denominazione' => 'Tnt Fedex',
                'logo' => '/loghi_corrieri/1774502132020353.png',
                'url_tracking' => 'https://www.tnt.it/tracking/getTrack.html?wt=1&consigNos=',
                'abilitato' => 1,
            ),
        ));
        
        
    }
}