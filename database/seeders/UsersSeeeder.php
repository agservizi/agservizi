<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users=\DB::table('users_old')->get();
        foreach ($users as $user){
            $u=new User();
            $u->name=$user->first_name;
            $u->cognome=$user->sur_name;
            $u->email=$user->e_mail;
            $u->password=$user->password;
            $u->email_verified_at=$user->email_verificated;
            $u->created_at=$user->created_at;
            $u->save();
        }
    }
}
