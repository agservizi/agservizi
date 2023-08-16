<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class RegistroSegnalazioni extends Model
{
    //
    protected $table = 'registro_segnalazioni';

    public static $cartellaImmagine = 'ScreenshotErrori/';

    protected static function boot()
    {

        parent::boot();

        self::deleting(function ($model) {
            //Elimino immagine
            Storage::disk('public')->delete($model->nomeFileConPath());
        });

    }


    public function utente()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }


    public function setTitoloAttribute($value)
    {
        $this->attributes['titolo'] = ucfirst($value);
    }

    public function nomeFile()
    {
        return 'Errore_' . $this->id . '.png';
    }

    public function nomeFileConPath()
    {
        return self::$cartellaImmagine . 'Errore_' . $this->id . '.png';

    }


}
