<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Backend\CorriereController;
use App\Http\Controllers\Controller;
use App\Models\Corriere;
use App\Models\Spedizione;
use Illuminate\Http\Request;

class SpedizioneController extends Controller
{
    public function index()
    {
        return view('Frontend.Spedizione.index', [
            'records' => Spedizione::with('corriere:id,denominazione,url_tracking')
                ->with('servizio:id,descrizione')
                ->with('statoSpedizione:id,nome')
                ->with('statoSpedizione:id,nome,colore_hex')
                ->where('cliente_id', \Auth::id())
                ->latest()
                ->paginate(),
            'titoloPagina' => 'Spedizioni' ,
        ]);
    }
}
