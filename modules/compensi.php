<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Ottieni i dati dell'utente corrente
$user_id = $_SESSION['user_id'] ?? 0;
$user_nome = $_SESSION['nome'] ?? '';
$user_cognome = $_SESSION['cognome'] ?? '';
$user_ruolo = $_SESSION['ruolo'] ?? '';
$negozio_id = $_SESSION['negozio_id'] ?? null;

// Verifica i permessi (solo amministratori e responsabili possono modificare i compensi)
$can_edit = in_array($user_ruolo, ['amministratore', 'responsabile']);

// Ottieni i filtri dalla query string
$operatore_filter = isset($_GET['operatore']) ? $_GET['operatore'] : '';
$periodo_filter = isset($_GET['periodo']) ? $_GET['periodo'] : 'current_month';
$stato_filter = isset($_GET['stato']) ? $_GET['stato'] : 'all';
$tipo_filter = isset($_GET['tipo']) ? $_GET['tipo'] : '';

// Calcola le date in base al periodo selezionato
$date_range = [];
switch ($periodo_filter) {
    case 'current_month':
        $date_range['start'] = date('Y-m-01');
        $date_range['end'] = date('Y-m-t');
        $date_range['label'] = 'Mese corrente (' . date('F Y') . ')';
        break;
    case 'previous_month':
        $date_range['start'] = date('Y-m-01', strtotime('-1 month'));
        $date_range['end'] = date('Y-m-t', strtotime('-1 month'));
        $date_range['label'] = 'Mese precedente (' . date('F Y', strtotime('-1 month')) . ')';
        break;
    case 'current_quarter':
        $quarter = ceil(date('n') / 3);
        $date_range['start'] = date('Y-' . (($quarter - 1) * 3 + 1) . '-01');
        $date_range['end'] = date('Y-' . ($quarter * 3) . '-' . date('t', strtotime('Y-' . ($quarter * 3) . '-01')));
        $date_range['label'] = 'Trimestre corrente (Q' . $quarter . ' ' . date('Y') . ')';
        break;
    case 'current_year':
        $date_range['start'] = date('Y-01-01');
        $date_range['end'] = date('Y-12-31');
        $date_range['label'] = 'Anno corrente (' . date('Y') . ')';
        break;
    case 'custom':
        $date_range['start'] = isset($_GET['data_da']) ? $_GET['data_da'] : date('Y-m-01');
        $date_range['end'] = isset($_GET['data_a']) ? $_GET['data_a'] : date('Y-m-t');
        $date_range['label'] = 'Periodo personalizzato';
        break;
    default:
        $date_range['start'] = date('Y-m-01');
        $date_range['end'] = date('Y-m-t');
        $date_range['label'] = 'Mese corrente (' . date('F Y') . ')';
}

// Tipi di compenso
$tipi_compenso = [
    'attivazione' => 'Attivazione',
    'rinnovo' => 'Rinnovo',
    'vendita' => 'Vendita',
    'bonus' => 'Bonus',
    'target' => 'Target',
    'altro' => 'Altro'
];

// Stati di pagamento
$stati_pagamento = [
    'pendente' => 'Pendente',
    'approvato' => 'Approvato',
    'pagato' => 'Pagato',
    'rifiutato' => 'Rifiutato'
];

// Funzione per ottenere gli operatori
function getOperatori($pdo, $negozio_id = null) {
    try {
        $sql = "SELECT id, nome, cognome, ruolo, negozio_id FROM operatori WHERE attivo = 1";
        $params = [];
        
        if ($negozio_id) {
            $sql .= " AND negozio_id = ?";
            $params[] = $negozio_id;
        }
        
        $sql .= " ORDER BY cognome, nome";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Errore nel recupero degli operatori: " . $e->getMessage());
        return [];
    }
}

// Funzione per ottenere i compensi operatore
function getCompensiOperatore($pdo, $filters = []) {
    try {
        $sql = "
            SELECT c.*, 
                   o.nome as operatore_nome, 
                   o.cognome as operatore_cognome,
                   t.numero_scontrino,
                   l.nome as listino_nome
            FROM compensi_operatore c
            LEFT JOIN operatori o ON c.operatore_id = o.id
            LEFT JOIN transazioni t ON c.transazione_id = t.id
            LEFT JOIN listini_operatori l ON c.listino_id = l.id
            WHERE 1=1
        ";
        $params = [];
        
        // Filtra per operatore
        if (!empty($filters['operatore_id'])) {
            $sql .= " AND c.operatore_id = ?";
            $params[] = $filters['operatore_id'];
        }
        
        // Filtra per periodo
        if (!empty($filters['data_da'])) {
            $sql .= " AND c.data >= ?";
            $params[] = $filters['data_da'];
        }
        
        if (!empty($filters['data_a'])) {
            $sql .= " AND c.data <= ?";
            $params[] = $filters['data_a'];
        }
        
        // Filtra per stato
        if (!empty($filters['stato']) && $filters['stato'] !== 'all') {
            $sql .= " AND c.stato = ?";
            $params[] = $filters['stato'];
        }
        
        // Filtra per tipo
        if (!empty($filters['tipo'])) {
            $sql .= " AND c.tipo = ?";
            $params[] = $filters['tipo'];
        }
        
        // Ordina per data decrescente
        $sql .= " ORDER BY c.data DESC, c.id DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Errore nel recupero dei compensi: " . $e->getMessage());
        return [];
    }
}

// Funzione per calcolare i totali dei compensi
function calcolaTotaliCompensi($compensi) {
    $totali = [
        'totale' => 0,
        'pendente' => 0,
        'approvato' => 0,
        'pagato' => 0,
        'rifiutato' => 0,
        'per_tipo' => []
    ];
    
    foreach ($compensi as $compenso) {
        $importo = floatval($compenso['importo']);
        $totali['totale'] += $importo;
        
        // Aggiorna i totali per stato
        $totali[$compenso['stato']] += $importo;
        
        // Aggiorna i totali per tipo
        if (!isset($totali['per_tipo'][$compenso['tipo']])) {
            $totali['per_tipo'][$compenso['tipo']] = 0;
        }
        $totali['per_tipo'][$compenso['tipo']] += $importo;
    }
    
    return $totali;
}

// Ottieni gli operatori
$operatori = getOperatori($pdo, $user_ruolo === 'responsabile' ? $negozio_id : null);

// Ottieni i compensi in base ai filtri
$filters = [
    'operatore_id' => $operatore_filter,
    'data_da' => $date_range['start'],
    'data_a' => $date_range['end'],
    'stato' => $stato_filter,
    'tipo' => $tipo_filter
];

// Se l'utente è un operatore normale, mostra solo i suoi compensi
if ($user_ruolo === 'operatore') {
    $filters['operatore_id'] = $user_id;
}

$compensi = getCompensiOperatore($pdo, $filters);
$totali_compensi = calcolaTotaliCompensi($compensi);

// Funzione per formattare l'importo
function formatImporto($importo) {
    return number_format($importo, 2, ',', '.') . ' €';
}

// Funzione per ottenere la classe del badge in base allo stato
function getStatoBadgeClass($stato) {
    switch ($stato) {
        case 'pendente': return 'warning';
        case 'approvato': return 'info';
        case 'pagato': return 'success';
        case 'rifiutato': return 'danger';
        default: return 'secondary';
    }
}

// Funzione per ottenere l'icona in base al tipo di compenso
function getTipoIcon($tipo) {
    switch ($tipo) {
        case 'attivazione': return 'play-circle';
        case 'rinnovo': return 'arrow-repeat';
        case 'vendita': return 'cart';
        case 'bonus': return 'star';
        case 'target': return 'bullseye';
        default: return 'tag';
    }
}
?>

<div class="compensi-container">
    <!-- Header della pagina -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-currency-euro"></i> Compensi Operatore</h2>
                <p class="text-muted mb-0">Gestisci e monitora i compensi degli operatori</p>
            </div>
            <?php if ($can_edit): ?>
            <div>
                <button class="btn btn-primary" id="addCompensoBtn" data-bs-toggle="modal" data-bs-target="#compensoModal">
                    <i class="bi bi-plus-circle"></i> Nuovo Compenso
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Riepilogo compensi -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Totale Compensi</h5>
                        <div class="stats-icon bg-primary-light">
                            <i class="bi bi-cash-stack text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo formatImporto($totali_compensi['totale']); ?></h3>
                    <p class="text-muted mb-0"><?php echo count($compensi); ?> compensi nel periodo</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Pagati</h5>
                        <div class="stats-icon bg-success-light">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo formatImporto($totali_compensi['pagato']); ?></h3>
                    <p class="text-muted mb-0">
                        <?php 
                        $percentuale_pagati = $totali_compensi['totale'] > 0 ? 
                            round(($totali_compensi['pagato'] / $totali_compensi['totale']) * 100) : 0;
                        echo $percentuale_pagati . '% del totale';
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">In Attesa</h5>
                        <div class="stats-icon bg-warning-light">
                            <i class="bi bi-hourglass-split text-warning"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo formatImporto($totali_compensi['pendente'] + $totali_compensi['approvato']); ?></h3>
                    <p class="text-muted mb-0">
                        <?php 
                        $percentuale_attesa = $totali_compensi['totale'] > 0 ? 
                            round((($totali_compensi['pendente'] + $totali_compensi['approvato']) / $totali_compensi['totale']) * 100) : 0;
                        echo $percentuale_attesa . '% del totale';
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Periodo</h5>
                        <div class="stats-icon bg-info-light">
                            <i class="bi bi-calendar-range text-info"></i>
                        </div>
                    </div>
                    <h6 class="mb-1"><?php echo $date_range['label']; ?></h6>
                    <p class="text-muted mb-0">
                        <?php echo date('d/m/Y', strtotime($date_range['start'])); ?> - 
                        <?php echo date('d/m/Y', strtotime($date_range['end'])); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtri -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-funnel"></i> Filtri</h5>
        </div>
        <div class="card-body">
            <form id="filterForm" method="get" action="index.php">
                <input type="hidden" name="module" value="compensi">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="operatore" class="form-label">Operatore</label>
                        <select class="form-select" id="operatore" name="operatore">
                            <option value="">Tutti gli operatori</option>
                            <?php foreach ($operatori as $operatore): ?>
                            <option value="<?php echo $operatore['id']; ?>" <?php echo $operatore_filter == $operatore['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($operatore['cognome'] . ' ' . $operatore['nome']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="periodo" class="form-label">Periodo</label>
                        <select class="form-select" id="periodo" name="periodo">
                            <option value="current_month" <?php echo $periodo_filter === 'current_month' ? 'selected' : ''; ?>>Mese corrente</option>
                            <option value="previous_month" <?php echo $periodo_filter === 'previous_month' ? 'selected' : ''; ?>>Mese precedente</option>
                            <option value="current_quarter" <?php echo $periodo_filter === 'current_quarter' ? 'selected' : ''; ?>>Trimestre corrente</option>
                            <option value="current_year" <?php echo $periodo_filter === 'current_year' ? 'selected' : ''; ?>>Anno corrente</option>
                            <option value="custom" <?php echo $periodo_filter === 'custom' ? 'selected' : ''; ?>>Personalizzato</option>
                        </select>
                    </div>
                    <div class="col-md-3 date-range <?php echo $periodo_filter !== 'custom' ? 'd-none' : ''; ?>">
                        <label for="data_da" class="form-label">Data da</label>
                        <input type="date" class="form-control" id="data_da" name="data_da" value="<?php echo $date_range['start']; ?>">
                    </div>
                    <div class="col-md-3 date-range <?php echo $periodo_filter !== 'custom' ? 'd-none' : ''; ?>">
                        <label for="data_a" class="form-label">Data a</label>
                        <input type="date" class="form-control" id="data_a" name="data_a" value="<?php echo $date_range['end']; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="stato" class="form-label">Stato</label>
                        <select class="form-select" id="stato" name="stato">
                            <option value="all" <?php echo $stato_filter === 'all' ? 'selected' : ''; ?>>Tutti gli stati</option>
                            <?php foreach ($stati_pagamento as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php echo $stato_filter === $key ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-select" id="tipo" name="tipo">
                            <option value="">Tutti i tipi</option>
                            <?php foreach ($tipi_compenso as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php echo $tipo_filter === $key ? 'selected' : ''; ?>>
                                <?php echo $value; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-funnel"></i> Filtra
                        </button>
                        <a href="index.php?module=compensi" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabella Compensi -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table"></i> Elenco Compensi</h5>
            <span class="badge bg-primary"><?php echo count($compensi); ?> compensi trovati</span>
        </div>
        <div class="card-body p-0">
            <?php if (empty($compensi)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> Nessun compenso trovato con i filtri selezionati.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Operatore</th>
                            <th>Tipo</th>
                            <th>Descrizione</th>
                            <th class="text-end">Importo</th>
                            <th>Stato</th>
                            <th class="text-end">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($compensi as $compenso): ?>
                        <tr>
                            <td><?php echo $compenso['id']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($compenso['data'])); ?></td>
                            <td>
                                <?php echo htmlspecialchars($compenso['operatore_cognome'] . ' ' . $compenso['operatore_nome']); ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">
                                    <i class="bi bi-<?php echo getTipoIcon($compenso['tipo']); ?>"></i>
                                    <?php echo $tipi_compenso[$compenso['tipo']] ?? ucfirst($compenso['tipo']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($compenso['descrizione'])): ?>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?php echo htmlspecialchars($compenso['descrizione']); ?>">
                                    <?php echo htmlspecialchars($compenso['descrizione']); ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                                
                                <?php if (!empty($compenso['transazione_id'])): ?>
                                <span class="badge bg-info">
                                    <i class="bi bi-receipt"></i> Scontrino: <?php echo $compenso['numero_scontrino']; ?>
                                </span>
                                <?php endif; ?>
                                
                                <?php if (!empty($compenso['listino_id'])): ?>
                                <span class="badge bg-secondary">
                                    <i class="bi bi-file-text"></i> Listino: <?php echo $compenso['listino_nome']; ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold"><?php echo formatImporto($compenso['importo']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo getStatoBadgeClass($compenso['stato']); ?>">
                                    <?php echo $stati_pagamento[$compenso['stato']] ?? ucfirst($compenso['stato']); ?>
                                </span>
                                <?php if ($compenso['stato'] === 'pagato' && !empty($compenso['data_pagamento'])): ?>
                                <small class="d-block text-muted">
                                    Pagato il: <?php echo date('d/m/Y', strtotime($compenso['data_pagamento'])); ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-info view-compenso" data-id="<?php echo $compenso['id']; ?>" title="Visualizza dettagli">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <?php if ($can_edit && $compenso['stato'] !== 'pagato'): ?>
                                    <button type="button" class="btn btn-sm btn-primary edit-compenso" data-id="<?php echo $compenso['id']; ?>" title="Modifica">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-compenso" data-id="<?php echo $compenso['id']; ?>" title="Elimina">
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
                    <button class="btn btn-outline-primary" id="exportCompensiBtn">
                        <i class="bi bi-file-earmark-excel"></i> Esporta
                    </button>
                    <?php if ($can_edit): ?>
                    <button class="btn btn-outline-success ms-2" id="approvaCompensiBtn">
                        <i class="bi bi-check-circle"></i> Approva Selezionati
                    </button>
                    <button class="btn btn-outline-info ms-2" id="pagaCompensiBtn">
                        <i class="bi bi-cash"></i> Segna Come Pagati
                    </button>
                    <?php endif; ?>
                </div>
                <div>
                    <small class="text-muted">Totale: <?php echo formatImporto($totali_compensi['totale']); ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico distribuzione compensi -->
    <?php if (!empty($compensi)): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Distribuzione per Tipo</h5>
                </div>
                <div class="card-body">
                    <canvas id="tipiChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Distribuzione per Stato</h5>
                </div>
                <div class="card-body">
                    <canvas id="statiChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal per aggiungere/modificare compenso -->
<div class="modal fade" id="compensoModal" tabindex="-1" aria-labelledby="compensoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="compensoModalLabel"><i class="bi bi-plus-circle"></i> Nuovo Compenso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="compensoForm">
                    <input type="hidden" id="compenso_id" name="id" value="">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="operatore_id" class="form-label">Operatore *</label>
                            <select class="form-select" id="operatore_id" name="operatore_id" required>
                                <option value="">Seleziona operatore</option>
                                <?php foreach ($operatori as $operatore): ?>
                                <option value="<?php echo $operatore['id']; ?>">
                                    <?php echo htmlspecialchars($operatore['cognome'] . ' ' . $operatore['nome']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="data" class="form-label">Data *</label>
                            <input type="date" class="form-control" id="data" name="data" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tipo" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipo_modal" name="tipo" required>
                                <?php foreach ($tipi_compenso as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="importo" class="form-label">Importo (€) *</label>
                            <input type="number" class="form-control" id="importo" name="importo" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="stato" class="form-label">Stato *</label>
                            <select class="form-select" id="stato_modal" name="stato" required>
                                <?php foreach ($stati_pagamento as $key => $value): ?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="data_pagamento" class="form-label">Data Pagamento</label>
                            <input type="date" class="form-control" id="data_pagamento" name="data_pagamento">
                            <small class="form-text text-muted">Compilare solo se lo stato è "Pagato"</small>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descrizione" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizione" name="descrizione" rows="3"></textarea>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="transazione_id" class="form-label">ID Transazione</label>
                            <input type="number" class="form-control" id="transazione_id" name="transazione_id" min="1">
                            <small class="form-text text-muted">Opzionale: collega a una transazione esistente</small>
                        </div>
                        <div class="col-md-6">
                            <label for="listino_id" class="form-label">ID Listino</label>
                            <input type="number" class="form-control" id="listino_id" name="listino_id" min="1">
                            <small class="form-text text-muted">Opzionale: collega a un listino esistente</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> I campi contrassegnati con * sono obbligatori.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="saveCompensoBtn">Salva</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal per visualizzare i dettagli del compenso -->
<div class="modal fade" id="viewCompensoModal" tabindex="-1" aria-labelledby="viewCompensoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="viewCompensoModalLabel"><i class="bi bi-info-circle"></i> Dettagli Compenso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Informazioni Generali</h5>
                            <p><strong>ID:</strong> <span id="view_id"></span></p>
                            <p><strong>Operatore:</strong> <span id="view_operatore"></span></p>
                            <p><strong>Data:</strong> <span id="view_data"></span></p>
                            <p><strong>Tipo:</strong> <span id="view_tipo"></span></p>
                            <p><strong>Descrizione:</strong> <span id="view_descrizione"></span></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Dettagli Economici</h5>
                            <p><strong>Importo:</strong> <span id="view_importo" class="text-success fw-bold"></span></p>
                            <p><strong>Stato:</strong> <span id="view_stato"></span></p>
                            <p><strong>Data Pagamento:</strong> <span id="view_data_pagamento"></span></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <h5 class="border-bottom pb-2">Collegamenti</h5>
                            <p><strong>Transazione:</strong> <span id="view_transazione"></span></p>
                            <p><strong>Listino:</strong> <span id="view_listino"></span></p>
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
<div class="modal fade" id="deleteCompensoModal" tabindex="-1" aria-labelledby="deleteCompensoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCompensoModalLabel"><i class="bi bi-exclamation-triangle"></i> Conferma Eliminazione</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questo compenso?</p>
                <p>Questa azione non può essere annullata.</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Attenzione: l'eliminazione di un compenso potrebbe influire sulle statistiche e sui report.
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
/* Stili personalizzati per i compensi */
.compensi-container {
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

/* Stili per le card statistiche */
.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.bg-primary-light {
    background-color: rgba(13, 110, 253, 0.1);
}

.bg-success-light {
    background-color: rgba(25, 135, 84, 0.1);
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1);
}

.bg-info-light {
    background-color: rgba(13, 202, 240, 0.1);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inizializzazione date nel form
    document.getElementById('data').valueAsDate = new Date();
    
    // Gestione del modal per aggiungere/modificare compenso
    const compensoModal = new bootstrap.Modal(document.getElementById('compensoModal'));
    const viewCompensoModal = new bootstrap.Modal(document.getElementById('viewCompensoModal'));
    const deleteCompensoModal = new bootstrap.Modal(document.getElementById('deleteCompensoModal'));
    
    // Gestione del campo periodo personalizzato
    document.getElementById('periodo').addEventListener('change', function() {
        const dateRangeFields = document.querySelectorAll('.date-range');
        if (this.value === 'custom') {
            dateRangeFields.forEach(field => field.classList.remove('d-none'));
        } else {
            dateRangeFields.forEach(field => field.classList.add('d-none'));
        }
    });
    
    // Apertura modal per nuovo compenso
    document.getElementById('addCompensoBtn').addEventListener('click', function() {
        // Reset form
        document.getElementById('compensoForm').reset();
        document.getElementById('compenso_id').value = '';
        document.getElementById('compensoModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Nuovo Compenso';
        document.getElementById('data').valueAsDate = new Date();
        document.getElementById('stato_modal').value = 'pendente';
    });
    
    // Salvataggio compenso
    document.getElementById('saveCompensoBtn').addEventListener('click', function() {
        const form = document.getElementById('compensoForm');
        
        // Validazione base
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Raccolta dati dal form
        const formData = new FormData(form);
        const compensoData = Object.fromEntries(formData.entries());
        
        // Validazione aggiuntiva
        if (compensoData.stato === 'pagato' && !compensoData.data_pagamento) {
            alert('Per i compensi pagati è necessario specificare la data di pagamento.');
            return;
        }
        
        // Qui andrebbe implementata la chiamata AJAX per salvare il compenso
        // Per ora simuliamo un salvataggio con successo
        
        alert('Compenso salvato con successo!');
        compensoModal.hide();
        
        // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
        // location.reload();
    });
    
    // Gestione pulsanti di modifica
    document.querySelectorAll('.edit-compenso').forEach(button => {
        button.addEventListener('click', function() {
            const compensoId = this.dataset.id;
            
            // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del compenso
            // Per ora simuliamo con dati di esempio
            const dummyData = {
                id: compensoId,
                operatore_id: 1,
                data: '2023-05-15',
                tipo: 'attivazione',
                importo: 25.00,
                stato: 'pendente',
                data_pagamento: '',
                descrizione: 'Compenso per attivazione contratto',
                transazione_id: 123,
                listino_id: 45
            };
            
            // Popola il form con i dati
            document.getElementById('compenso_id').value = dummyData.id;
            document.getElementById('operatore_id').value = dummyData.operatore_id;
            document.getElementById('data').value = dummyData.data;
            document.getElementById('tipo_modal').value = dummyData.tipo;
            document.getElementById('importo').value = dummyData.importo;
            document.getElementById('stato_modal').value = dummyData.stato;
            document.getElementById('data_pagamento').value = dummyData.data_pagamento;
            document.getElementById('descrizione').value = dummyData.descrizione;
            document.getElementById('transazione_id').value = dummyData.transazione_id;
            document.getElementById('listino_id').value = dummyData.listino_id;
            
            // Aggiorna il titolo del modal
            document.getElementById('compensoModalLabel').innerHTML = '<i class="bi bi-pencil"></i> Modifica Compenso';
            
            // Mostra il modal
            compensoModal.show();
        });
    });
    
    // Gestione pulsanti di visualizzazione
    document.querySelectorAll('.view-compenso').forEach(button => {
        button.addEventListener('click', function() {
            const compensoId = this.dataset.id;
            
            // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del compenso
            // Per ora simuliamo con dati di esempio
            const dummyData = {
                id: compensoId,
                operatore: 'Rossi Mario',
                data: '15/05/2023',
                tipo: 'Attivazione',
                importo: '25,00 €',
                stato: '<span class="badge bg-warning">Pendente</span>',
                data_pagamento: '-',
                descrizione: 'Compenso per attivazione contratto',
                transazione: 'Scontrino #123456 del 15/05/2023',
                listino: 'Fastweb Mobile 100GB'
            };
            
            // Popola il modal con i dati
            document.getElementById('view_id').textContent = dummyData.id;
            document.getElementById('view_operatore').textContent = dummyData.operatore;
            document.getElementById('view_data').textContent = dummyData.data;
            document.getElementById('view_tipo').textContent = dummyData.tipo;
            document.getElementById('view_importo').textContent = dummyData.importo;
            document.getElementById('view_stato').innerHTML = dummyData.stato;
            document.getElementById('view_data_pagamento').textContent = dummyData.data_pagamento;
            document.getElementById('view_descrizione').textContent = dummyData.descrizione || 'Nessuna descrizione';
            document.getElementById('view_transazione').textContent = dummyData.transazione || 'Nessuna transazione collegata';
            document.getElementById('view_listino').textContent = dummyData.listino || 'Nessun listino collegato';
            
            // Salva l'ID per il pulsante di modifica
            document.getElementById('editFromViewBtn').dataset.id = compensoId;
            
            // Mostra il modal
            viewCompensoModal.show();
        });
    });
    
    // Gestione pulsante modifica dal modal di visualizzazione
    document.getElementById('editFromViewBtn').addEventListener('click', function() {
        const compensoId = this.dataset.id;
        
        // Chiudi il modal di visualizzazione
        viewCompensoModal.hide();
        
        // Simula il click sul pulsante di modifica corrispondente
        document.querySelector(`.edit-compenso[data-id="${compensoId}"]`).click();
    });
    
    // Gestione pulsanti di eliminazione
    document.querySelectorAll('.delete-compenso').forEach(button => {
        button.addEventListener('click', function() {
            const compensoId = this.dataset.id;
            
            // Salva l'ID per il pulsante di conferma
            document.getElementById('confirmDeleteBtn').dataset.id = compensoId;
            
            // Mostra il modal
            deleteCompensoModal.show();
        });
    });
    
    // Conferma eliminazione
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        const compensoId = this.dataset.id;
        
        // Qui andrebbe implementata la chiamata AJAX per eliminare il compenso
        // Per ora simuliamo un'eliminazione con successo
        
        alert('Compenso eliminato con successo!');
        deleteCompensoModal.hide();
        
        // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
        // location.reload();
    });
    
    // Esportazione compensi
    document.getElementById('exportCompensiBtn').addEventListener('click', function() {
        // Qui andrebbe implementata la logica per esportare i compensi
        alert('Funzionalità di esportazione in fase di implementazione');
    });
    
    // Approvazione compensi selezionati
    document.getElementById('approvaCompensiBtn').addEventListener('click', function() {
        // Qui andrebbe implementata la logica per approvare i compensi selezionati
        alert('Funzionalità di approvazione in fase di implementazione');
    });
    
    // Pagamento compensi selezionati
    document.getElementById('pagaCompensiBtn').addEventListener('click', function() {
        // Qui andrebbe implementata la logica per segnare come pagati i compensi selezionati
        alert('Funzionalità di pagamento in fase di implementazione');
    });
    
    // Inizializzazione grafici se ci sono dati
    <?php if (!empty($compensi)): ?>
    // Dati per il grafico dei tipi
    const tipiData = {
        labels: [
            <?php 
            foreach ($totali_compensi['per_tipo'] as $tipo => $importo) {
                echo "'" . ($tipi_compenso[$tipo] ?? ucfirst($tipo)) . "', ";
            }
            ?>
        ],
        datasets: [{
            data: [
                <?php 
                foreach ($totali_compensi['per_tipo'] as $importo) {
                    echo $importo . ", ";
                }
                ?>
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    // Dati per il grafico degli stati
    const statiData = {
        labels: ['Pendente', 'Approvato', 'Pagato', 'Rifiutato'],
        datasets: [{
            data: [
                <?php echo $totali_compensi['pendente']; ?>,
                <?php echo $totali_compensi['approvato']; ?>,
                <?php echo $totali_compensi['pagato']; ?>,
                <?php echo $totali_compensi['rifiutato']; ?>
            ],
            backgroundColor: [
                'rgba(255, 193, 7, 0.7)',
                'rgba(13, 202, 240, 0.7)',
                'rgba(25, 135, 84, 0.7)',
                'rgba(220, 53, 69, 0.7)'
            ],
            borderColor: [
                'rgba(255, 193, 7, 1)',
                'rgba(13, 202, 240, 1)',
                'rgba(25, 135, 84, 1)',
                'rgba(220, 53, 69, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    // Configurazione comune per i grafici
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right',
            }
        }
    };
    
    // Creazione grafico tipi
    const tipiChart = new Chart(
        document.getElementById('tipiChart').getContext('2d'),
        {
            type: 'pie',
            data: tipiData,
            options: chartOptions
        }
    );
    
    // Creazione grafico stati
    const statiChart = new Chart(
        document.getElementById('statiChart').getContext('2d'),
        {
            type: 'pie',
            data: statiData,
            options: chartOptions
        }
    );
    <?php endif; ?>
});
</script>

