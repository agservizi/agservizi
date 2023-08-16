<?php
/**
 * Created by PhpStorm.
 * User: andrea
 * Date: 16/08/18
 * Time: 09:54
 */

namespace App\Http\MieClassi;


use Illuminate\Support\Facades\Session;

class AlertMessage
{
    protected $messaggi = [];
    protected $classe = 'success';
    protected $titolo;

    public function __construct()
    {
        if (\session()->has('alertMessage')) {
            $this->messaggi = \session()->get('alertMessage');
        }
    }

    /**
     * @param string|array $testo
     * @return $this
     */
    public function messaggio($testo, $tipo = 'success')
    {

        if ($tipo !== 'success' && !in_array($tipo, ['primary', 'danger', 'info', 'warning'])) {
            $tipo = 'success';
        }

        if (is_array($testo)) {
            $this->addMessaggi($testo, $tipo);
        } else {
            $this->addMessaggi([$testo], $tipo);
        }

        if (!array_key_exists('icona', $this->messaggi[$tipo])) {
            $this->messaggi[$tipo]['icona'] = $this->determinaIcona($tipo);
        }
        if (!array_key_exists('titolo', $this->messaggi[$tipo])) {
            $this->messaggi[$tipo]['titolo'] = $this->determinaTitolo($tipo);
        }


        return $this;
    }


    protected function addMessaggi($testoArr, $tipo)
    {
        foreach ($testoArr as $testo) {
            $this->messaggi[$tipo]['messaggi'][] = $testo;
        }
    }


    public function titolo($testo, $tipo = 'success', $icona = null)
    {
        $this->messaggi[$tipo]['titolo'] = $testo;
        if ($icona) {
            $this->messaggi[$tipo]['icona'] = $icona;
        }

        return $this;
    }

    public function fromArray($arr)
    {

        // $arr['danger'] = [
        //     'titolo' => 'Errore nel file Excel',
        //    'messaggi' => ['Foglio UOMO o DONNA non trovato']
        // ];

        $this->messaggi = $arr;
        foreach ($this->messaggi as $level => $value) {
            if (!array_key_exists('icona', $value)) {
                $this->messaggi[$level]['icona'] = $this->determinaIcona($level);
            }
        }

        return $this;

    }

    public function flash()
    {
        if (count($this->messaggi)) {
            Session::flash('alertMessage', $this->messaggi);

        }
    }

    protected function determinaIcona($level)
    {
        switch ($level) {
            case 'warning':
                return 'fas fa-exclamation-triangle';

            case 'danger':
                return 'fas fa-exclamation-circle';

            case 'info':
                return 'fas fa-exclamation';


            default:
                return 'fas fa-check-circle';
        }
    }

    protected function determinaTitolo($level)
    {
        switch ($level) {
            case 'danger':
                return 'Errore!';

            case 'warning':
                return 'Attenzione!';

            default:
                return 'Bene!';
        }
    }


}
