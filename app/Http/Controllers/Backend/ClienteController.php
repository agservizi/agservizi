<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;

class ClienteController extends Controller
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
                'html' => base64_encode(view('Backend.Cliente.tabella', [
                    'records' => $records,
                    'controller' => $nomeClasse,
                ]))
            ];
        }

        return view('Backend.Cliente.index', [
            'records' => $records,
            'controller' => $nomeClasse,
            'titoloPagina' => 'Elenco ' . \App\Models\Cliente::NOME_PLURALE,
            'orderBy' => $orderBy,
            'ordinamenti' => $ordinamenti,
            'filtro' => $filtro ?? 'tutti',
            'conFiltro' => $this->conFiltro,
            'testoNuovo' => null,//'Nuovo '. \App\Models\Cliente::NOME_SINGOLARE,
            'testoCerca' => null,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function applicaFiltri($request)
    {

        $queryBuilder = \App\Models\Cliente::query()
            ->SoloCliente();
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
        $record = new Cliente();
        return view('Backend.Cliente.edit', [
            'record' => $record,
            'titoloPagina' => 'Nuovo ' . Cliente::NOME_SINGOLARE,
            'controller' => get_class($this),
            'breadcrumbs' => [action([ClienteController::class, 'index']) => 'Torna a elenco ' . Cliente::NOME_PLURALE],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate($this->rules(null));
        $record = new Cliente();
        $this->salvaDati($record, $request);
        return $this->backToIndex();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $record = Cliente::SoloCliente()->find($id);
        abort_if(!$record, 404, 'Questo cliente non esiste');
        return view('Backend.Cliente.show', [
            'record' => $record,
            'controller' => ClienteController::class,
            'titoloPagina' => ucfirst(Cliente::NOME_SINGOLARE),
            'breadcrumbs' => [action([ClienteController::class, 'index']) => 'Torna a elenco ' . Cliente::NOME_PLURALE],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $record = Cliente::find($id);
        abort_if(!$record, 404, 'Questo cliente non esiste');
        if (false) {
            $eliminabile = 'Non eliminabile perchè presente in ...';
        } else {
            $eliminabile = true;
        }
        return view('Backend.Cliente.edit', [
            'record' => $record,
            'controller' => ClienteController::class,
            'titoloPagina' => 'Modifica ' . Cliente::NOME_SINGOLARE,
            'eliminabile' => $eliminabile,
            'breadcrumbs' => [action([ClienteController::class, 'index']) => 'Torna a elenco ' . Cliente::NOME_PLURALE]

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $record = Cliente::find($id);
        abort_if(!$record, 404, 'Questo ' . Cliente::NOME_SINGOLARE . ' non esiste');
        $request->validate($this->rules($id));
        $this->salvaDati($record, $request);

        return $this->backToIndex();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $record = Cliente::find($id);
        abort_if(!$record, 404, 'Questo ' . Cliente::NOME_SINGOLARE . ' non esiste');
        $record->delete();

        return [
            'success' => true,
            'redirect' => action([ClienteController::class, 'index']),
        ];
    }


    public function azioni($id, $azione)
    {
        $u = User::find($id);
        if (!$u) {
            return ['success' => false, 'message' => 'Questo utente non esiste'];
        }
        switch ($azione) {

            case 'impersona':
                return $this->azioneImpersona($id);

            case 'invia-mail-password-reset':
                return $this->azioneInviaMailPassowrdReset($id);

            case 'resetta-password':
                $user = User::find($id);
                $user->password = bcrypt('123456');
                $user->save();
                return ['success' => true, 'title' => 'Password impostata', 'message' => 'La password è stata impostata a 123456'];

        }

    }

    /**
     * @param Cliente $record
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
            'nome' => 'app\getInputUcwords',
            'cognome' => 'app\getInputUcwords',
            'email' => 'strtolower',
            'email_verified_at' => '',
            'password' => '',
            'two_factor_secret' => '',
            'two_factor_recovery_codes' => '',
            'two_factor_confirmed_at' => '',
            'remember_token' => '',
            'ultimo_accesso' => '',
            'telefono' => 'app\getInputTelefono',
            'ruolo' => '',
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
            'nome' => ['required', 'max:255'],
            'cognome' => ['required', 'max:255'],
            'email' => ['required', 'email'],
            'email_verified_at' => ['nullable'],
            'password' => ['required', 'max:255'],
            'two_factor_secret' => ['nullable'],
            'two_factor_recovery_codes' => ['nullable'],
            'two_factor_confirmed_at' => ['nullable'],
            'remember_token' => ['nullable'],
            'ultimo_accesso' => ['nullable'],
            'telefono' => ['nullable', new \App\Rules\TelefonoRule()],
            'ruolo' => ['nullable', 'max:255'],
        ];

        return $rules;
    }

    protected function azioneImpersona($id)
    {

        $user = User::find($id);
        if ($user->hasPermissionTo('admin') && Auth::id() != 1) {
            return ['success' => false, 'message' => 'Non puoi impersonare questo utente'];
        }

        Session::flash('impersona', Auth::id());
        Auth::loginUsingId($id, false);
        return ['success' => true, 'redirect' => '/'];
    }

    protected function azioneInviaMailPassowrdReset($id)
    {

        $user = User::find($id);

        dispatch(function () use ($user) {
            $token = Password::broker('new_users')->createToken($user);
            $user->notify(new PasswordResetNotification($token));

        })->afterResponse();
        return ['success' => true, 'title' => 'Email inviata', 'message' => 'La mail con il link per impostare la password è stata inviata all\'indirizzo ' . $user->email];


    }

}
