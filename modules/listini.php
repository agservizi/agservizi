<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Ottieni i dati dell'utente corrente
$user_id = $_SESSION['user_id'] ?? 0;
$user_nome = $_SESSION['nome'] ?? '';
$user_cognome = $_SESSION['cognome'] ?? '';
$user_ruolo = $_SESSION['ruolo'] ?? '';
$negozio_id = $_SESSION['negozio_id'] ?? null;

// Verifica i permessi (solo amministratori e responsabili possono modificare i listini)
$can_edit = in_array($user_ruolo, ['amministratore', 'responsabile']);

// Ottieni i filtri dalla query string
$gestore_filter = isset($_GET['gestore']) ? $_GET['gestore'] : '';
$tipo_filter = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$attivo_filter = isset($_GET['attivo']) ? $_GET['attivo'] : 'all';
$search_filter = isset($_GET['search']) ? $_GET['search'] : '';

// Ottieni i gestori disponibili
$gestori = getProviders();

// Tipi di offerte disponibili
$tipi_offerta = [
    'mobile' => 'Mobile',
    'fisso' => 'Fisso',
    'convergente' => 'Convergente',
    'tv' => 'TV',
    'accessori' => 'Accessori'
];

// Funzione per ottenere i listini operatori con gestione errori
function getListiniOperatori($pdo, $filters = []) {
    try {
        $sql = "
            SELECT l.*, 
                   CASE 
                       WHEN l.data_fine < CURDATE() THEN 'scaduto'
                       WHEN l.attivo = 0 THEN 'inattivo'
                       ELSE 'attivo' 
                   END AS stato_effettivo
            FROM listini_operatori l
            WHERE 1=1
        ";
        $params = [];
        
        // Applica i filtri
        if (!empty($filters['gestore'])) {
            $sql .= " AND l.gestore = ?";
            $params[] = $filters['gestore'];
        }
        
        if (!empty($filters['tipo'])) {
            $sql .= " AND l.tipo_offerta = ?";
            $params[] = $filters['tipo'];
        }
        
        if (!empty($filters['attivo']) && $filters['attivo'] !== 'all') {
            if ($filters['attivo'] === 'active') {
                $sql .= " AND l.attivo = 1 AND (l.data_fine IS NULL OR l.data_fine >= CURDATE())";
            } elseif ($filters['attivo'] === 'inactive') {
                $sql .= " AND (l.attivo = 0 OR l.data_fine < CURDATE())";
            }
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (l.nome LIKE ? OR l.descrizione LIKE ?)";
            $search_term = '%' . $filters['search'] . '%';
            $params[] = $search_term;
            $params[] = $search_term;
        }
        
        // Ordina per data di inizio decrescente (più recenti prima)
        $sql .= " ORDER BY l.data_inizio DESC, l.nome ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        // Log dell'errore
        error_log("Errore nel recupero dei listini operatori: " . $e->getMessage());
        return [];
    }
}

// Ottieni i listini operatori in base ai filtri
$filters = [
    'gestore' => $gestore_filter,
    'tipo' => $tipo_filter,
    'attivo' => $attivo_filter,
    'search' => $search_filter
];

$listini = getListiniOperatori($pdo, $filters);

// Funzione per formattare il prezzo
function formatPrice($price) {
    return number_format($price, 2, ',', '.') . ' €';
}

// Funzione per ottenere il nome del gestore
function getProviderName($provider_key, $providers) {
    return $providers[$provider_key] ?? $provider_key;
}

// Funzione per ottenere lo stato del listino
function getListinoStatus($listino) {
    $oggi = new DateTime();
    $data_fine = $listino['data_fine'] ? new DateTime($listino['data_fine']) : null;
    
    if (!$listino['attivo']) {
        return ['status' => 'inattivo', 'badge' => 'secondary', 'icon' => 'x-circle'];
    }
    
    if ($data_fine && $data_fine < $oggi) {
        return ['status' => 'scaduto', 'badge' => 'danger', 'icon' => 'calendar-x'];
    }
    
    return ['status' => 'attivo', 'badge' => 'success', 'icon' => 'check-circle'];
}

// Funzione per calcolare i giorni rimanenti
function getDaysRemaining($date_str) {
    if (!$date_str) return null;
    
    $oggi = new DateTime();
    $data = new DateTime($date_str);
    $diff = $oggi->diff($data);
    
    if ($data < $oggi) {
        return -$diff->days; // Giorni passati (negativo)
    }
    
    return $diff->days; // Giorni rimanenti (positivo)
}
?>

<div class="listini-container">
    <!-- Header della pagina -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-file-text"></i> Listini Operatori</h2>
                <p class="text-muted mb-0">Gestisci le offerte e i compensi degli operatori</p>
            </div>
            <?php if ($can_edit): ?>
            <div>
                <button class="btn btn-primary" id="addListinoBtn" data-bs-toggle="modal" data-bs-target="#listinoModal">
                    <i class="bi bi-plus-circle"></i> Nuovo Listino
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filtri -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtri</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" method="get" action="index.php">
                <input type="hidden" name="module" value="listini">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="gestore" class="form-label">Gestore</label>
                        <select class="form-select" id="gestore" name="gestore">
                            <option value="">Tutti i gestori</option>
                            <?php foreach ($gestori as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo $gestore_filter === $key ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo Offerta</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="">Tutti i tipi</option>
                            <?php foreach ($tipi_offerta as $key => $name): ?>
                            <option value="<?php echo $key; ?>" <?php echo $tipo_filter === $key ? 'selected' : ''; ?>>
                                <?php echo $name; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="attivo" class="form-label">Stato</label>
                        <select class="form-select" id="attivo" name="attivo">
                            <option value="all" <?php echo $attivo_filter === 'all' ? 'selected' : ''; ?>>Tutti</option>
                            <option value="active" <?php echo $attivo_filter === 'active' ? 'selected' : ''; ?>>Solo attivi</option>
                            <option value="inactive" <?php echo $attivo_filter === 'inactive' ? 'selected' : ''; ?>>Solo inattivi/scaduti</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="search" class="form-label">Cerca</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Nome o descrizione" value="<?php echo htmlspecialchars($search_filter); ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Filtra
                        </button>
                        <a href="index.php?module=listini" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabella Listini -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table"></i> Listini Disponibili</h5>
            <span class="badge bg-primary"><?php echo count($listini); ?> listini trovati</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($listini)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> Nessun listino trovato con i filtri selezionati.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nome</th>
                            <th>Gestore</th>
                            <th>Tipo</th>
                            <th class="text-end">Prezzo Attivazione</th>
                            <th class="text-end">Canone Mensile</th>
                            <th class="text-end">Compenso</th>
                            <th>Validità</th>
                            <th>Stato</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($listini as $listino): 
                            $status = getListinoStatus($listino);
                            $days_remaining = getDaysRemaining($listino['data_fine']);
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($listino['nome']); ?></div>
                                <?php if (!empty($listino['descrizione'])): ?>
                                <small class="text-muted d-block text-truncate" style="max-width: 250px;" title="<?php echo htmlspecialchars($listino['descrizione']); ?>">
                                    <?php echo htmlspecialchars($listino['descrizione']); ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $gestore_name = getProviderName($listino['gestore'], $gestori);
                                $icon = 'building';
                                switch($listino['gestore']) {
                                    case 'fastweb': $icon = 'wifi'; break;
                                    case 'iliad': $icon = 'phone'; break;
                                    case 'windtre': $icon = 'broadcast'; break;
                                    case 'skywifi': $icon = 'cloud'; break;
                                    case 'skytv': $icon = 'tv'; break;
                                    case 'pianetafibra': $icon = 'globe'; break;
                                }
                                ?>
                                <span><i class="bi bi-<?php echo $icon; ?>"></i> <?php echo htmlspecialchars($gestore_name); ?></span>
                            </td>
                            <td>
                                <?php 
                                $tipo_name = $tipi_offerta[$listino['tipo_offerta']] ?? $listino['tipo_offerta'];
                                $tipo_icon = 'tag';
                                switch($listino['tipo_offerta']) {
                                    case 'mobile': $tipo_icon = 'phone'; break;
                                    case 'fisso': $tipo_icon = 'house'; break;
                                    case 'convergente': $tipo_icon = 'arrows-angle-contract'; break;
                                    case 'tv': $tipo_icon = 'tv'; break;
                                    case 'accessori': $tipo_icon = 'headset'; break;
                                }
                                ?>
                                <span><i class="bi bi-<?php echo $tipo_icon; ?>"></i> <?php echo htmlspecialchars($tipo_name); ?></span>
                            </td>
                            <td class="text-end"><?php echo formatPrice($listino['prezzo_attivazione']); ?></td>
                            <td class="text-end"><?php echo formatPrice($listino['canone_mensile']); ?></td>
                            <td class="text-end">
                                <span class="fw-bold text-success"><?php echo formatPrice($listino['compenso_operatore']); ?></span>
                            </td>
                            <td>
                                <div>Dal: <?php echo date('d/m/Y', strtotime($listino['data_inizio'])); ?></div>
                                <?php if ($listino['data_fine']): ?>
                                <div>
                                    Al: <?php echo date('d/m/Y', strtotime($listino['data_fine'])); ?>
                                    <?php if ($days_remaining !== null): ?>
                                        <?php if ($days_remaining < 0): ?>
                                            <span class="badge bg-danger">Scaduto da <?php echo abs($days_remaining); ?> giorni</span>
                                        <?php elseif ($days_remaining <= 7): ?>
                                            <span class="badge bg-warning">Scade tra <?php echo $days_remaining; ?> giorni</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                <div><span class="badge bg-info">Senza scadenza</span></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $status['badge']; ?>">
                                    <i class="bi bi-<?php echo $status['icon']; ?>"></i> 
                                    <?php echo ucfirst($status['status']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info view-listino" data-id="<?php echo $listino['id']; ?>" title="Visualizza dettagli">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if ($can_edit): ?>
                                    <button type="button" class="btn btn-sm btn-primary edit-listino" data-id="<?php echo $listino['id']; ?>" title="Modifica">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-listino" data-id="<?php echo $listino['id']; ?>" title="Elimina">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-outline-primary" id="exportListiniBtn">
                        <i class="bi bi-file-earmark-excel"></i> Esporta
                    </button>
                </div>
                <div>
                    <small class="text-muted">Ultimo aggiornamento: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal per aggiungere/modificare listino -->
<div class="modal fade" id="listinoModal" tabindex="-1" aria-labelledby="listinoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="listinoModalLabel"><i class="bi bi-file-earmark-plus"></i> Nuovo Listino</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="listinoForm">
                    <input type="hidden" id="listino_id" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="nome" class="form-label">Nome Offerta *</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-md-4">
                            <label for="gestore" class="form-label">Gestore *</label>
                            <select class="form-select" id="gestore_modal" name="gestore" required>
                                <?php foreach ($gestori as $key => $name): ?>
                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tipo_offerta" class="form-label">Tipo Offerta *</label>
                            <select class="form-select" id="tipo_offerta" name="tipo_offerta" required>
                                <?php foreach ($tipi_offerta as $key => $name): ?>
                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="prezzo_attivazione" class="form-label">Prezzo Attivazione (€) *</label>
                            <input type="number" class="form-control" id="prezzo_attivazione" name="prezzo_attivazione" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="canone_mensile" class="form-label">Canone Mensile (€) *</label>
                            <input type="number" class="form-control" id="canone_mensile" name="canone_mensile" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="compenso_operatore" class="form-label">Compenso Operatore (€) *</label>
                            <input type="number" class="form-control" id="compenso_operatore" name="compenso_operatore" step="0.01" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="data_inizio" class="form-label">Data Inizio *</label>
                            <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
                        </div>
                        <div class="col-md-4">
                            <label for="data_fine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="data_fine" name="data_fine">
                            <small class="form-text text-muted">Lasciare vuoto se senza scadenza</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="attivo" name="attivo" value="1" checked>
                        <label class="form-check-label" for="attivo">
                            Listino attivo
                        </label>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> I campi contrassegnati con * sono obbligatori.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="saveListinoBtn">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per visualizzare i dettagli del listino -->
<div class="modal fade" id="viewListinoModal" tabindex="-1" aria-labelledby="viewListinoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewListinoModalLabel"><i class="bi bi-info-circle"></i> Dettagli Listino</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Informazioni Generali</h5>
                            <p><strong>Nome:</strong> <span id="view_nome"></span></p>
                            <p><strong>Gestore:</strong> <span id="view_gestore"></span></p>
                            <p><strong>Tipo Offerta:</strong> <span id="view_tipo_offerta"></span></p>
                            <p><strong>Descrizione:</strong> <span id="view_descrizione"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Dettagli Economici</h5>
                            <p><strong>Prezzo Attivazione:</strong> <span id="view_prezzo_attivazione"></span></p>
                            <p><strong>Canone Mensile:</strong> <span id="view_canone_mensile"></span></p>
                            <p><strong>Compenso Operatore:</strong> <span id="view_compenso_operatore" class="text-success fw-bold"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Validità</h5>
                            <p><strong>Data Inizio:</strong> <span id="view_data_inizio"></span></p>
                            <p><strong>Data Fine:</strong> <span id="view_data_fine"></span></p>
                            <p><strong>Stato:</strong> <span id="view_stato"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Informazioni di Sistema</h5>
                            <p><strong>ID:</strong> <span id="view_id"></span></p>
                            <p><strong>Data Creazione:</strong> <span id="view_data_creazione"></span></p>
                            <p><strong>Ultima Modifica:</strong> <span id="view_data_modifica"></span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <?php if ($can_edit): ?>
                <button type="button" class="btn btn-primary" id="editFromViewBtn">Modifica</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal di conferma eliminazione -->
<div class="modal fade" id="deleteListinoModal" tabindex="-1" aria-labelledby="deleteListinoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteListinoModalLabel"><i class="bi bi-exclamation-triangle"></i> Conferma Eliminazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare il listino <strong id="delete_listino_name"></strong>?</p>
                <p>Questa azione non può essere annullata.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Attenzione: l'eliminazione di un listino potrebbe influire sui compensi degli operatori e sulle statistiche.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Elimina</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Stili personalizzati per i listini */
.listini-container {
    margin-bottom: 30px;
}

.table th, .table td {
    vertical-align: middle;
}

.table td {
    padding: 0.75rem;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.75em;
}

.modal-header {
    border-bottom: 0;
}

.modal-footer {
    border-top: 0;
}

.form-label {
    font-weight: 500;
}

.text-truncate {
    max-width: 200px;
    display: inline-block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inizializzazione date nel form
    document.getElementById('data_inizio').valueAsDate = new Date();
    
    // Gestione del modal per aggiungere/modificare listino
    const listinoModal = new bootstrap.Modal(document.getElementById('listinoModal'));
    const viewListinoModal = new bootstrap.Modal(document.getElementById('viewListinoModal'));
    const deleteListinoModal = new bootstrap.Modal(document.getElementById('deleteListinoModal'));
    
    // Apertura modal per nuovo listino
    document.getElementById('addListinoBtn').addEventListener('click', function() {
        // Reset form
        document.getElementById('listinoForm').reset();
        document.getElementById('listino_id').value = '';
        document.getElementById('listinoModalLabel').innerHTML = '<i class="bi bi-file-earmark-plus"></i> Nuovo Listino';
        document.getElementById('data_inizio').valueAsDate = new Date();
        document.getElementById('attivo').checked = true;
    });
    
    // Salvataggio listino
    document.getElementById('saveListinoBtn').addEventListener('click', function() {
        const form = document.getElementById('listinoForm');
        
        // Validazione base
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Raccolta dati dal form
        const formData = new FormData(form);
        const listinoData = Object.fromEntries(formData.entries());
        
        // Qui andrebbe implementata la chiamata AJAX per salvare il listino
        // Per ora simuliamo un salvataggio con successo
        
        alert('Listino salvato con successo!');
        listinoModal.hide();
        
        // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
        // location.reload();
    });
    
    // Gestione pulsanti di modifica
    document.querySelectorAll('.edit-listino').forEach(button => {
        button.addEventListener('click', function() {
            const listinoId = this.dataset.id;
            
            // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del listino
            // Per ora simuliamo con dati di esempio
            const dummyData = {
                id: listinoId,
                nome: 'Offerta Esempio',
                gestore: 'fastweb',
                tipo_offerta: 'mobile',
                descrizione: 'Descrizione di esempio',
                prezzo_attivazione: 10.00,
                canone_mensile: 9.99,
                compenso_operatore: 15.00,
                data_inizio: '2023-01-01',
                data_fine: '2023-12-31',
                attivo: true
            };
            
            // Popola il form con i dati
            document.getElementById('listino_id').value = dummyData.id;
            document.getElementById('nome').value = dummyData.nome;
            document.getElementById('gestore_modal').value = dummyData.gestore;
            document.getElementById('tipo_offerta').value = dummyData.tipo_offerta;
            document.getElementById('descrizione').value = dummyData.descrizione;
            document.getElementById('prezzo_attivazione').value = dummyData.prezzo_attivazione;
            document.getElementById('canone_mensile').value = dummyData.canone_mensile;
            document.getElementById('compenso_operatore').value = dummyData.compenso_operatore;
            document.getElementById('data_inizio').value = dummyData.data_inizio;
            document.getElementById('data_fine').value = dummyData.data_fine || '';
            document.getElementById('attivo').checked = dummyData.attivo;
            
            // Aggiorna il titolo del modal
            document.getElementById('listinoModalLabel').innerHTML = '<i class="bi bi-pencil"></i> Modifica Listino';
            
            // Mostra il modal
            listinoModal.show();
        });
    });
    
    // Gestione pulsanti di visualizzazione
    document.querySelectorAll('.view-listino').forEach(button => {
        button.addEventListener('click', function() {
            const listinoId = this.dataset.id;
            
            // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del listino
            // Per ora simuliamo con dati di esempio
            const dummyData = {
                id: listinoId,
                nome: 'Offerta Esempio',
                gestore: 'fastweb',
                gestore_nome: 'Fastweb',
                tipo_offerta: 'mobile',
                tipo_offerta_nome: 'Mobile',
                descrizione: 'Descrizione dettagliata dell\'offerta di esempio con tutti i dettagli.',
                prezzo_attivazione: 10.00,
                canone_mensile: 9.99,
                compenso_operatore: 15.00,
                data_inizio: '01/01/2023',
                data_fine: '31/12/2023',
                stato: 'attivo',
                data_creazione: '01/01/2023 10:00',
                data_modifica: '15/01/2023 14:30'
            };
            
            // Popola il modal con i dati
            document.getElementById('view_id').textContent = dummyData.id;
            document.getElementById('view_nome').textContent = dummyData.nome;
            document.getElementById('view_gestore').textContent = dummyData.gestore_nome;
            document.getElementById('view_tipo_offerta').textContent = dummyData.tipo_offerta_nome;
            document.getElementById('view_descrizione').textContent = dummyData.descrizione || 'Nessuna descrizione';
            document.getElementById('view_prezzo_attivazione').textContent = formatPrice(dummyData.prezzo_attivazione);
            document.getElementById('view_canone_mensile').textContent = formatPrice(dummyData.canone_mensile);
            document.getElementById('view_compenso_operatore').textContent = formatPrice(dummyData.compenso_operatore);
            document.getElementById('view_data_inizio').textContent = dummyData.data_inizio;
            document.getElementById('view_data_fine').textContent = dummyData.data_fine || 'Senza scadenza';
            document.getElementById('view_stato').innerHTML = `<span class="badge bg-success"><i class="bi bi-check-circle"></i> ${dummyData.stato.charAt(0).toUpperCase() + dummyData.stato.slice(1)}</span>`;
            document.getElementById('view_data_creazione').textContent = dummyData.data_creazione;
            document.getElementById('view_data_modifica').textContent = dummyData.data_modifica;
            
            // Salva l'ID per il pulsante di modifica
            document.getElementById('editFromViewBtn').dataset.id = listinoId;
            
            // Mostra il modal
            viewListinoModal.show();
        });
    });
    
    // Gestione pulsante modifica dal modal di visualizzazione
    document.getElementById('editFromViewBtn').addEventListener('click', function() {
        const listinoId = this.dataset.id;
        
        // Chiudi il modal di visualizzazione
        viewListinoModal.hide();
        
        // Simula il click sul pulsante di modifica corrispondente
        document.querySelector(`.edit-listino[data-id="${listinoId}"]`).click();
    });
    
    // Gestione pulsanti di eliminazione
    document.querySelectorAll('.delete-listino').forEach(button => {
        button.addEventListener('click', function() {
            const listinoId = this.dataset.id;
            const listinoName = this.closest('tr').querySelector('td:first-child div.fw-bold').textContent;
            
            // Popola il modal di conferma
            document.getElementById('delete_listino_name').textContent = listinoName;
            document.getElementById('confirmDeleteBtn').dataset.id = listinoId;
            
            // Mostra il modal
            deleteListinoModal.show();
        });
    });
    
    // Conferma eliminazione
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const listinoId = this.dataset.id;
        
        // Qui andrebbe implementata la chiamata AJAX per eliminare il listino
        // Per ora simuliamo un'eliminazione con successo
        
        alert('Listino eliminato con successo!');
        deleteListinoModal.hide();
        
        // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
        // location.reload();
    });
    
    // Esportazione listini
    document.getElementById('exportListiniBtn').addEventListener('click', function() {
        // Qui andrebbe implementata la logica per esportare i listini
        alert('Funzionalità di esportazione in fase di implementazione');
    });
    
    // Funzione per formattare il prezzo
    function formatPrice(price) {
        return parseFloat(price).toFixed(2).replace('.', ',') + ' €';
    }
});
</script>

