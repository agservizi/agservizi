<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

// Verifica l'autenticazione
requireAuthentication();

// Parametri di filtro
$data_da = $_GET['data_da'] ?? date('Y-m-01');
$data_a = $_GET['data_a'] ?? date('Y-m-d');
$cliente = $_GET['cliente'] ?? '';

// Recupera le transazioni filtrate
$transactions = searchTransactions([
    'data_da' => $data_da,
    'data_a' => $data_a,
    'cliente' => $cliente
]);

// Crea il file CSV
$filename = 'transazioni_' . date('Y-m-d_H-i-s') . '.csv';

// Imposta gli header per il download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Apri l'output come file CSV
$output = fopen('php://output', 'w');

// Intestazioni delle colonne
fputcsv($output, [
    'ID',
    'Data',
    'Cliente Nome',
    'Cliente Cognome',
    'Cliente Telefono',
    'Cliente Email',
    'Cliente CF',
    'Numero Scontrino',
    'Totale',
    'IVA',
    'Sconto',
    'Metodo Pagamento'
]);

// Dati delle transazioni
foreach ($transactions as $transaction) {
    fputcsv($output, [
        $transaction['id'],
        $transaction['data'],
        $transaction['cliente_nome'],
        $transaction['cliente_cognome'],
        $transaction['cliente_telefono'],
        $transaction['cliente_email'],
        $transaction['cliente_cf'],
        $transaction['numero_scontrino'],
        $transaction['totale'],
        $transaction['iva'],
        $transaction['sconto'],
        $transaction['metodo_pagamento']
    ]);
}

fclose($output);
exit;

