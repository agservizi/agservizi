<?php

namespace App\Http\Controllers\Backend;

use App\Http\MieClassi\AlertMessage;
use App\Models\User;
use App\Rules\ConfermaEliminaRule;
use App\Rules\PasswordAttualeRule;
use App\Rules\TelefonoRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Rules\Password;
use App\Http\Controllers\Controller;
use Storage;
use function App\getInputTelefono;
use function App\getInputUcwords;

class DatiUtenteController extends Controller
{

    public function show($cosa = null)
    {

        return view('Backend.DatiUtente.editDatiUtente', [
            'record' => Auth::user(),
            'controller' => DatiUtenteController::class,
            'titoloPagina'=>'Dati utente'
        ]);

    }

    public function update(Request $request, $cosa)
    {
        switch ($cosa) {
            case 'dati-utente':
                Validator::make($request->input(), [
                    'nome' => ['required', 'string', 'max:255'],
                    'cognome' => ['required', 'string', 'max:255'],
                    'telefono' => ['required', new TelefonoRule(),Rule::unique(User::class)->ignore(\Auth::id())],
                ])->validate();

                $this->updateDatiUtente($request);
                $alert = new AlertMessage();
                $alert->messaggio('I tuoi dati sono stati aggiornati')->flash();
                break;


            case 'dati-email':
                Validator::make($request->input(), [
                    'email' => [
                        'required',
                        'string',
                        'email:rfc,dns',
                        'max:255',
                        Rule::unique(User::class)->ignore(\Auth::id()),
                    ]
                ])->validate();
                $this->updateEmail($request);
                $alert = new AlertMessage();
                $alert->messaggio('Il tuo indirizzo email è stato modificato in: ' . $request->input('email'))->titolo('Indirizzo email modificato')->flash();
                break;

            case 'dati-password':
                Validator::make($request->input(), [
                    'password_attuale' => new PasswordAttualeRule(),
                    'password' => $this->passwordRules(),
                ])->validate();
                $this->updatePassword($request);
                $alert = new AlertMessage();
                $alert->messaggio('La tua password è stata modificata ')->flash();
                break;

            case 'elimina-account':
                Validator::make($request->input(), [
                    'password_attuale' => new PasswordAttualeRule(),
                    'conferma' => [new ConfermaEliminaRule()],
                ])->validate();


                dispatch(function () {
                    Auth::user()->notify(new NotificaAccountEliminatoAUtente());
                    $userAdmin = User::find(2);
                    $userAdmin->notify(new NotificaAccountEliminatoAAdmin(Auth::user()));
                })->afterResponse();

                $alert = new AlertMessage();
                $alert->messaggio('Il tuo account è stato eliminato', 'warning')->flash();
                Auth::user()->delete();
                return redirect('/logout');

                break;


        }

        return redirect()->action([DatiUtenteController::class, 'show']);
    }




    protected function updateDatiUtente($request)
    {
        $user = Auth::user();
        $user->nome = getInputUcwords($request->input('nome'));
        $user->cognome = getInputUcwords($request->input('cognome'));
        $user->telefono = getInputTelefono($request->input('telefono'));
        //$user->iban = $request->input('iban');
        $user->save();

    }

    protected function updateEmail($request)
    {
        $user = Auth::user();
        $user->email = $request->input('email');
        $user->save();

    }

    protected function updatePassword($request)
    {
        $user = Auth::user();
        $user->password = Hash::make($request->input('password'));
        $user->save();

    }

    protected function passwordRules()
    {
        return ['required', 'string', new Password, 'confirmed'];
    }


}
