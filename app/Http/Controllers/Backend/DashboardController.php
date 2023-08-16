<?php

namespace App\Http\Controllers\Backend;

use App\Http\MieClassiCache\CacheUnaVoltaAlGiorno;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use function App\mese;

class DashboardController extends Controller
{
    public function show(Request $request)
    {


        dispatch(function () {
            CacheUnaVoltaAlGiorno::get();
        })->afterResponse();

        $titoloPagina = 'Dashboard';
        return view('Backend.Dashboard.show', compact( 'titoloPagina', ));


    }

    protected function showAdmin()
    {
        return view('Backend.Dashboard.showAdmin', [
            'titoloPagina' => 'Dashboard ',
            'mainMenu' => 'dashboard',
        ]);

    }

    protected function showUtente()
    {


        return view('Backend.Dashboard.showUtente', [
            'titoloPagina' => 'Dashboard ',
            'mainMenu' => 'dashboard',
        ]);

    }


}
