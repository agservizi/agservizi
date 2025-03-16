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
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'save':
            $product = $_POST['product'] ?? [];
            
            if (empty($product)) {
                echo json_encode(['success' => false, 'message' => 'Dati prodotto mancanti']);
                exit;
            }
            
            try {
                if (!empty($product['id'])) {
                    // Aggiorna prodotto esistente
                    $stmt = $pdo->prepare("
                        UPDATE prodotti 
                        SET 
                            codice = ?,
                            nome = ?,
                            descrizione = ?,
                            gestore = ?,
                            tipo = ?,
                            prezzo = ?,
                            iva = ?,
                            quantita = ?
                        WHERE id = ?
                    ");
                    
                    $stmt->execute([
                        $product['codice'],
                        $product['nome'],
                        $product['descrizione'] ?? '',
                        $product['gestore'],
                        $product['tipo'],
                        $product['prezzo'],
                        $product['iva'],
                        $product['quantita'],
                        $product['id']
                    ]);
                    
                    echo json_encode(['success' => true, 'message' => 'Prodotto aggiornato con successo']);
                } else {
                    // Inserisci nuovo prodotto
                    $stmt = $pdo->prepare("
                        INSERT INTO prodotti (
                            codice,
                            nome,
                            descrizione,
                            gestore,
                            tipo,
                            prezzo,
                            iva,
                            quantita
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $product['codice'],
                        $product['nome'],
                        $product['descrizione'] ?? '',
                        $product['gestore'],
                        $product['tipo'],
                        $product['prezzo'],
                        $product['iva'],
                        $product['quantita']
                    ]);
                    
                    echo json_encode(['success' => true, 'message' => 'Prodotto aggiunto con successo']);
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    echo json_encode(['success' => false, 'message' => 'Codice prodotto già esistente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Errore durante il salvataggio del prodotto: ' . $e->getMessage()]);
                }
            }
            break;
            
        case 'delete':
            $id = $_POST['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID prodotto mancante']);
                exit;
            }
            
            try {
                $stmt = $pdo->prepare("DELETE FROM prodotti WHERE id = ?");
                $stmt->execute([$id]);
                
                echo json_encode(['success' => true, 'message' => 'Prodotto eliminato con successo']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione del prodotto: ' . $e->getMessage()]);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $id = $_GET['id'] ?? 0;
            
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'ID prodotto mancante']);
                exit;
            }
            
            $product = getProductById($id);
            
            if ($product) {
                echo json_encode(['success' => true, 'product' => $product]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Prodotto non trovato']);
            }
            break;
            
        case 'filter':
            $gestore = $_GET['gestore'] ?? '';
            $tipo = $_GET['tipo'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $sql = "SELECT p.*, COALESCE(g.nome, p.gestore) as gestore_nome 
                    FROM prodotti p 
                    LEFT JOIN (
                        SELECT 'fastweb' as id, 'Fastweb' as nome
                        UNION SELECT 'iliad', 'Iliad'
                        UNION SELECT 'windtre', 'WindTre'
                        UNION SELECT 'skywifi', 'Sky Wifi'
                        UNION SELECT 'skytv', 'Sky TV'
                        UNION SELECT 'pianetafibra', 'Pianeta Fibra'
                    ) g ON p.gestore = g.id
                    WHERE 1=1";
            $params = [];
            
            if (!empty($gestore)) {
                $sql .= " AND p.gestore = ?";
                $params[] = $gestore;
            }
            
            if (!empty($tipo)) {
                $sql .= " AND p.tipo = ?";
                $params[] = $tipo;
            }
            
            if (!empty($search)) {
                $sql .= " AND (p.nome LIKE ? OR p.codice LIKE ?)";
                $search_term = '%' . $search . '%';
                $params[] = $search_term;
                $params[] = $search_term;
            }
            
            $sql .= " ORDER BY p.nome";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
            
            echo json_encode(['success' => true, 'products' => $products]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
}

