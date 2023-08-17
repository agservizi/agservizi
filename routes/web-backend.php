<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth']], function () {
    Route::get('/metronic/{cosa}', [\App\Http\Controllers\Backend\AreaPersonaleController::class, 'metronic']);

});


Route::group(['middleware' => ['auth', 'role_or_permission:admin']], function () {

    Route::get('/', [\App\Http\Controllers\Backend\DashboardController::class, 'show']);

    Route::resource('spedizione', \App\Http\Controllers\Backend\SpedizioneController::class);

    //select2
    Route::get('select2', [\App\Http\Controllers\Backend\Select2::class, 'response']);

    //Modal
    Route::get('modal/{cosa}', [\App\Http\Controllers\Backend\ModalController::class, 'show']);

    //Impostazioni
    Route::get('/settings/{sezione}', [\App\Http\Controllers\Backend\SettingController::class, 'edit']);
    Route::get('/settings', [\App\Http\Controllers\Backend\SettingController::class, 'index'])->name('settings');
    Route::post('/settings/{sezione}', [\App\Http\Controllers\Backend\SettingController::class, 'store'])->name('settings.store');


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
