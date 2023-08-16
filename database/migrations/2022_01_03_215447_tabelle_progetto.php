<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TabelleProgetto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('corrieri', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('denominazione');
            $table->string('logo')->nullable();
            $table->string('url_tracking')->nullable();
            $table->boolean('abilitato')->index();
        });

        Schema::create('servizi', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('corriere_id')->constrained('corrieri');
            $table->string('descrizione');
            $table->boolean('abilitato')->index();
        });

        Schema::create('stati_spedizioni', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamps();
            $table->string('nome');
            $table->string('colore_hex')->nullable();
            $table->boolean('primo_stato')->index();
        });

        Schema::create('spedizioni', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('corriere_id')->constrained('corrieri');
            $table->foreignId('servizio_id')->constrained('servizi');
            $table->foreignId('cliente_id')->constrained('users');
            $table->date('data_spedizione')->nullable();
            $table->string('stato_spedizione');
            $table->string('denominazione_destinatario')->nullable();
            $table->string('indirizzo_destinatario')->nullable();
            $table->string('citta_destinatario')->nullable();
            $table->string('cap_destinatario')->nullable();
            $table->string('nazione_destinatario')->nullable();
            $table->string('codice_tracking')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ///
    }
}
