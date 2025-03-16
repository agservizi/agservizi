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

// Gestione delle richieste AJAX per il carrello
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add':
            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;
            
            if (addToCart($product_id, $quantity)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'aggiunta del prodotto al carrello']);
            }
            break;
            
        case 'update':
            $product_id = $_POST['product_id'] ?? 0;
            $change = $_POST['change'] ?? 0;
            
            if (isset($_SESSION['cart'][$product_id])) {
                $new_quantity = $_SESSION['cart'][$product_id]['quantity'] + $change;
                
                if ($new_quantity <= 0) {
                    removeFromCart($product_id);
                } else {
                    $_SESSION['cart'][$product_id]['quantity'] = $new_quantity;
                }
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Prodotto non trovato nel carrello']);
            }
            break;
            
        case 'remove':
            $product_id = $_POST['product_id'] ?? 0;
            
            if (removeFromCart($product_id)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Errore durante la rimozione del prodotto dal carrello']);
            }
            break;
            
        case 'clear':
            clearCart();
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get':
            $cart = [];
            
            foreach ($_SESSION['cart'] as $item) {
                $cart[] = $item;
            }
            
            echo json_encode(['success' => true, 'cart' => $cart]);
            break;
            
        case 'totals':
            $discount = isset($_GET['discount']) ? floatval($_GET['discount']) : 0;
            
            $totals = calculateCartTotal();
            $total_with_discount = $totals['total'] - $discount;
            
            echo json_encode([
                'success' => true,
                'totals' => [
                    'subtotal' => $totals['subtotal'],
                    'iva' => $totals['iva'],
                    'discount' => $discount,
                    'total' => $total_with_discount > 0 ? $total_with_discount : 0
                ]
            ]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
}

