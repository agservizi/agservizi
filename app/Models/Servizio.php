<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Servizio extends Model
{
    use HasFactory;
    protected $table="servizi";

    public const NOME_SINGOLARE = "servizio";
    public const NOME_PLURALE = "servizi";

    /*
    |--------------------------------------------------------------------------
    | RELAZIONI
    |--------------------------------------------------------------------------
    */

    public function spedizioni(): HasMany
    {
        return $this->hasMany(Spedizione::class,'servizio_id');
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
                return "<option value='$id' selected>{$record->descrizione}</option>";
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | ALTRO
    |--------------------------------------------------------------------------
    */
}
