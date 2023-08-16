<?php

namespace App\Http\Controllers\Backend;

use App\Models\Cliente;
use App\Models\Comune;
use App\Models\Corriere;
use App\Models\Provincia;
use App\Models\Servizio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use robertogallea\LaravelCodiceFiscale\CodiceFiscale;

class Select2 extends Controller
{
    public function response(Request $request)
    {


        $querystring = $request->input();


        //Prende la prima chiave della querystring
        reset($querystring);
        $key = key($querystring);
        //

        $term = trim($request->input('term'));


        // $term=trim($term);
        switch ($key) {


            case 'cliente':
                $queryBuilder = Cliente::orderBy('cognome')
                    ->orderBy('nome')
                    ->select(['id', DB::raw('concat_ws(" ",cognome,nome) as text')]);

                if ($term) {
                    $arrTerm = explode(' ', $term);
                    foreach ($arrTerm as $t) {
                        $queryBuilder->whereRaw(DB::raw('concat_ws(\' \',cognome,nome,email,telefono) like ?'), "%$t%");
                    }
                }
                return $queryBuilder->get();

            case 'corriere_id':
                $queryBuilder = Corriere::orderBy('denominazione')
                    ->where('abilitato', 1)
                    ->select(['id', 'denominazione as text']);

                if ($term) {
                    $arrTerm = explode(' ', $term);
                    foreach ($arrTerm as $t) {
                        $queryBuilder->whereRaw('denominazione like ?', "%$t%");
                    }
                }
                return $queryBuilder->get();


            case 'servizio_id':
                $queryBuilder = Servizio::orderBy('descrizione')
                    ->where('abilitato', 1)
                    ->select(['id', 'descrizione as text']);
                if ($request->input('corriere_id')) {
                    $queryBuilder->where('corriere_id', $request->input('corriere_id'));
                }

                if ($term) {
                    $arrTerm = explode(' ', $term);
                    foreach ($arrTerm as $t) {
                        $queryBuilder->whereRaw('descrizione like ?', "%$t%");
                    }
                }
                return $queryBuilder->get();


            case 'dati-cf':
                $codiceFiscale = $request->input('codice_fiscale');
                $datiRitorno = [];
                $parserCodiceFiscale = new CodiceFiscale();
                if ($parserCodiceFiscale->parse($codiceFiscale) !== false) {
                    $datiRitorno['genere'] = $parserCodiceFiscale->getGender();
                    $datiRitorno['data_di_nascita'] = $parserCodiceFiscale->getBirthdate()->format('d/m/Y');
                    $luogoNascita = $parserCodiceFiscale->getBirthPlace();
                    $cittaNascita = Comune::where('codice_catastale', $luogoNascita)->first();
                    if ($cittaNascita) {
                        $datiRitorno['luogo_di_nascita'] = $cittaNascita->comune;
                    } else {
                        $datiRitorno['luogo_di_nascita'] = $parserCodiceFiscale->getBirthPlaceComplete();
                    }
                }

                return ['success' => true, 'dati_ritorno' => $datiRitorno];


            case 'citta':
                if (empty($term)) {
                    return [''];
                }
                if (is_array($term)) {
                    $term = $term['term'];
                }


                $queryBuilder = Comune::orderBy('comune')->select(['elenco_comuni.id', DB::raw('CONCAT(comune, " (", targa,")") AS text'), 'cap']);
                return $queryBuilder->where('comune', 'like', $term . '%')->where('soppresso', 0)->get();
                break;


            case 'provincia':
                if (empty($term)) {
                    return [''];
                }
                return Provincia::orderBy('provincia')->select(['id', 'provincia as text'])->where('provincia', 'like', $term . '%')->get();


            case 'regione':
                $queryBuilder = Provincia::orderBy('regione')->select(['id_regione as id', 'regione as text']);
                if ($term != '') {
                    $queryBuilder->where('regione', 'like', $term . '%');
                }
                return $queryBuilder->distinct()->get();

            case 'nazione':
                if (empty($term)) {
                    return [''];
                }
                return DB::table('elenco_nazioni')
                    ->select('alpha2 as id', 'langit as text')
                    ->orderBy('langit')
                    ->where('langit', 'like', $term . '%')
                    ->get();


            default:

                return [];

        }


    }
}
