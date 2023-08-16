<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Servizio;
use DB;

class ServizioController extends Controller
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

            'nominativo' => ['testo' => 'Nominativo', 'filtro' => function ($q) {
                return $q->orderBy('cognome')->orderBy('nome');
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
                'html' => base64_encode(view('Backend.Servizio.tabella', [
                    'records' => $records,
                    'controller' => $nomeClasse,
                ]))
            ];
        }

        return view('Backend.Servizio.index', [
            'records' => $records,
            'controller' => $nomeClasse,
            'titoloPagina' => 'Elenco ' . \App\Models\Servizio::NOME_PLURALE,
            'orderBy' => $orderBy,
            'ordinamenti' => $ordinamenti,
            'filtro' => $filtro ?? 'tutti',
            'conFiltro' => $this->conFiltro,
            'testoNuovo' => 'Nuovo ' . \App\Models\Servizio::NOME_SINGOLARE,
            'testoCerca' => null,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applicaFiltri($request)
    {

        $queryBuilder = \App\Models\Servizio::query();
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
        $record = new Servizio();
        $record->corriere_id = \request()->input('corriere_id');
        $record->abilitato = 1;
        return view('Backend.Servizio.edit', [
            'record' => $record,
            'titoloPagina' => 'Nuovo ' . Servizio::NOME_SINGOLARE,
            'controller' => get_class($this),
            'breadcrumbs' => [action([CorriereController::class, 'show'],$record->corriere_id) => 'Torna al corriere'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(null));
        $record = new Servizio();
        $this->salvaDati($record, $request);
        return $this->backToIndex($record->corriere_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = Servizio::find($id);
        abort_if(!$record, 404, 'Questo servizio non esiste');
        return view('Backend.Servizio.show', [
            'record' => $record,
            'controller' => ServizioController::class,
            'titoloPagina' => ucfirst(Servizio::NOME_SINGOLARE),
            'breadcrumbs' => [action([ServizioController::class, 'index']) => 'Torna a elenco ' . Servizio::NOME_PLURALE],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = Servizio::find($id);
        abort_if(!$record, 404, 'Questo servizio non esiste');
        if (false) {
            $eliminabile = 'Non eliminabile perchè presente in ...';
        } else {
            $eliminabile = true;
        }
        return view('Backend.Servizio.edit', [
            'record' => $record,
            'controller' => ServizioController::class,
            'titoloPagina' => 'Modifica ' . Servizio::NOME_SINGOLARE,
            'eliminabile' => $eliminabile,
            'breadcrumbs' => [action([CorriereController::class, 'show'],$record->corriere_id) => 'Torna al corriere'],

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = Servizio::find($id);
        abort_if(!$record, 404, 'Questo ' . Servizio::NOME_SINGOLARE . ' non esiste');
        $request->validate($this->rules($id));
        $this->salvaDati($record, $request);

        return $this->backToIndex($record->corriere_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = Servizio::find($id);
        abort_if(!$record, 404, 'Questo ' . Servizio::NOME_SINGOLARE . ' non esiste');
        $record->delete();

        return [
            'success' => true,
            'redirect' => action([ServizioController::class, 'index']),
        ];
    }

    /**
     * @param Servizio $record
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
            'corriere_id' => '',
            'descrizione' => '',
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
        return $record;
    }

    protected function backToIndex($corriereId)
    {
        return redirect()->action([CorriereController::class, 'show'],$corriereId);
    }


    protected function rules($id = null)
    {


        $rules = [
            'corriere_id' => ['required'],
            'descrizione' => ['required', 'max:255'],
            'abilitato' => ['nullable'],
        ];

        return $rules;
    }

}
