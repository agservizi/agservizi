<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('/metronic/{cosa}', [\App\Http\Controllers\Backend\AreaPersonaleController::class, 'metronic']);

});


Route::group(['middleware' => ['auth', 'role_or_permission:admin']], function () {

    Route::get('/', [\App\Http\Controllers\Backend\DashboardController::class, 'show']);

    //Spedizioni
    Route::resource('spedizione', \App\Http\Controllers\Backend\SpedizioneController::class);
    Route::get('spedizione-cambia-stato/{id}',[\App\Http\Controllers\Backend\SpedizioneController::class,'modalCambiaStato']);
    Route::patch('spedizione-cambia-stato/{id}',[\App\Http\Controllers\Backend\SpedizioneController::class,'updateStato']);
    Route::post('spedizione-upload/{cosa}/{id}',[\App\Http\Controllers\Backend\SpedizioneController::class,'uploadAllegato']);
    Route::delete('spedizione-upload/',[\App\Http\Controllers\Backend\SpedizioneController::class,'deleteAllegato']);
    Route::get('/spedizione-download/{id}', [\App\Http\Controllers\Backend\SpedizioneController::class, 'downloadAllegato']);

    //select2
    Route::get('select2', [\App\Http\Controllers\Backend\Select2::class, 'response']);

    //Corrieri
    Route::resource('corriere', \App\Http\Controllers\Backend\CorriereController::class);

    //Servizi
    Route::resource('servizio', \App\Http\Controllers\Backend\ServizioController::class)->except(['show', 'index']);

    //Clienti
    Route::resource('cliente', \App\Http\Controllers\Backend\ClienteController::class)->only(['index', 'show', 'edit']);
    Route::post('/cliente/{id}/azione/{azione}', [\App\Http\Controllers\Backend\ClienteController::class, 'azioni']);

    //Stato spedizione
    Route::resource('stato-spedizione', \App\Http\Controllers\Backend\StatoSpedizioneController::class)->except(['show']);


    //Registri
    Route::get('registro/{cosa}', [\App\Http\Controllers\Backend\RegistriController::class, 'index']);

    //Dati utente
    Route::get('/dati-utente', [\App\Http\Controllers\Backend\DatiUtenteController::class, 'show']);
    Route::patch('/dati-utente/{cosa}', [\App\Http\Controllers\Backend\DatiUtenteController::class, 'update']);

    //Tabelle


    //Operatore
    Route::get('/operatore-tab/{id}/tab/{tab}', [\App\Http\Controllers\Backend\OperatoreController::class, 'tab']);
    Route::resource('/operatore', \App\Http\Controllers\Backend\OperatoreController::class);
    Route::post('/operatore/{id}/azione/{azione}', [\App\Http\Controllers\Backend\OperatoreController::class, 'azioni']);


});
