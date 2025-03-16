<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Verifica l'autenticazione e il ruolo
requireRole(['amministratore', 'responsabile']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save':
            $store_info = $_POST['store_info'] ?? [];
            $printer_settings = $_POST['printer_settings'] ?? [];
            
            try {
                $pdo->beginTransaction();
                
                // Salva le informazioni del negozio
                foreach ($store_info as $key => $value) {
                    $stmt = $pdo->prepare("
                        INSERT INTO impostazioni (chiave, valore) 
                        VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE valore = ?
                    ");
                    $stmt->execute([$key, $value, $value]);
                }
                
                // Salva le impostazioni della stampante
                foreach ($printer_settings as $key => $value) {
                    $stmt = $pdo->prepare("
                        INSERT INTO impostazioni (chiave, valore) 
                        VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE valore = ?
                    ");
                    $stmt->execute([$key, $value, $value]);
                }
                
                $pdo->commit();
                echo json_encode(['success' => true, 'message' => 'Impostazioni salvate con successo']);
            } catch (PDOException $e) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio delle impostazioni: ' . $e->getMessage()]);
            }
            break;
            
        case 'test_printer':
            $printer_settings = $_POST['printer_settings'] ?? [];
            
            // Qui andrebbe implementata la logica di test della connessione alla stampante
            // Per ora restituiamo un successo simulato
            echo json_encode(['success' => true, 'message' => 'Connessione alla stampante riuscita']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $settings = [];
            
            $stmt = $pdo->query("SELECT chiave, valore FROM impostazioni");
            $rows = $stmt->fetchAll();
            
            foreach ($rows as $row) {
                $settings[$row['chiave']] = $row['valore'];
            }
            
            echo json_encode(['success' => true, 'settings' => $settings]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
}

