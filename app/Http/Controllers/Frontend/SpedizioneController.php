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
            'records' => Spedizione::where('cliente_id', \Auth::id())->orderByDesc('data_spedizione')->paginate(),
            'titoloPagina' => 'Spedizioni' ,
        ]);
    }
}
