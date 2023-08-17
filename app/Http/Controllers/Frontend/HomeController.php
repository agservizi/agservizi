<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Controller;
use App\Models\AllegatoCondominio;
use App\Models\Condominio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{

    public function home()
    {
        if (Auth::user()->hasPermissionTo('admin')) {
            return redirect()->action([DashboardController::class, 'show']);
        } else {
            return view('Frontend.Dashboard.show');
        }
    }

    public function index()
    {
        if (Auth::user()->hasPermissionTo('admin')) {
            return redirect()->action([DashboardController::class, 'show']);
        }

        $condominii = Condominio::whereRelation('condomini', 'users.id', Auth::id())->get();
        if ($condominii->count() == 1) {
            return view('Frontend.show', [
                'condominio' => $condominii[0]
            ]);
        }

        $condominioId = \request()->input('condominio_id', $condominii[0]->id);
        $condominio = $condominii->where('id', $condominioId)->first();
        abort_if(!$condominio, 404);
        return view('Frontend.index', [
            'condominio' => $condominio,
            'condominii' => $condominii,
            'condominioId' => $condominioId
        ]);
    }

    public function downloadFile($id)
    {
        $record = AllegatoCondominio::find($id);
        abort_if(!$record, 404);
        return response()->download(Storage::path($record->path_filename), $record->filename_originale);
    }
}
