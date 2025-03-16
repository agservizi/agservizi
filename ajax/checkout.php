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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_data = $_POST['customer'] ?? [];
    $discount = isset($_POST['discount']) ? floatval($_POST['discount']) : 0;
    $payment_method = $_POST['payment_method'] ?? 'contanti';
    
    $transaction_id = saveTransaction($customer_data, $discount, $payment_method);
    
    if ($transaction_id) {
        // Recupera i dati della transazione
        $stmt = $pdo->prepare("SELECT * FROM transazioni WHERE id = ?");
        $stmt->execute([$transaction_id]);
        $transaction = $stmt->fetch();
        
        echo json_encode([
            'success' => true,
            'transaction_id' => $transaction_id,
            'receipt' => [
                'number' => $transaction['numero_scontrino'],
                'date' => date('d/m/Y H:i', strtotime($transaction['data'])),
                'total' => $transaction['totale']
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio della transazione']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metodo non valido']);
}

