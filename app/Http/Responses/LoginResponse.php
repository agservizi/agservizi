<?php

namespace App\Http\Responses;

use App\Http\Controllers\Backend\DashboardController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{

    public function toResponse($request)
    {

        // below is the existing response
        // replace this with your own code
        // the user can be located with Auth facade

        if ($request->has('backTo')) {
            return redirect($request->input('backTo'));
        }

        if (Auth::user()->hasPermissionTo('admin') ) {
            $redirectTo = action([DashboardController::class, 'show']);
        } else {
            $redirectTo = config('fortify.home');
        }


        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended($redirectTo);
    }

}
