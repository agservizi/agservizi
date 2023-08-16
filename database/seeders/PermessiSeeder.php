<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermessiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'admin']);
        Permission::create(['name' => 'operatore']);
        //Permission::create(['name' => 'sospeso']);
        \Artisan::call('permission:cache-reset');

    }
}
