<?php

namespace App\Http\Controllers\Backend;

use App\Http\HelperForMetronic;
use App\Mail\SegnalaProblema;
use App\Models\RegistroSegnalazioni;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Swift_SmtpTransport;
use App\Http\Controllers\Controller;

class SegnalaProblemaController extends Controller
{

    protected $urgenza = [
        0 => ['testo' => '', 'colore' => ''],
        5 => ['testo' => '', 'colore' => ''],
        25 => ['testo' => 'Quando hai tempo', 'colore' => 'yellow-crusta'],
        50 => ['testo' => 'Prima possibile', 'colore' => 'red-flamingo'],
    ];

    protected $risoltotesto = [
        0 => ['testo' => 'Da completare', 'colore' => ''],
        25 => ['testo' => 'Parzialmente', 'colore' => 'yellow-crusta'],
        50 => ['testo' => 'Completatato', 'colore' => 'green-jungle']
    ];

    public function index()
    {
        if (Auth::id() == 1) {
            $segnalazioniQB = RegistroSegnalazioni::where('risolto', '<', 50)->orderBy('urgenza', 'desc');
        } else {
            $segnalazioniQB = RegistroSegnalazioni::where('user_id', \Auth::id())->orderBy('id', 'desc');
        }

        return view('Backend.SegnalaProblema.index')->with([
            'records' => $segnalazioniQB->with('utente')->paginate(20),
            'filtro' => false,
            'storageUrl' => Storage::disk('public')->url(RegistroSegnalazioni::$cartellaImmagine),
            'risoltotesto' => $this->risoltotesto,
            'urgenza' => $this->urgenza,
            'controller' => SegnalaProblemaController::class,
            'titoloPagina' => 'Elenco segnalazioni'
        ]);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('Backend.SegnalaProblema.create')->with([
            'urgenza' => $this->urgenza,
            'titoloPagina' => 'Segnala un problema',
            'record' => new RegistroSegnalazioni()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function store(Request $request)
    {
        //


        if ($request->has('base64data')) {

            $model = $this->scriviNelRegistro($request->input('titolo'), $request->input('descrizione'), $request->input('url'), $request->input('urgenza', 5));

            $unencodedImmagine = $this->unencodeImmagine($request->input('base64data'));
            $this->salvaImmagine($unencodedImmagine, $model);

            if (env('MAIL_HOST') == 'smtp.mailtrap.io') {
                $transport = new Swift_SmtpTransport('smtp.googlemail.com', 465, 'ssl');
                $transport->setUsername('andicot@gmail.com');
                $transport->setPassword('ebxcvtwfwvfpjapd');
                $gmail = new \Swift_Mailer($transport);
                Mail::setSwiftMailer($gmail);

            }


            dispatch(function () use ($request, $unencodedImmagine) {
                Mail::send(new SegnalaProblema($request->all(), $unencodedImmagine));
            })->afterResponse();


            return [true];
        } else {

            return [false];
        }


    }


    public function update(Request $request, $id)
    {

        $segnalazione = RegistroSegnalazioni::find($id);
        $segnalazione->risolto = $request->input('risolto');
        $segnalazione->save();

        return ['success' => true, 'id' => $id, 'label' => HelperForMetronic::labelSegnalazione($segnalazione->risolto)];
    }

    protected function unencodeImmagine($data64)
    {
        //Get the base-64 string from data
        $filteredData = substr($data64, strpos($data64, ",") + 1);

        //Decode the string
        $unencodedData = base64_decode($filteredData);
        return $unencodedData;
    }

    /** Salva l'immagine screenshot passata dal client
     * @param $unencodedImmagine
     * @param RegistroSegnalazioni $segnalazione
     */
    protected function salvaImmagine($unencodedImmagine, $segnalazione)
    {
        File::ensureDirectoryExists(Storage::disk('public')->path(RegistroSegnalazioni::$cartellaImmagine));
        Storage::disk('public')->put($segnalazione->nomeFileConPath(), $unencodedImmagine);
    }

    /**
     * @param $titoloErrore
     * @param $testoErrore
     * @param $urlPagina
     * @param $urgenza
     * @return RegistroSegnalazioni
     */
    protected function scriviNelRegistro($titoloErrore, $testoErrore, $urlPagina, $urgenza)
    {
        $segnalazione = new RegistroSegnalazioni();
        $segnalazione->user_id = Auth::id();
        $segnalazione->titolo = $titoloErrore;
        $segnalazione->testo = $testoErrore;
        $segnalazione->url = $urlPagina;
        $segnalazione->urgenza = $urgenza;
        $segnalazione->save();
        return $segnalazione;
    }


}
