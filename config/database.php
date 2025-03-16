<?php
// Sostituisci le credenziali del database con quelle fornite

// Configurazione del database
$db_host = '127.0.0.1:3306';
$db_user = 'u427445037_coresuiteIT';
$db_pass = 'Giogiu2123@';
$db_name = 'u427445037_coresuiteIT';

// Connessione al database
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore di connessione al database: " . $e->getMessage());
}

// Inizializzazione del carrello nella sessione se non esiste
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

