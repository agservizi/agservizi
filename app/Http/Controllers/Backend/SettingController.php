<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('Backend.Setting.index', [
            'titoloPagina' => 'Impostazioni'
        ]);
    }
    public function edit($sezione)
    {


        return view('Backend.Setting.sezione', [
            'titoloPagina' => config('configurazione.sezioni_home.' . $sezione),
            'controller' => get_class($this),
            'sezione' => $sezione
        ]);
    }

    public function store(Request $request, $sezione)
    {

        $rules = Setting::getValidationRules($sezione);
        $data = $this->validate($request, $rules);

        $validSettings = array_keys($rules);

        foreach ($data as $key => $val) {
            if (in_array($key, $validSettings)) {
                Setting::add($key, $val, Setting::getDataType($key));
            }
        }

        return redirect()->back()->with('status', 'Dati salvati');
    }

}
