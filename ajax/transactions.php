<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth_check.php';

// Verifica l'autenticazione
requireAuthentication();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'filter':
            $data_da = $_GET['data_da'] ?? '';
            $data_a = $_GET['data_a'] ?? '';
            $cliente = $_GET['cliente'] ?? '';
            
            $transactions = searchTransactions([
                'data_da' => $data_da,
                'data_a' => $data_a,
                'cliente' => $cliente
            ]);
            
            echo json_encode(['success' => true, 'transactions' => $transactions]);
            break;
            
        case 'details':
            $id = $_GET['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID transazione mancante']);
                exit;
            }
            
            // Recupera i dati della transazione
            $stmt = $pdo->prepare("SELECT * FROM transazioni WHERE id = ?");
            $stmt->execute([$id]);
            $transaction = $stmt->fetch();
            
            if (!$transaction) {
                echo json_encode(['success' => false, 'message' => 'Transazione non trovata']);
                exit;
            }
            
            // Recupera i dettagli della transazione
            $stmt = $pdo->prepare("SELECT * FROM transazioni_dettaglio WHERE transazione_id = ?");
            $stmt->execute([$id]);
            $details = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'transaction' => $transaction,
                'details' => $details
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'print':
            $id = $_POST['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID transazione mancante']);
                exit;
            }
            
            // Qui andrebbe implementata la logica di stampa dello scontrino
            // Per ora restituiamo un successo simulato
            echo json_encode(['success' => true, 'message' => 'Scontrino inviato alla stampante']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
}

