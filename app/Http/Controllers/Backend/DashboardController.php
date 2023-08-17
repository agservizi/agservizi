<?php

namespace App\Http\Controllers\Backend;

use App\Http\MieClassiCache\CacheUnaVoltaAlGiorno;
use App\Models\Spedizione;
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
        $grafico = $this->reportAnno($request->input('anno', now()->year));
        return view('Backend.Dashboard.show', compact( 'titoloPagina','grafico' ));


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

    private function reportAnno($anno)
    {
        $perMese = Spedizione::selectRaw('MONTH(created_at) as mese,YEAR(created_at) as anno, count(*) as conteggio ')->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->whereYear('created_at', $anno)
            ->get();


        $arrDati = [];
        for ($n = 1; $n <= 12; $n++) {
            $datiMese = $perMese->where('mese', $n)->first();
            $arrDati['conteggio'][] = $datiMese?->conteggio ?? '';
            $arrDati['labels'][] = mese($n);
        }


        return [
            'anno' => $anno,
            'arrDati' => $arrDati,
            'elencoAnni' => $this->elencoAnni(),
        ];
    }

    private function elencoAnni(): array
    {
        $anni = [];
        for ($anno = config('configurazione.primoAnno'); $anno <= now()->year; $anno++) {
            $anni[] = $anno;
        }
        return $anni;
    }


}
