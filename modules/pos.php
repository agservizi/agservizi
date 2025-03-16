<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Ottieni i dati dell'utente corrente
$user_id = $_SESSION['user_id'] ?? 0;
$user_nome = $_SESSION['nome'] ?? '';
$user_cognome = $_SESSION['cognome'] ?? '';
$user_ruolo = $_SESSION['ruolo'] ?? '';
$negozio_id = $_SESSION['negozio_id'] ?? null;

// Ottieni il gestore selezionato (se presente)
$gestore_selezionato = isset($_GET['gestore']) ? $_GET['gestore'] : null;

// Ottieni i gestori disponibili
$gestori = getProviders();

// Funzione per ottenere i prodotti dal database con gestione errori
function getProdottiSicuri($pdo, $gestore = null, $tipo = null, $search = null) {
    try {
        $sql = "SELECT * FROM prodotti WHERE 1=1";
        $params = [];
        
        if ($gestore) {
            $sql .= " AND gestore = ?";
            $params[] = $gestore;
        }
        
        if ($tipo) {
            $sql .= " AND tipo = ?";
            $params[] = $tipo;
        }
        
        if ($search) {
            $sql .= " AND (nome LIKE ? OR codice LIKE ? OR descrizione LIKE ?)";
            $search_term = '%' . $search . '%';
            $params[] = $search_term;
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        $sql .= " ORDER BY nome ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // Log dell'errore
        error_log("Errore nel recupero dei prodotti: " . $e->getMessage());
        return [];
    }
}

// Ottieni i prodotti per ogni categoria
$sim_products = getProdottiSicuri($pdo, $gestore_selezionato, 'sim');
$devices_products = getProdottiSicuri($pdo, $gestore_selezionato, 'dispositivo');
$recharges_products = getProdottiSicuri($pdo, $gestore_selezionato, 'ricarica');

// Calcola i totali del carrello
$totals = calculateCartTotal();

// Ottieni i metodi di pagamento disponibili
$metodi_pagamento = [
    'contanti' => 'Contanti',
    'carta' => 'Carta di credito/debito',
    'bonifico' => 'Bonifico bancario',
    'satispay' => 'Satispay',
    'paypal' => 'PayPal'
];

// Funzione per verificare se un prodotto ha IMEI/ICCID
function hasSerialNumbers($pdo, $product_id) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM imei_iccid WHERE prodotto_id = ? AND stato = 'disponibile'");
        $stmt->execute([$product_id]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Funzione per ottenere i clienti per l'autocompletamento
function getClientiSuggerimenti($pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, codice_fiscale, nome, cognome, telefono, email 
            FROM clienti 
            ORDER BY nome, cognome 
            LIMIT 100
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Ottieni i clienti per l'autocompletamento
$clienti_suggerimenti = getClientiSuggerimenti($pdo);
$clienti_json = json_encode($clienti_suggerimenti);
?>

<div class="pos-container">
    <!-- Header della pagina POS -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-cart"></i> Punto Vendita</h2>
                <p class="text-muted mb-0">Gestisci le vendite e le transazioni</p>
            </div>
            <div>
                <button class="btn btn-outline-secondary me-2" id="newTransactionBtn" title="Inizia una nuova transazione">
                    <i class="bi bi-plus-circle"></i> Nuova Transazione
                </button>
                <button class="btn btn-outline-secondary" id="searchProductBtn" title="Cerca un prodotto">
                    <i class="bi bi-search"></i> Cerca Prodotto
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonna sinistra: Selezione prodotti -->
        <div class="col-md-3">
            <!-- Selezione gestore -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Gestori</h5>
                    <button class="btn btn-sm btn-outline-light" id="toggleGestoriBtn" title="Espandi/Comprimi">
                        <i class="bi bi-chevron-down"></i>
                    </button>
                </div>
                <div class="card-body p-0" id="gestoriContainer">
                    <div class="list-group list-group-flush">
                        <a href="?module=pos" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo !isset($_GET['gestore']) ? 'active' : ''; ?>">
                            <span><i class="bi bi-grid-3x3-gap"></i> Tutti i gestori</span>
                            <span class="badge bg-primary rounded-pill"><?php echo count($sim_products) + count($devices_products) + count($recharges_products); ?></span>
                        </a>
                        <?php foreach ($gestori as $key => $name): ?>
                        <a href="?module=pos&gestore=<?php echo $key; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo isset($_GET['gestore']) && $_GET['gestore'] == $key ? 'active' : ''; ?>">
                            <span>
                                <?php 
                                $icon = 'building';
                                switch($key) {
                                    case 'fastweb': $icon = 'wifi'; break;
                                    case 'iliad': $icon = 'phone'; break;
                                    case 'windtre': $icon = 'broadcast'; break;
                                    case 'skywifi': $icon = 'cloud'; break;
                                    case 'skytv': $icon = 'tv'; break;
                                    case 'pianetafibra': $icon = 'globe'; break;
                                }
                                ?>
                                <i class="bi bi-<?php echo $icon; ?>"></i> <?php echo $name; ?>
                            </span>
                            <?php 
                            $count = count(getProdottiSicuri($pdo, $key));
                            if ($count > 0): 
                            ?>
                            <span class="badge bg-primary rounded-pill"><?php echo $count; ?></span>
                            <?php endif; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Prodotti -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-box"></i> Prodotti</h5>
                </div>
                <div class="card-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="quickSearchProduct" placeholder="Cerca prodotto...">
                        <button class="btn btn-outline-secondary" type="button" id="quickSearchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                    
                    <ul class="nav nav-tabs product-tabs" id="productTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="sim-tab" data-bs-toggle="tab" data-bs-target="#sim" type="button" role="tab" aria-controls="sim" aria-selected="true">
                                <i class="bi bi-sim"></i> SIM
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="devices-tab" data-bs-toggle="tab" data-bs-target="#devices" type="button" role="tab" aria-controls="devices" aria-selected="false">
                                <i class="bi bi-phone"></i> Dispositivi
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="recharges-tab" data-bs-toggle="tab" data-bs-target="#recharges" type="button" role="tab" aria-controls="recharges" aria-selected="false">
                                <i class="bi bi-cash"></i> Ricariche
                            </button>
                        </li>
                    </ul>
                    <div class="tab-content p-2" id="productTabsContent">
                        <!-- Tab SIM -->
                        <div class="tab-pane fade show active" id="sim" role="tabpanel" aria-labelledby="sim-tab">
                            <?php if (empty($sim_products)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Nessuna SIM disponibile per questo gestore.
                            </div>
                            <?php else: ?>
                            <div class="row row-cols-1 g-2 product-grid">
                                <?php foreach ($sim_products as $product): ?>
                                <div class="col product-item" data-search="<?php echo strtolower($product['nome'] . ' ' . $product['codice']); ?>">
                                    <div class="card product-card h-100 <?php echo $product['quantita'] <= 0 ? 'border-danger' : ''; ?>" data-id="<?php echo $product['id']; ?>" data-has-serial="<?php echo hasSerialNumbers($pdo, $product['id']) ? '1' : '0'; ?>">
                                        <div class="card-body p-2">
                                            <h6 class="card-title"><?php echo htmlspecialchars($product['nome']); ?></h6>
                                            <p class="card-text mb-0 small text-muted"><?php echo htmlspecialchars($product['codice']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="badge bg-<?php echo $product['quantita'] > 10 ? 'success' : ($product['quantita'] > 0 ? 'warning' : 'danger'); ?>">
                                                    <?php echo $product['quantita']; ?> disponibili
                                                </span>
                                                <span class="fw-bold"><?php echo number_format($product['prezzo'], 2); ?> €</span>
                                            </div>
                                        </div>
                                        <div class="card-footer p-0 bg-transparent border-0">
                                            <button class="btn btn-sm btn-primary w-100 add-to-cart-btn" <?php echo $product['quantita'] <= 0 ? 'disabled' : ''; ?>>
                                                <i class="bi bi-cart-plus"></i> Aggiungi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tab Dispositivi -->
                        <div class="tab-pane fade" id="devices" role="tabpanel" aria-labelledby="devices-tab">
                            <?php if (empty($devices_products)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Nessun dispositivo disponibile per questo gestore.
                            </div>
                            <?php else: ?>
                            <div class="row row-cols-1 g-2 product-grid">
                                <?php foreach ($devices_products as $product): ?>
                                <div class="col product-item" data-search="<?php echo strtolower($product['nome'] . ' ' . $product['codice']); ?>">
                                    <div class="card product-card h-100 <?php echo $product['quantita'] <= 0 ? 'border-danger' : ''; ?>" data-id="<?php echo $product['id']; ?>" data-has-serial="<?php echo hasSerialNumbers($pdo, $product['id']) ? '1' : '0'; ?>">
                                        <div class="card-body p-2">
                                            <h6 class="card-title"><?php echo htmlspecialchars($product['nome']); ?></h6>
                                            <p class="card-text mb-0 small text-muted"><?php echo htmlspecialchars($product['codice']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="badge bg-<?php echo $product['quantita'] > 10 ? 'success' : ($product['quantita'] > 0 ? 'warning' : 'danger'); ?>">
                                                    <?php echo $product['quantita']; ?> disponibili
                                                </span>
                                                <span class="fw-bold"><?php echo number_format($product['prezzo'], 2); ?> €</span>
                                            </div>
                                        </div>
                                        <div class="card-footer p-0 bg-transparent border-0">
                                            <button class="btn btn-sm btn-primary w-100 add-to-cart-btn" <?php echo $product['quantita'] <= 0 ? 'disabled' : ''; ?>>
                                                <i class="bi bi-cart-plus"></i> Aggiungi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tab Ricariche -->
                        <div class="tab-pane fade" id="recharges" role="tabpanel" aria-labelledby="recharges-tab">
                            <?php if (empty($recharges_products)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Nessuna ricarica disponibile per questo gestore.
                            </div>
                            <?php else: ?>
                            <div class="row row-cols-1 g-2 product-grid">
                                <?php foreach ($recharges_products as $product): ?>
                                <div class="col product-item" data-search="<?php echo strtolower($product['nome'] . ' ' . $product['codice']); ?>">
                                    <div class="card product-card h-100" data-id="<?php echo $product['id']; ?>">
                                        <div class="card-body p-2">
                                            <h6 class="card-title"><?php echo htmlspecialchars($product['nome']); ?></h6>
                                            <p class="card-text mb-0 small text-muted"><?php echo htmlspecialchars($product['codice']); ?></p>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="badge bg-info">Ricarica</span>
                                                <span class="fw-bold"><?php echo number_format($product['prezzo'], 2); ?> €</span>
                                            </div>
                                        </div>
                                        <div class="card-footer p-0 bg-transparent border-0">
                                            <button class="btn btn-sm btn-primary w-100 add-to-cart-btn">
                                                <i class="bi bi-cart-plus"></i> Aggiungi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonna centrale: Carrello -->
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-cart"></i> Carrello</h5>
                    <div>
                        <button class="btn btn-sm btn-outline-light" id="clearCart" title="Svuota carrello">
                            <i class="bi bi-trash"></i> Svuota
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive cart-items-container">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Codice</th>
                                    <th>Descrizione</th>
                                    <th class="text-center">Q.tà</th>
                                    <th class="text-end">Prezzo</th>
                                    <th class="text-end">Totale</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="cartItems">
                                <?php if (empty($_SESSION['cart'])): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                                        <p class="mb-0 mt-2">Il carrello è vuoto</p>
                                        <p class="text-muted small">Aggiungi prodotti dal catalogo</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($_SESSION['cart'] as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['codice']); ?></td>
                                    <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm quantity-control">
                                            <button class="btn btn-outline-secondary quantity-decrease" type="button" data-id="<?php echo $item['id']; ?>">-</button>
                                            <input type="text" class="form-control text-center quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                            <button class="btn btn-outline-secondary quantity-increase" type="button" data-id="<?php echo $item['id']; ?>">+</button>
                                        </div>
                                    </td>
                                    <td class="text-end"><?php echo number_format($item['prezzo'], 2); ?> €</td>
                                    <td class="text-end"><?php echo number_format($item['prezzo'] * $item['quantity'], 2); ?> €</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-danger remove-item" data-id="<?php echo $item['id']; ?>" title="Rimuovi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount" class="form-label">Sconto</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="discount" min="0" step="0.01" value="0">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Metodo di pagamento</label>
                                <select class="form-select" id="paymentMethod">
                                    <?php foreach ($metodi_pagamento as $key => $name): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body p-2">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td>Subtotale</td>
                                            <td class="text-end" id="subtotal"><?php echo number_format($totals['subtotal'], 2); ?> €</td>
                                        </tr>
                                        <tr>
                                            <td>IVA</td>
                                            <td class="text-end" id="iva"><?php echo number_format($totals['iva'], 2); ?> €</td>
                                        </tr>
                                        <tr>
                                            <td>Sconto</td>
                                            <td class="text-end" id="discountAmount">0.00 €</td>
                                        </tr>
                                        <tr class="fw-bold">
                                            <td>Totale</td>
                                            <td class="text-end" id="total"><?php echo number_format($totals['total'], 2); ?> €</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-3">
                        <div class="btn-group">
                            <button class="btn btn-primary" id="checkoutBtn" <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>>
                                <i class="bi bi-receipt"></i> Emetti scontrino
                            </button>
                            <button class="btn btn-outline-primary" id="saveTransactionBtn" <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>>
                                <i class="bi bi-save"></i> Salva
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Riepilogo transazione -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Riepilogo Transazione</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Operatore:</strong> <?php echo htmlspecialchars($user_nome . ' ' . $user_cognome); ?></p>
                            <p><strong>Data:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Numero prodotti:</strong> <span id="numProducts">0</span></p>
                            <p><strong>Totale transazione:</strong> <span id="totalTransaction">0.00 €</span></p>
                        </div>
                    </div>
                    <div class="alert alert-info mt-2 mb-0">
                        <i class="bi bi-lightbulb"></i> <strong>Suggerimento:</strong> Puoi cercare un cliente esistente inserendo il suo codice fiscale o numero di telefono.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Colonna destra: Dati cliente -->
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Dati cliente</h5>
                    <button class="btn btn-sm btn-outline-light" id="searchClientBtn" title="Cerca cliente">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="card-body">
                    <form id="customerForm">
                        <div class="mb-3">
                            <label for="codiceFiscale" class="form-label">Codice Fiscale</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
                                <input type="text" class="form-control" id="codiceFiscale" name="codice_fiscale">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="nome" name="nome">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="cognome" class="form-label">Cognome</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="cognome" name="cognome">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Cellulare</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-phone"></i></span>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="abilitaSms" name="abilita_sms">
                                    <label class="form-check-label" for="abilitaSms">
                                        Abilita SMS
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="abilitaEmail" name="abilita_email">
                                    <label class="form-check-label" for="abilitaEmail">
                                        Abilita E-Mail
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tesseraFidelity" class="form-label">Tessera Fidelity</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                <input type="text" class="form-control" id="tesseraFidelity" name="tessera_fidelity">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dataRicontatto" class="form-label">Data Ricontatto</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                <input type="date" class="form-control" id="dataRicontatto" name="data_ricontatto">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Note</label>
                            <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                        </div>
                        
                        <div class="d-grid">
                            <button type="button" class="btn btn-outline-secondary" id="clearCustomerBtn">
                                <i class="bi bi-eraser"></i> Pulisci campi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per la conferma della transazione -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="transactionModalLabel"><i class="bi bi-check-circle"></i> Transazione Completata</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle-fill"></i> Transazione completata con successo!
                </div>
                <div class="card mb-3">
                    <div class="card-body">
                        <p><strong>Numero scontrino:</strong> <span id="receiptNumber">-</span></p>
                        <p><strong>Data:</strong> <span id="receiptDate">-</span></p>
                        <p><strong>Totale:</strong> <span id="receiptTotal">-</span></p>
                        <p><strong>Metodo pagamento:</strong> <span id="receiptPayment">-</span></p>
                    </div>
                </div>
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Lo scontrino è stato salvato nel sistema e può essere ristampato in qualsiasi momento dalla sezione Transazioni.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="printReceiptBtn">
                    <i class="bi bi-printer"></i> Stampa scontrino
                </button>
                <button type="button" class="btn btn-success" id="newSaleBtn">
                    <i class="bi bi-plus-circle"></i> Nuova vendita
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per la ricerca prodotti -->
<div class="modal fade" id="searchProductModal" tabindex="-1" aria-labelledby="searchProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="searchProductModalLabel"><i class="bi bi-search"></i> Ricerca Prodotto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="searchProductInput" placeholder="Cerca per codice, nome o descrizione">
                    <button class="btn btn-primary" type="button" id="searchProductButton">
                        <i class="bi bi-search"></i> Cerca
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Codice</th>
                                <th>Nome</th>
                                <th>Gestore</th>
                                <th>Tipo</th>
                                <th class="text-end">Prezzo</th>
                                <th class="text-center">Disponibilità</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="searchProductResults">
                            <!-- I risultati della ricerca verranno inseriti qui -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per la selezione IMEI/ICCID -->
<div class="modal fade" id="serialNumberModal" tabindex="-1" aria-labelledby="serialNumberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="serialNumberModalLabel"><i class="bi bi-upc-scan"></i> Seleziona IMEI/ICCID</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="serialNumberSelect" class="form-label">Seleziona un IMEI/ICCID disponibile:</label>
                    <select class="form-select" id="serialNumberSelect">
                        <!-- Le opzioni verranno caricate dinamicamente -->
                    </select>
                </div>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Selezionando un IMEI/ICCID, questo verrà associato alla vendita e non sarà più disponibile per altre vendite.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="confirmSerialBtn">Conferma</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per la ricerca clienti -->
<div class="modal fade" id="searchClientModal" tabindex="-1" aria-labelledby="searchClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="searchClientModalLabel"><i class="bi bi-search"></i> Ricerca Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="searchClientInput" placeholder="Cerca per nome, cognome, codice fiscale o telefono">
                    <button class="btn btn-primary" type="button" id="searchClientButton">
                        <i class="bi bi-search"></i> Cerca
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Cognome</th>
                                <th>Codice Fiscale</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="searchClientResults">
                            <!-- I risultati della ricerca verranno inseriti qui -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Stili personalizzati per il POS */
.pos-container {
    margin-bottom: 30px;
}

.product-grid {
    max-height: 500px;
    overflow-y: auto;
    padding-right: 5px;
}

.product-card {
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid #dee2e6;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border-color: #2e8b57;
}

.product-card .card-title {
    font-size: 0.9rem;
    font-weight: 600;
}

.cart-items-container {
    max-height: 300px;
    overflow-y: auto;
}

.quantity-control {
    max-width: 100px;
    margin: 0 auto;
}

.quantity-input {
    text-align: center;
}

/* Stili per la ricerca rapida */
.product-item {
    transition: all 0.3s ease;
}

.product-item.hidden {
    display: none;
}

/* Stili per i tab */
.product-tabs .nav-link {
    color: #495057;
    font-size: 0.9rem;
}

.product-tabs .nav-link.active {
    color: #2e8b57;
    font-weight: 600;
    border-bottom: 2px solid #2e8b57;
}

.product-tabs .nav-link i {
    margin-right: 5px;
}

/* Stili per i bottoni */
.add-to-cart-btn {
    border-radius: 0 0 5px 5px;
    transition: all 0.2s ease;
}

.add-to-cart-btn:hover {
    background-color: #2e8b57;
    border-color: #2e8b57;
}

/* Stili per i modali */
.modal-header {
    border-bottom: 0;
}

.modal-footer {
    border-top: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestione ricerca rapida prodotti
    const quickSearchInput = document.getElementById('quickSearchProduct');
    quickSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const productItems = document.querySelectorAll('.product-item');
        
        productItems.forEach(item => {
            const searchText = item.dataset.search;
            if (searchText.includes(searchTerm) || searchTerm === '') {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });
    
    // Gestione toggle gestori
    const toggleGestoriBtn = document.getElementById('toggleGestoriBtn');
    const gestoriContainer = document.getElementById('gestoriContainer');
    
    toggleGestoriBtn.addEventListener('click', function() {
        if (gestoriContainer.style.display === 'none') {
            gestoriContainer.style.display = 'block';
            this.innerHTML = '<i class="bi bi-chevron-up"></i>';
        } else {
            gestoriContainer.style.display = 'none';
            this.innerHTML = '<i class="bi bi-chevron-down"></i>';
        }
    });
    
    // Aggiunta prodotti al carrello
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productId = productCard.dataset.id;
            const hasSerial = productCard.dataset.hasSerial === '1';
            
            if (hasSerial) {
                // Apri il modal per selezionare IMEI/ICCID
                loadSerialNumbers(productId);
            } else {
                // Aggiungi direttamente al carrello
                addProductToCart(productId);
            }
        });
    });
    
    // Funzione per caricare i numeri seriali
    function loadSerialNumbers(productId) {
        // Qui andrebbe implementata la chiamata AJAX per caricare i numeri seriali
        // Per ora simuliamo con dati di esempio
        const serialSelect = document.getElementById('serialNumberSelect');
        serialSelect.innerHTML = '';
        
        // Simulazione dati
        const dummyData = [
            { id: 1, codice: '123456789012345' },
            { id: 2, codice: '234567890123456' },
            { id: 3, codice: '345678901234567' }
        ];
        
        dummyData.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.codice;
            serialSelect.appendChild(option);
        });
        
        // Salva il productId per l'uso successivo
        document.getElementById('confirmSerialBtn').dataset.productId = productId;
        
        // Mostra il modal
        const serialModal = new bootstrap.Modal(document.getElementById('serialNumberModal'));
        serialModal.show();
    }
    
    // Conferma selezione numero seriale
    document.getElementById('confirmSerialBtn').addEventListener('click', function() {
        const productId = this.dataset.productId;
        const serialId = document.getElementById('serialNumberSelect').value;
        
        // Qui andrebbe implementata la logica per associare il numero seriale
        // Per ora aggiungiamo semplicemente il prodotto al carrello
        addProductToCart(productId);
        
        // Chiudi il modal
        bootstrap.Modal.getInstance(document.getElementById('serialNumberModal')).hide();
    });
    
    // Aggiornamento contatori carrello
    function updateCartCounters() {
        // Simulazione conteggio prodotti
        let numProducts = 0;
        let totalTransaction = 0;
        
        // Qui andrebbe implementata la logica per contare i prodotti e calcolare il totale
        // Per ora usiamo valori di esempio
        document.getElementById('numProducts').textContent = '3';
        document.getElementById('totalTransaction').textContent = '150.00 €';
    }
    
    // Chiamata iniziale per aggiornare i contatori
    updateCartCounters();
    
    // Gestione ricerca clienti
    const clientiSuggerimenti = <?php echo $clienti_json; ?>;
    
    document.getElementById('searchClientBtn').addEventListener('click', function() {
        const searchClientModal = new bootstrap.Modal(document.getElementById('searchClientModal'));
        searchClientModal.show();
    });
    
    document.getElementById('searchClientButton').addEventListener('click', function() {
        const searchTerm = document.getElementById('searchClientInput').value.toLowerCase();
        const resultsContainer = document.getElementById('searchClientResults');
        resultsContainer.innerHTML = '';
        
        // Filtra i clienti in base al termine di ricerca
        const filteredClients = clientiSuggerimenti.filter(client => {
            return (
                (client.nome && client.nome.toLowerCase().includes(searchTerm)) ||
                (client.cognome && client.cognome.toLowerCase().includes(searchTerm)) ||
                (client.codice_fiscale && client.codice_fiscale.toLowerCase().includes(searchTerm)) ||
                (client.telefono && client.telefono.toLowerCase().includes(searchTerm))
            );
        });
        
        if (filteredClients.length === 0) {
            resultsContainer.innerHTML = '<tr><td colspan="6" class="text-center py-3">Nessun cliente trovato</td></tr>';
            return;
        }
        
        // Mostra i risultati
        filteredClients.forEach(client => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${client.nome || '-'}</td>
                <td>${client.cognome || '-'}</td>
                <td>${client.codice_fiscale || '-'}</td>
                <td>${client.telefono || '-'}</td>
                <td>${client.email || '-'}</td>
                <td>
                    <button class="btn btn-sm btn-primary select-client" data-id="${client.id}">
                        <i class="bi bi-check"></i> Seleziona
                    </button>
                </td>
            `;
            resultsContainer.appendChild(row);
        });
        
        // Aggiungi event listener ai pulsanti di selezione
        document.querySelectorAll('.select-client').forEach(button => {
            button.addEventListener('click', function() {
                const clientId = this.dataset.id;
                const client = clientiSuggerimenti.find(c => c.id == clientId);
                
                // Popola il form cliente
                document.getElementById('codiceFiscale').value = client.codice_fiscale || '';
                document.getElementById('nome').value = client.nome || '';
                document.getElementById('cognome').value = client.cognome || '';
                document.getElementById('telefono').value = client.telefono || '';
                document.getElementById('email').value = client.email || '';
                
                // Chiudi il modal
                bootstrap.Modal.getInstance(document.getElementById('searchClientModal')).hide();
            });
        });
    });
    
    // Pulisci campi cliente
    document.getElementById('clearCustomerBtn').addEventListener('click', function() {
        document.getElementById('customerForm').reset();
    });
    
    // Funzione per aggiungere prodotto al carrello (simulata)
    function addProductToCart(productId) {
        // Qui andrebbe implementata la chiamata AJAX per aggiungere il prodotto al carrello
        console.log('Aggiunto prodotto ID:', productId);
        
        // Simulazione aggiornamento carrello
        alert('Prodotto aggiunto al carrello!');
        
        // Aggiorna i contatori
        updateCartCounters();
    }
});
</script>

