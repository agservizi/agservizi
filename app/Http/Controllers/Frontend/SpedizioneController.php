<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Backend\CorriereController;
use App\Http\Controllers\Controller;
use App\Models\AllegatoSpedizione;
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
                ->with('letteraDiVettura:id,spedizione_id')
                ->with('pod:id,spedizione_id')
                ->where('cliente_id', \Auth::id())
                ->latest()
                ->paginate(),
            'titoloPagina' => 'Spedizioni',
        ]);
    }

    public function downloadAllegato($id)
    {
        $record = AllegatoSpedizione::with('spedizione')->find($id);
        abort_if(!$record, 404, 'Questo allegato non esiste');
        abort_if($record->spedizione->cliente_id !== \Auth::id(), 404, 'Questo allegato non esiste');

        return response()->download(\Storage::path($record->path_filename), $record->filename_originale);

    }
}
