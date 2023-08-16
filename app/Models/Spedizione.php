<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Spedizione extends Model
{
    use HasFactory;

    protected $table = "spedizioni";

    public const NOME_SINGOLARE = "spedizione";
    public const NOME_PLURALE = "spedizioni";

    protected $casts = [
        'data_spedizione' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELAZIONI
    |--------------------------------------------------------------------------
    */
    public function cliente(): HasOne
    {
        return $this->hasOne(Cliente::class, 'id', 'cliente_id');
    }

    public function corriere(): HasOne
    {
        return $this->hasOne(Corriere::class, 'id', 'corriere_id');
    }

    public function servizio(): HasOne
    {
        return $this->hasOne(Servizio::class, 'id', 'servizio_id');
    }

    public function statoSpedizione(): HasOne
    {
        return $this->hasOne(StatoSpedizione::class, 'id', 'stato_spedizione');
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

    /*
    |--------------------------------------------------------------------------
    | ALTRO
    |--------------------------------------------------------------------------
    */
}
