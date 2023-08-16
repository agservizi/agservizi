<?php

namespace Database\Factories;

use App\Models\Comune;
use Database\Seeders\CfPiRandom;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Spedizione>
 */
class SpedizioneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
                 'corriere_id'=>null,
     'servizio'=>null,
     'cliente_id'=>null,
     'data_spedizione'=>null,
     'stato_spedizione'=>null,
     'denominazione_destinatario'=>null,
     'indirizzo_destinatario'=>null,
     'citta_destinatario'=>null,
     'cap_destinatario'=>null,
     'nazione_destinatario'=>null,
     'codice_tracking'=>null,

        ];
    }
}
