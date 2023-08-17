<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('spedizioni_allegati', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('spedizione_id')->constrained('spedizioni');
            $table->string('uid')->nullable()->index();
            $table->string('filename_originale');
            $table->string('path_filename');
            $table->unsignedBigInteger('dimensione_file');
            $table->string('tipo_file');
            $table->string('thumbnail')->nullable();
            $table->string('cosa')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spedizioni_allegati');
    }
};
