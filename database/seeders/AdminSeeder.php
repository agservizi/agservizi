<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permessiAdmin = Permission::findByName('admin');
        //Utente Andrea
        $user = new User();
        $user->nome = "Andrea";
        $user->cognome = "Spotorno";
        $user->email = 'andicot@gmail.com';
        $user->telefono = '+393338484862';
        $user->password = '$2y$10$LOGbyXsR3b8VXtKrm2FhvOCZ6nQGdKitI4JYWtfFlq06XyHKD1o5q';
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->ruolo='admin';
        $user->save();

        $user->givePermissionTo($permessiAdmin);


        //Utente Test
        $user = new User();
        $user->nome = "Admin";
        $user->cognome = "Admin";
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('password');
        $user->email_verified_at = \Carbon\Carbon::now();
        $user->ruolo='admin';

        $user->save();
        $user->givePermissionTo($permessiAdmin);

    }
}
