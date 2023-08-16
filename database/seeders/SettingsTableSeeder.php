<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('settings')->delete();
        
        \DB::table('settings')->insert(array (
            0 => 
            array (
                'created_at' => '2022-04-20 07:29:54',
                'id' => 1,
                'name' => 'prezzo_documento',
                'type' => 'float',
                'updated_at' => '2022-04-20 17:27:53',
                'val' => '5,50',
            ),
        ));
        
        
    }
}