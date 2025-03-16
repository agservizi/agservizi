<?php
// Funzioni di utilità per il sistema POS

/**
 * Verifica lo stato della stampante fiscale
 * @return bool Stato della stampante (true = attiva, false = non attiva)
 */
function checkFiscalPrinterStatus() {
    // Qui andrebbe implementata la logica di verifica della stampante fiscale
    // Per ora restituiamo un valore di esempio
    return false;
}

/**
 * Ottiene l'elenco dei gestori disponibili
 * @return array Lista dei gestori
 */
function getProviders() {
    return [
        'fastweb' => 'Fastweb',
        'iliad' => 'Iliad',
        'windtre' => 'WindTre',
        'skywifi' => 'Sky Wifi',
        'skytv' => 'Sky TV',
        'pianetafibra' => 'Pianeta Fibra'
    ];
}

/**
 * Ottiene i prodotti dal database
 * @param string $provider Filtra per gestore (opzionale)
 * @param string $type Filtra per tipo (sim, dispositivo, ricarica)
 * @return array Lista dei prodotti
 */
function getProducts($provider = null, $type = null) {
    global $pdo;
    
    $sql = "SELECT * FROM prodotti WHERE 1=1";
    $params = [];
    
    if ($provider) {
        $sql .= " AND gestore = ?";
        $params[] = $provider;
    }
    
    if ($type) {
        $sql .= " AND tipo = ?";
        $params[] = $type;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}

/**
 * Ottiene un prodotto dal database tramite ID
 * @param int $id ID del prodotto
 * @return array|false Dati del prodotto o false se non trovato
 */
function getProductById($id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM prodotti WHERE id = ?");
    $stmt->execute([$id]);
    
    return $stmt->fetch();
}

/**
 * Aggiunge un prodotto al carrello
 * @param int $product_id ID del prodotto
 * @param int $quantity Quantità
 * @return bool Esito dell'operazione
 */
function addToCart($product_id, $quantity = 1) {
    $product = getProductById($product_id);
    
    if (!$product) {
        return false;
    }
    
    // Se il prodotto è già nel carrello, aggiorna la quantità
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'codice' => $product['codice'],
            'nome' => $product['nome'],
            'prezzo' => $product['prezzo'],
            'iva' => $product['iva'],
            'quantity' => $quantity
        ];
    }
    
    return true;
}

/**
 * Rimuove un prodotto dal carrello
 * @param int $product_id ID del prodotto
 * @return bool Esito dell'operazione
 */
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        return true;
    }
    
    return false;
}

/**
 * Calcola il totale del carrello
 * @return array Totali (subtotale, iva, totale)
 */
function calculateCartTotal() {
    $subtotal = 0;
    $total_iva = 0;
    
    foreach ($_SESSION['cart'] as $item) {
        $price = $item['prezzo'] * $item['quantity'];
        $subtotal += $price;
        $total_iva += $price * ($item['iva'] / 100);
    }
    
    $total = $subtotal + $total_iva;
    
    return [
        'subtotal' => $subtotal,
        'iva' => $total_iva,
        'total' => $total
    ];
}

/**
 * Svuota il carrello
 */
function clearCart() {
    $_SESSION['cart'] = [];
}

/**
 * Registra una transazione nel database
 * @param array $customer_data Dati del cliente
 * @param float $discount Sconto applicato
 * @param string $payment_method Metodo di pagamento
 * @return int|false ID della transazione o false in caso di errore
 */
function saveTransaction($customer_data, $discount = 0, $payment_method = 'contanti') {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        $totals = calculateCartTotal();
        $total_with_discount = $totals['total'] - $discount;
        
        // Inserisci la transazione
        $stmt = $pdo->prepare("
            INSERT INTO transazioni (
                data, 
                totale, 
                iva, 
                sconto, 
                metodo_pagamento, 
                cliente_nome, 
                cliente_cognome, 
                cliente_telefono, 
                cliente_email, 
                cliente_cf, 
                numero_scontrino
            ) VALUES (
                NOW(), 
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?,
                ?
            )
        ");
        
        $receipt_number = generateReceiptNumber();
        
        $stmt->execute([
            $total_with_discount,
            $totals['iva'],
            $discount,
            $payment_method,
            $customer_data['nome'] ?? '',
            $customer_data['cognome'] ?? '',
            $customer_data['telefono'] ?? '',
            $customer_data['email'] ?? '',
            $customer_data['codice_fiscale'] ?? '',
            $receipt_number
        ]);
        
        $transaction_id = $pdo->lastInsertId();
        
        // Inserisci i dettagli della transazione
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO transazioni_dettaglio (
                    transazione_id, 
                    prodotto_id, 
                    codice, 
                    nome, 
                    prezzo, 
                    iva, 
                    quantita
                ) VALUES (
                    ?, 
                    ?, 
                    ?, 
                    ?, 
                    ?, 
                    ?, 
                    ?
                )
            ");
            
            $stmt->execute([
                $transaction_id,
                $item['id'],
                $item['codice'],
                $item['nome'],
                $item['prezzo'],
                $item['iva'],
                $item['quantity']
            ]);
            
            // Aggiorna il magazzino
            updateInventory($item['id'], $item['quantity']);
        }
        
        // Emetti lo scontrino fiscale
        $fiscal_receipt = emitFiscalReceipt($transaction_id);
        
        // Aggiorna la transazione con i dati dello scontrino
        if ($fiscal_receipt) {
            $stmt = $pdo->prepare("
                UPDATE transazioni 
                SET 
                    numero_scontrino = ?,
                    data_scontrino = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $fiscal_receipt['numero'],
                $fiscal_receipt['data'],
                $transaction_id
            ]);
        }
        
        $pdo->commit();
        clearCart();
        
        return $transaction_id;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Errore durante il salvataggio della transazione: " . $e->getMessage());
        return false;
    }
}

/**
 * Aggiorna il magazzino dopo una vendita
 * @param int $product_id ID del prodotto
 * @param int $quantity Quantità venduta
 * @return bool Esito dell'operazione
 */
function updateInventory($product_id, $quantity) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE prodotti 
        SET quantita = quantita - ? 
        WHERE id = ?
    ");
    
    return $stmt->execute([$quantity, $product_id]);
}

/**
 * Genera un numero di scontrino
 * @return string Numero di scontrino
 */
function generateReceiptNumber() {
    return date('Ymd') . '-' . rand(1000, 9999);
}

/**
 * Emette uno scontrino fiscale tramite il misuratore fiscale
 * @param int $transaction_id ID della transazione
 * @return array|false Dati dello scontrino o false in caso di errore
 */
function emitFiscalReceipt($transaction_id) {
    global $pdo;
    
    // Recupera i dati della transazione
    $stmt = $pdo->prepare("
        SELECT t.*, td.* 
        FROM transazioni t
        JOIN transazioni_dettaglio td ON t.id = td.transazione_id
        WHERE t.id = ?
    ");
    
    $stmt->execute([$transaction_id]);
    $transaction_data = $stmt->fetchAll();
    
    if (empty($transaction_data)) {
        return false;
    }
    
    // Qui andrebbe implementata la logica di comunicazione con il misuratore fiscale
    // Questo è solo un esempio di implementazione
    
    // Simula l'emissione dello scontrino
    $receipt_data = [
        'numero' => generateReceiptNumber(),
        'data' => date('Y-m-d H:i:s')
    ];
    
    return $receipt_data;
}

/**
 * Cerca transazioni nel database
 * @param array $filters Filtri di ricerca
 * @return array Lista delle transazioni
 */
function searchTransactions($filters = []) {
    global $pdo;
    
    $sql = "SELECT * FROM transazioni WHERE 1=1";
    $params = [];
    
    if (!empty($filters['data_da'])) {
        $sql .= " AND data >= ?";
        $params[] = $filters['data_da'] . ' 00:00:00';
    }
    
    if (!empty($filters['data_a'])) {
        $sql .= " AND data <= ?";
        $params[] = $filters['data_a'] . ' 23:59:59';
    }
    
    if (!empty($filters['cliente'])) {
        $sql .= " AND (cliente_nome LIKE ? OR cliente_cognome LIKE ? OR cliente_telefono LIKE ?)";
        $search_term = '%' . $filters['cliente'] . '%';
        $params[] = $search_term;
        $params[] = $search_term;
        $params[] = $search_term;
    }
    
    $sql .= " ORDER BY data DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    return $stmt->fetchAll();
}


/**
 * Verifica se l'utente ha un determinato permesso
 * 
 * @param string $permissionName Nome del permesso da verificare
 * @return bool True se l'utente ha il permesso, False altrimenti
 */
function hasPermission($permissionName) {
    // Se l'utente è admin, ha tutti i permessi
    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
        return true;
    }
    
    // Verifica se l'utente ha il permesso specifico
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $db = getDbConnection();
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM roles_permissions rp
        JOIN permissions p ON rp.permission_id = p.id
        JOIN roles r ON rp.role_id = r.id
        JOIN users u ON u.role_id = r.id
        WHERE u.id = ? AND p.name = ?
    ");
    
    $stmt->execute([$_SESSION['user_id'], $permissionName]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] > 0;
}

