<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Corriere extends Model
{
    use HasFactory;

    protected $table = "corrieri";

    public const NOME_SINGOLARE = "corriere";
    public const NOME_PLURALE = "corrieri";

    /*
    |--------------------------------------------------------------------------
    | RELAZIONI
    |--------------------------------------------------------------------------
    */

    public function servizi(): HasMany
    {
        return $this->hasMany(Servizio::class, 'corriere_id');
    }

    public function spedizioni()
    {
        return $this->hasManyThrough(Spedizione::class, Servizio::class, 'corriere_id', 'servizio_id', 'id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPE
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | PER BLADE
    |--------------------------------------------------------------------------
    */
    public static function selected($id)
    {
        if ($id) {
            $record = self::find($id);
            if ($record) {
                return "<option value='$id' selected>{$record->denominazione}</option>";
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ALTRO
    |--------------------------------------------------------------------------
    */

    public function immagineLogo()
    {
        return $this->logo ? ('/storage' . $this->logo) : '/images/logo-placeholder.png';
    }
}
