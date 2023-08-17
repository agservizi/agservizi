<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\MieClassi\DatiRitorno;
use App\Models\Cliente;
use App\Models\StatoSpedizione;
use App\Models\User;
use App\Notifications\NotificaAlNuovoCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Spedizione;
use DB;
use Illuminate\Testing\Fluent\Concerns\Has;

class SpedizioneController extends Controller
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
                'html' => base64_encode(view('Backend.Spedizione.tabella', [
                    'records' => $records,
                    'controller' => $nomeClasse,
                ]))
            ];
        }

        return view('Backend.Spedizione.index', [
            'records' => $records,
            'controller' => $nomeClasse,
            'titoloPagina' => 'Elenco ' . \App\Models\Spedizione::NOME_PLURALE,
            'orderBy' => $orderBy,
            'ordinamenti' => $ordinamenti,
            'filtro' => $filtro ?? 'tutti',
            'conFiltro' => $this->conFiltro,
            'testoNuovo' => 'Nuova ' . \App\Models\Spedizione::NOME_SINGOLARE,
            'testoCerca' => 'Cerca in cliente',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applicaFiltri($request)
    {

        $queryBuilder = \App\Models\Spedizione::query()
            ->with('corriere:id,denominazione,url_tracking')
            ->with('servizio:id,descrizione')
            ->with('statoSpedizione:id,nome')
            ->with('cliente:id,cognome,nome')
            ->with('statoSpedizione:id,nome,colore_hex');
        $term = $request->input('cerca');
        if ($term) {
            $queryBuilder->whereHas('cliente', function ($q) use ($term) {
                $arrTerm = explode(' ', $term);
                foreach ($arrTerm as $t) {
                    $q->where(DB::raw('concat_ws(\' \',cognome,nome)'), 'like', "%$t%");
                }
            });
        }

        //$this->conFiltro = true;
        return $queryBuilder;
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $record = new Spedizione();
        $record->data_spedizione = today();
        return view('Backend.Spedizione.edit', [
            'record' => $record,
            'titoloPagina' => 'Nuova ' . Spedizione::NOME_SINGOLARE,
            'controller' => get_class($this),
            'breadcrumbs' => [action([SpedizioneController::class, 'index']) => 'Torna a elenco ' . Spedizione::NOME_PLURALE],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(null));
        DB::beginTransaction();
        $record = new Spedizione();
        $inviaMail = false;
        if (!$request->input('cliente_id')) {
            $inviaMail = true;
            $password = \Str::random(8);
            $user = $this->creaCliente(new Cliente(), $request, $password);
            $record->cliente_id = $user->id;
        } else {
            $record->cliente_id = $request->input('cliente_id');
        }
        $record->stato_spedizione = StatoSpedizione::where('primo_stato', 1)->first()->id;
        $this->salvaDati($record, $request);
        DB::commit();
        if ($inviaMail) {
            dispatch(function () use ($user, $password) {
                $user->notify(new NotificaAlNuovoCliente($password));

            })->afterResponse();
        }

        return $this->backToIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = Spedizione::find($id);
        abort_if(!$record, 404, 'Questa spedizione non esiste');
        return view('Backend.Spedizione.show', [
            'record' => $record,
            'controller' => SpedizioneController::class,
            'titoloPagina' => ucfirst(Spedizione::NOME_SINGOLARE),
            'breadcrumbs' => [action([SpedizioneController::class, 'index']) => 'Torna a elenco ' . Spedizione::NOME_PLURALE],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = Spedizione::find($id);
        abort_if(!$record, 404, 'Questa spedizione non esiste');
        if (false) {
            $eliminabile = 'Non eliminabile perchè presente in ...';
        } else {
            $eliminabile = true;
        }
        return view('Backend.Spedizione.edit', [
            'record' => $record,
            'controller' => SpedizioneController::class,
            'titoloPagina' => 'Modifica ' . Spedizione::NOME_SINGOLARE,
            'eliminabile' => $eliminabile,
            'breadcrumbs' => [action([SpedizioneController::class, 'index']) => 'Torna a elenco ' . Spedizione::NOME_PLURALE]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = Spedizione::find($id);
        abort_if(!$record, 404, 'Questa ' . Spedizione::NOME_SINGOLARE . ' non esiste');
        $request->validate($this->rules($id));
        $this->salvaDati($record, $request);

        return $this->backToIndex();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = Spedizione::find($id);
        abort_if(!$record, 404, 'Questa ' . Spedizione::NOME_SINGOLARE . ' non esiste');
        $record->delete();

        return [
            'success' => true,
            'redirect' => action([SpedizioneController::class, 'index']),
        ];
    }


    public function modalCambiaStato($spedizioneId)
    {

        $record = Spedizione::find($spedizioneId);
        abort_if(!$record, 404, 'Questa spedizione non esiste');

        return view('Backend.Spedizione.modalCambiaStato', [
            'titoloPagina' => 'Cambia stato ',
            'record' => $record,
        ]);
    }

    public function updateStato(Request $request, $id)
    {
        $record = Spedizione::with('statoSpedizione')->find($id);
        abort_if(!$record, 404);
        $stato = StatoSpedizione::find($request->input('stato_spedizione'));
        $record->stato_spedizione = $stato->id;
        $record->save();


        $datiRitorno = new DatiRitorno();
        $datiRitorno->chiudiDialog(true);
        $datiRitorno->oggettoReload('stato_' . $id, $stato->badgeStato());
        return $datiRitorno->toArray();
    }

    /**
     * @param Spedizione $record
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
            'servizio_id' => '',
            'data_spedizione' => 'app\getInputData',
            'denominazione_destinatario' => '',
            'indirizzo_destinatario' => '',
            'citta_destinatario' => '',
            'cap_destinatario' => '',
            'nazione_destinatario' => '',
            'codice_tracking' => '',
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

    protected function backToIndex()
    {
        return redirect()->action([get_class($this), 'index']);
    }


    protected function rules($id = null)
    {


        $rules = [
            'corriere_id' => ['required'],
            'servizio_id' => ['required'],
            'data_spedizione' => ['nullable'],
            'denominazione_destinatario' => ['nullable', 'max:255'],
            'indirizzo_destinatario' => ['nullable', 'max:255'],
            'citta_destinatario' => ['nullable', 'max:255'],
            'cap_destinatario' => ['nullable', 'max:255'],
            'nazione_destinatario' => ['nullable', 'max:255'],
            'codice_tracking' => ['nullable', 'max:255'],
        ];

        return $rules;
    }


    /**
     * @param Cliente $record
     * @param Request $request
     * @return mixed
     */
    protected function creaCliente($record, $request, $password)
    {

        $nuovo = !$record->id;

        if ($nuovo) {

        }

        //Ciclo su campi
        $campi = [
            'nome' => 'app\getInputUcwords',
            'cognome' => 'app\getInputUcwords',
            'email' => 'strtolower',
            'telefono' => 'app\getInputData',

        ];
        foreach ($campi as $campo => $funzione) {
            $valore = $request->$campo;
            if ($funzione != '') {
                $valore = $funzione($valore);
            }
            $record->$campo = $valore;
        }


        $record->password = \Hash::make($password);
        $record->save();
        return $record;
    }

}
