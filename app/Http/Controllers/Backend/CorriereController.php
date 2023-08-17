<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Servizio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Corriere;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use function App\singolareOplurale;

class CorriereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    protected $conFiltro = false;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $nomeClasse = get_class($this);
        $recordsQB = $this->applicaFiltri($request);

        $ordinamenti = [
            'recente' => ['testo' => 'Più recente', 'filtro' => function ($q) {
                return $q->orderBy('id', 'desc');
            }],

            'denominazione' => ['testo' => 'Denominazione', 'filtro' => function ($q) {
                return $q->orderBy('denominazione');
            }]
        ];

        $orderByUser = Auth::user()->getExtra($nomeClasse);
        $orderByString = $request->input('orderBy');

        if ($orderByString) {
            $orderBy = $orderByString;
        } else if ($orderByUser) {
            $orderBy = $orderByUser;
        } else {
            $orderBy = 'recente';
        }

        if ($orderByUser != $orderByString) {
            Auth::user()->setExtra([$nomeClasse => $orderBy]);
        }

        //Applico ordinamento
        $recordsQB = call_user_func($ordinamenti[$orderBy]['filtro'], $recordsQB);

        $records = $recordsQB->paginate(25)->withQueryString();

        if ($request->ajax()) {
            return [
                'html' => base64_encode(view('Backend.Corriere.tabella', [
                    'records' => $records,
                    'controller' => $nomeClasse,
                ]))
            ];
        }

        return view('Backend.Corriere.index', [
            'records' => $records,
            'controller' => $nomeClasse,
            'titoloPagina' => 'Elenco ' . \App\Models\Corriere::NOME_PLURALE,
            'orderBy' => $orderBy,
            'ordinamenti' => $ordinamenti,
            'filtro' => $filtro ?? 'tutti',
            'conFiltro' => $this->conFiltro,
            'testoNuovo' => 'Nuovo ' . \App\Models\Corriere::NOME_SINGOLARE,
            'testoCerca' => null,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applicaFiltri($request)
    {

        $queryBuilder = \App\Models\Corriere::query();
        $term = $request->input('cerca');
        if ($term) {
            $arrTerm = explode(' ', $term);
            foreach ($arrTerm as $t) {
                $queryBuilder->where(DB::raw('concat_ws(\' \',nome)'), 'like', "%$t%");
            }
        }

        //$this->conFiltro = true;
        return $queryBuilder;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $record = new Corriere();
        $record->abilitato = 1;
        return view('Backend.Corriere.edit', [
            'record' => $record,
            'titoloPagina' => 'Nuovo ' . Corriere::NOME_SINGOLARE,
            'controller' => get_class($this),
            'breadcrumbs' => [action([CorriereController::class, 'index']) => 'Torna a elenco ' . Corriere::NOME_PLURALE],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(null));
        $record = new Corriere();
        $this->salvaDati($record, $request);
        return $this->backToIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = Corriere::find($id);
        abort_if(!$record, 404, 'Questo corriere non esiste');
        return view('Backend.Corriere.show', [
            'records' => Servizio::where('corriere_id', $id)->orderBy('descrizione')->get(),
            'record' => $record,
            'controller' => CorriereController::class,
            'titoloPagina' => ucfirst(Corriere::NOME_SINGOLARE),
            'breadcrumbs' => [action([CorriereController::class, 'index']) => 'Torna a elenco ' . Corriere::NOME_PLURALE],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = Corriere::withCount('spedizioni')->find($id);


        abort_if(!$record, 404, 'Questo corriere non esiste');

        if ($record->spedizioni_count) {
            $eliminabile = 'Non eliminabile perchè presente in ' . singolareOplurale($record->spedizioni_count, 'spedizione', 'spedizioni');
        } else {
            $eliminabile = true;
        }
        return view('Backend.Corriere.edit', [
            'record' => $record,
            'controller' => CorriereController::class,
            'titoloPagina' => 'Modifica ' . Corriere::NOME_SINGOLARE,
            'eliminabile' => $eliminabile,
            'breadcrumbs' => [action([CorriereController::class, 'index']) => 'Torna a elenco ' . Corriere::NOME_PLURALE]

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = Corriere::find($id);
        abort_if(!$record, 404, 'Questo ' . Corriere::NOME_SINGOLARE . ' non esiste');
        $request->validate($this->rules($id));
        $this->salvaDati($record, $request);

        return $this->backToIndex();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = Corriere::find($id);
        abort_if(!$record, 404, 'Questo ' . Corriere::NOME_SINGOLARE . ' non esiste');
        $record->delete();

        return [
            'success' => true,
            'redirect' => action([CorriereController::class, 'index']),
        ];
    }

    /**
     * @param Corriere $record
     * @param Request $request
     * @return mixed
     */
    protected function salvaDati($record, $request)
    {

        $nuovo = !$record->id;

        if ($nuovo) {

        }

        //Ciclo su campi
        $campi = [
            'denominazione' => 'app\getInputUcwords',
            'logo' => '',
            'url_tracking' => 'app\getInputHttps',
            'abilitato' => 'app\getInputCheckbox',
        ];
        foreach ($campi as $campo => $funzione) {
            $valore = $request->$campo;
            if ($funzione != '') {
                $valore = $funzione($valore);
            }
            $record->$campo = $valore;
        }

        $record->save();

        if ($request->file('logo')) {
            $tmpFile = $request->file('logo');
            $extensione = $tmpFile->extension();
            $filename = hexdec(uniqid()) . '.' . $extensione;
            if ($record->logo && Storage::exists($record->logo)) {
                Storage::delete($record->logo);
            }

            $fileImmagine = $this->salvaImmagine($tmpFile, $filename, true);
            $record->logo = $fileImmagine;
            $record->save();

        }
        return $record;
    }

    protected function backToIndex()
    {
        return redirect()->action([get_class($this), 'index']);
    }


    protected function rules($id = null)
    {


        $rules = [
            'denominazione' => ['required', 'max:255'],
            'logo' => ['nullable', 'max:255'],
            'url_tracking' => ['nullable', 'max:255'],
            'abilitato' => ['nullable'],
        ];

        return $rules;
    }


    protected function salvaImmagine($tmpFile, $nomefile, $canvas = false)
    {

        $cartella = config('configurazione.loghi_corrieri.cartella');
        if (!Storage::exists($cartella)) {
            Storage::makeDirectory($cartella);
        }


        $img = Image::make($tmpFile);
        $dimensioni = config('configurazione.loghi_corrieri.dimensioni');
        //$img->fit($dimensioni['width'], $dimensioni['height'], null, 'center');
        $img = $this->ridimensionaImmagine($img, $dimensioni['width'], $dimensioni['height'], $canvas, 'normale');
        $img->save(Storage::path($cartella . '/' . $nomefile), 80);


        return $cartella . '/' . $nomefile;

    }

    protected function ridimensionaImmagine($img, $width, $height, $canvas, $testoLog)
    {
        //Resize immagine
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        //Aggiusta rapporto immagine
        Log::debug("Immagine $testoLog {$img->width()}x{$img->height()}");
        if ($canvas) {

            if ($img->height() < $height || $img->width() < $width) {
                \Log::debug('Aggiusto rapporto');
                $img->resizeCanvas($width, $height, 'center', false, 'ffffff');
            }
        }

        Log::debug("Immagine $testoLog finale: {$img->width()}x{$img->height()}");

        return $img;

    }


}
