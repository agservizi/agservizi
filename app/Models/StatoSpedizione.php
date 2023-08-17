<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StatoSpedizione extends Model
{
    use Sluggable;
    protected $table="stati_spedizioni";
    public $incrementing = false;


    public const NOME_SINGOLARE = "stato spedizione";
    public const NOME_PLURALE = "stati spedizioni";


    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'id' => [
                'source' => 'nome'
            ]
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELAZIONI
    |--------------------------------------------------------------------------
    */

    public function spedizioni(): HasMany
    {
        return $this->hasMany(Spedizione::class,'stato_spedizione');
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

    public function badgeStato()
    {
        return "<span class='badge' style='background-color: {$this->colore_hex}'>{$this->nome}</span>";
    }

    /*
    |--------------------------------------------------------------------------
    | ALTRO
    |--------------------------------------------------------------------------
    */
}
