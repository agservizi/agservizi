<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use function redirect;
use function Session;

class LogOut
{

    public function logOut(){

        Auth::logout();
        return redirect('/login');

    }
}
