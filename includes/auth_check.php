<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Inizializza la sessione
session_start();

// Verifica se l'utente è autenticato
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Verifica se l'utente ha un ruolo specifico
function hasRole($role) {
    if (!isAuthenticated()) {
        return false;
    }
    
    if (is_array($role)) {
        return in_array($_SESSION['ruolo'], $role);
    }
    
    return $_SESSION['ruolo'] === $role;
}

// Verifica se l'utente è autenticato, altrimenti restituisce un errore JSON
function requireAuthentication() {
    if (!isAuthenticated()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Autenticazione richiesta',
            'redirect' => 'login.php'
        ]);
        exit;
    }
}

// Verifica se l'utente ha un ruolo specifico, altrimenti restituisce un errore JSON
function requireRole($role) {
    requireAuthentication();
    
    if (!hasRole($role)) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Non hai i permessi necessari per eseguire questa operazione'
        ]);
        exit;
    }
}

