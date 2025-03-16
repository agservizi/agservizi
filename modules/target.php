<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Ottieni i dati dell'utente corrente
$user_id = $_SESSION['user_id'] ?? 0;
$user_nome = $_SESSION['nome'] ?? '';
$user_cognome = $_SESSION['cognome'] ?? '';
$user_ruolo = $_SESSION['ruolo'] ?? '';
$negozio_id = $_SESSION['negozio_id'] ?? null;

// Verifica i permessi (solo amministratori e responsabili possono modificare i target)
$can_edit = in_array($user_ruolo, ['amministratore', 'responsabile']);

// Ottieni i filtri dalla query string
$gestore_filter = isset($_GET['gestore']) ? $_GET['gestore'] : '';
$periodo_filter = isset($_GET['periodo']) ? $_GET['periodo'] : 'current';
$stato_filter = isset($_GET['stato']) ? $_GET['stato'] : 'all';
$negozio_filter = isset($_GET['negozio']) ? $_GET['negozio'] : '';
$operatore_filter = isset($_GET['operatore']) ? $_GET['operatore'] : '';

// Calcola le date in base al periodo selezionato
$date_range = [];
switch ($periodo_filter) {
   case 'current':
       $date_range['start'] = date('Y-m-01');
       $date_range['end'] = date('Y-m-t');
       $date_range['label'] = 'Mese corrente (' . date('F Y') . ')';
       break;
   case 'next':
       $date_range['start'] = date('Y-m-01', strtotime('+1 month'));
       $date_range['end'] = date('Y-m-t', strtotime('+1 month'));
       $date_range['label'] = 'Prossimo mese (' . date('F Y', strtotime('+1 month')) . ')';
       break;
   case 'quarter':
       $quarter = ceil(date('n') / 3);
       $date_range['start'] = date('Y-' . (($quarter - 1) * 3 + 1) . '-01');
       $date_range['end'] = date('Y-' . ($quarter * 3) . '-' . date('t', strtotime('Y-' . ($quarter * 3) . '-01')));
       $date_range['label'] = 'Trimestre corrente (Q' . $quarter . ' ' . date('Y') . ')';
       break;
   case 'year':
       $date_range['start'] = date('Y-01-01');
       $date_range['end'] = date('Y-12-31');
       $date_range['label'] = 'Anno corrente (' . date('Y') . ')';
       break;
   case 'past':
       $date_range['start'] = '2000-01-01';
       $date_range['end'] = date('Y-m-d', strtotime('yesterday'));
       $date_range['label'] = 'Target passati';
       break;
   case 'future':
       $date_range['start'] = date('Y-m-d', strtotime('tomorrow'));
       $date_range['end'] = '2099-12-31';
       $date_range['label'] = 'Target futuri';
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

// Ottieni i gestori disponibili
$gestori = getProviders();

// Tipi di target
$tipi_target = [
   'mobile' => 'Mobile',
   'fisso' => 'Fisso',
   'convergente' => 'Convergente',
   'tv' => 'TV',
   'accessori' => 'Accessori',
   'vendite' => 'Vendite Totali',
   'fatturato' => 'Fatturato'
];

// Stati di target
$stati_target = [
   'attivo' => 'Attivo',
   'completato' => 'Completato',
   'scaduto' => 'Scaduto',
   'annullato' => 'Annullato'
];

// Funzione per ottenere i negozi
function getNegozi($pdo) {
   try {
       $sql = "SELECT id, nome, citta FROM negozi WHERE attivo = 1 ORDER BY nome";
       $stmt = $pdo->prepare($sql);
       $stmt->execute();
       
       return $stmt->fetchAll();
   } catch (PDOException $e) {
       error_log("Errore nel recupero dei negozi: " . $e->getMessage());
       return [];
   }
}

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

// Funzione per ottenere i target
function getTarget($pdo, $filters = []) {
   try {
       $sql = "
           SELECT t.*, 
                  n.nome as negozio_nome, 
                  CONCAT(o.cognome, ' ', o.nome) as operatore_nome
           FROM target t
           LEFT JOIN negozi n ON t.negozio_id = n.id
           LEFT JOIN operatori o ON t.operatore_id = o.id
           WHERE 1=1
       ";
       $params = [];
       
       // Filtra per gestore
       if (!empty($filters['gestore'])) {
           $sql .= " AND t.gestore = ?";
           $params[] = $filters['gestore'];
       }
       
       // Filtra per periodo
       if (!empty($filters['data_da'])) {
           $sql .= " AND t.data_fine >= ?";
           $params[] = $filters['data_da'];
       }
       
       if (!empty($filters['data_a'])) {
           $sql .= " AND t.data_inizio <= ?";
           $params[] = $filters['data_a'];
       }
       
       // Filtra per stato
       if (!empty($filters['stato']) && $filters['stato'] !== 'all') {
           $sql .= " AND t.stato = ?";
           $params[] = $filters['stato'];
       }
       
       // Filtra per negozio
       if (!empty($filters['negozio_id'])) {
           $sql .= " AND t.negozio_id = ?";
           $params[] = $filters['negozio_id'];
       }
       
       // Filtra per operatore
       if (!empty($filters['operatore_id'])) {
           $sql .= " AND t.operatore_id = ?";
           $params[] = $filters['operatore_id'];
       }
       
       // Ordina per data di inizio decrescente
       $sql .= " ORDER BY t.data_inizio DESC, t.id DESC";
       
       $stmt = $pdo->prepare($sql);
       $stmt->execute($params);
       
       return $stmt->fetchAll();
   } catch (PDOException $e) {
       error_log("Errore nel recupero dei target: " . $e->getMessage());
       return [];
   }
}

// Funzione per calcolare lo stato effettivo di un target
function calcolaStatoTarget($target) {
   $oggi = new DateTime();
   $data_inizio = new DateTime($target['data_inizio']);
   $data_fine = new DateTime($target['data_fine']);
   
   // Se il target è stato annullato manualmente
   if ($target['stato'] === 'annullato') {
       return 'annullato';
   }
   
   // Se il target è stato completato (raggiunto >= obiettivo)
   if ($target['raggiunto'] >= $target['obiettivo']) {
       return 'completato';
   }
   
   // Se la data di fine è passata
   if ($data_fine < $oggi) {
       return 'scaduto';
   }
   
   // Se la data di inizio è nel futuro
   if ($data_inizio > $oggi) {
       return 'futuro';
   }
   
   // Altrimenti è attivo
   return 'attivo';
}

// Funzione per calcolare la percentuale di completamento
function calcolaPercentualeCompletamento($target) {
   if ($target['obiettivo'] <= 0) {
       return 0;
   }
   
   $percentuale = ($target['raggiunto'] / $target['obiettivo']) * 100;
   return min(100, $percentuale); // Limita al 100%
}

// Funzione per ottenere la classe del badge in base allo stato
function getStatoBadgeClass($stato) {
   switch ($stato) {
       case 'attivo': return 'success';
       case 'futuro': return 'info';
       case 'completato': return 'primary';
       case 'scaduto': return 'warning';
       case 'annullato': return 'danger';
       default: return 'secondary';
   }
}

// Funzione per ottenere l'icona in base al tipo di target
function getTipoIcon($tipo) {
   switch ($tipo) {
       case 'mobile': return 'phone';
       case 'fisso': return 'house';
       case 'convergente': return 'arrows-angle-contract';
       case 'tv': return 'tv';
       case 'accessori': return 'headset';
       case 'vendite': return 'cart';
       case 'fatturato': return 'cash-stack';
       default: return 'bullseye';
   }
}

// Funzione per formattare l'importo
function formatImporto($importo) {
   return number_format($importo, 2, ',', '.') . ' €';
}

// Ottieni i negozi
$negozi = getNegozi($pdo);

// Ottieni gli operatori
$operatori = getOperatori($pdo, $user_ruolo === 'responsabile' ? $negozio_id : null);

// Ottieni i target in base ai filtri
$filters = [
   'gestore' => $gestore_filter,
   'data_da' => $date_range['start'],
   'data_a' => $date_range['end'],
   'stato' => $stato_filter,
   'negozio_id' => $negozio_filter,
   'operatore_id' => $operatore_filter
];

// Se l'utente è un responsabile, mostra solo i target del suo negozio
if ($user_ruolo === 'responsabile') {
   $filters['negozio_id'] = $negozio_id;
}

// Se l'utente è un operatore normale, mostra solo i suoi target
if ($user_ruolo === 'operatore') {
   $filters['operatore_id'] = $user_id;
}

$targets = getTarget($pdo, $filters);

// Calcola statistiche sui target
$stats = [
   'totale' => count($targets),
   'attivi' => 0,
   'completati' => 0,
   'scaduti' => 0,
   'annullati' => 0,
   'premio_totale' => 0,
   'premio_guadagnato' => 0
];

foreach ($targets as &$target) {
   // Calcola lo stato effettivo
   $target['stato_effettivo'] = calcolaStatoTarget($target);
   
   // Calcola la percentuale di completamento
   $target['percentuale'] = calcolaPercentualeCompletamento($target);
   
   // Aggiorna le statistiche
   switch ($target['stato_effettivo']) {
       case 'attivo':
       case 'futuro':
           $stats['attivi']++;
           break;
       case 'completato':
           $stats['completati']++;
           $stats['premio_guadagnato'] += $target['premio_base'];
           // Aggiungi premio extra se è stato superato l'obiettivo
           if ($target['raggiunto'] > $target['obiettivo']) {
               $stats['premio_guadagnato'] += $target['premio_extra'];
           }
           break;
       case 'scaduto':
           $stats['scaduti']++;
           break;
       case 'annullato':
           $stats['annullati']++;
           break;
   }
   
   // Calcola il premio totale potenziale
   $stats['premio_totale'] += $target['premio_base'] + $target['premio_extra'];
}
?>

<div class="target-container">
   <!-- Header della pagina -->
   <div class="row mb-3">
       <div class="col-12 d-flex justify-content-between align-items-center">
           <div>
               <h2><i class="bi bi-bullseye"></i> Gestione Target</h2>
               <p class="text-muted mb-0">Monitora e gestisci gli obiettivi di vendita</p>
           </div>
           <?php if ($can_edit): ?>
           <div>
               <button class="btn btn-primary" id="addTargetBtn" data-bs-toggle="modal" data-bs-target="#targetModal">
                   <i class="bi bi-plus-circle"></i> Nuovo Target
               </button>
           </div>
           <?php endif; ?>
       </div>
   </div>

   <!-- Riepilogo target -->
   <div class="row mb-4">
       <div class="col-md-3 mb-3">
           <div class="card shadow-sm h-100">
               <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="card-title mb-0">Target Attivi</h5>
                       <div class="stats-icon bg-success-light">
                           <i class="bi bi-bullseye text-success"></i>
                       </div>
                   </div>
                   <h3 class="mb-1"><?php echo $stats['attivi']; ?></h3>
                   <p class="text-muted mb-0">
                       <?php 
                       $percentuale_attivi = $stats['totale'] > 0 ? 
                           round(($stats['attivi'] / $stats['totale']) * 100) : 0;
                       echo $percentuale_attivi . '% del totale';
                       ?>
                   </p>
               </div>
           </div>
       </div>
       <div class="col-md-3 mb-3">
           <div class="card shadow-sm h-100">
               <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="card-title mb-0">Target Completati</h5>
                       <div class="stats-icon bg-primary-light">
                           <i class="bi bi-check-circle text-primary"></i>
                       </div>
                   </div>
                   <h3 class="mb-1"><?php echo $stats['completati']; ?></h3>
                   <p class="text-muted mb-0">
                       <?php 
                       $percentuale_completati = $stats['totale'] > 0 ? 
                           round(($stats['completati'] / $stats['totale']) * 100) : 0;
                       echo $percentuale_completati . '% del totale';
                       ?>
                   </p>
               </div>
           </div>
       </div>
       <div class="col-md-3 mb-3">
           <div class="card shadow-sm h-100">
               <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="card-title mb-0">Premio Potenziale</h5>
                       <div class="stats-icon bg-warning-light">
                           <i class="bi bi-trophy text-warning"></i>
                       </div>
                   </div>
                   <h3 class="mb-1"><?php echo formatImporto($stats['premio_totale']); ?></h3>
                   <p class="text-muted mb-0">
                       <?php echo formatImporto($stats['premio_guadagnato']); ?> guadagnati finora
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
               <input type="hidden" name="module" value="target">
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
                       <label for="periodo" class="form-label">Periodo</label>
                       <select class="form-select" id="periodo" name="periodo">
                           <option value="current" <?php echo $periodo_filter === 'current' ? 'selected' : ''; ?>>Mese corrente</option>
                           <option value="next" <?php echo $periodo_filter === 'next' ? 'selected' : ''; ?>>Prossimo mese</option>
                           <option value="quarter" <?php echo $periodo_filter === 'quarter' ? 'selected' : ''; ?>>Trimestre corrente</option>
                           <option value="year" <?php echo $periodo_filter === 'year' ? 'selected' : ''; ?>>Anno corrente</option>
                           <option value="past" <?php echo $periodo_filter === 'past' ? 'selected' : ''; ?>>Target passati</option>
                           <option value="future" <?php echo $periodo_filter === 'future' ? 'selected' : ''; ?>>Target futuri</option>
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
                           <?php foreach ($stati_target as $key => $value): ?>
                           <option value="<?php echo $key; ?>" <?php echo $stato_filter === $key ? 'selected' : ''; ?>>
                               <?php echo $value; ?>
                           </option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   <?php if (in_array($user_ruolo, ['amministratore', 'responsabile'])): ?>
                   <div class="col-md-3">
                       <label for="negozio" class="form-label">Negozio</label>
                       <select class="form-select" id="negozio" name="negozio" <?php echo $user_ruolo === 'responsabile' ? 'disabled' : ''; ?>>
                           <option value="">Tutti i negozi</option>
                           <?php foreach ($negozi as $negozio): ?>
                           <option value="<?php echo $negozio['id']; ?>" <?php echo $negozio_filter == $negozio['id'] || ($user_ruolo === 'responsabile' && $negozio_id == $negozio['id']) ? 'selected' : ''; ?>>
                               <?php echo htmlspecialchars($negozio['nome'] . ' (' . $negozio['citta'] . ')'); ?>
                           </option>
                           <?php endforeach; ?>
                       </select>
                   </div>
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
                   <?php endif; ?>
               </div>
               <div class="row mt-3">
                   <div class="col-12 d-flex justify-content-end">
                       <button type="submit" class="btn btn-primary me-2">
                           <i class="bi bi-funnel"></i> Filtra
                       </button>
                       <a href="index.php?module=target" class="btn btn-outline-secondary">
                           <i class="bi bi-x-circle"></i> Reset
                       </a>
                   </div>
               </div>
           </form>
       </div>
   </div>

   <!-- Tabella Target -->
   <div class="card shadow-sm">
       <div class="card-header bg-light d-flex justify-content-between align-items-center">
           <h5 class="mb-0"><i class="bi bi-table"></i> Elenco Target</h5>
           <span class="badge bg-primary"><?php echo count($targets); ?> target trovati</span>
       </div>
       <div class="card-body p-0">
           <?php if (empty($targets)): ?>
           <div class="alert alert-info m-3">
               <i class="bi bi-info-circle"></i> Nessun target trovato con i filtri selezionati.
           </div>
           <?php else: ?>
           <div class="table-responsive">
               <table class="table table-hover table-striped mb-0">
                   <thead class="table-light">
                       <tr>
                           <th>Nome</th>
                           <th>Gestore</th>
                           <th>Tipo</th>
                           <th>Assegnato a</th>
                           <th>Periodo</th>
                           <th>Obiettivo</th>
                           <th>Progresso</th>
                           <th>Premio</th>
                           <th>Stato</th>
                           <th class="text-end">Azioni</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($targets as $target): ?>
                       <tr>
                           <td>
                               <div class="fw-bold"><?php echo htmlspecialchars($target['nome']); ?></div>
                           </td>
                           <td>
                               <?php 
                               $gestore_name = $gestori[$target['gestore']] ?? $target['gestore'];
                               $icon = 'building';
                               switch($target['gestore']) {
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
                               $tipo_name = $tipi_target[$target['tipo']] ?? ucfirst($target['tipo']);
                               $tipo_icon = getTipoIcon($target['tipo']);
                               ?>
                               <span><i class="bi bi-<?php echo $tipo_icon; ?>"></i> <?php echo htmlspecialchars($tipo_name); ?></span>
                           </td>
                           <td>
                               <?php if (!empty($target['negozio_nome'])): ?>
                               <div><i class="bi bi-shop"></i> <?php echo htmlspecialchars($target['negozio_nome']); ?></div>
                               <?php endif; ?>
                               <?php if (!empty($target['operatore_nome'])): ?>
                               <div><i class="bi bi-person"></i> <?php echo htmlspecialchars($target['operatore_nome']); ?></div>
                               <?php endif; ?>
                               <?php if (empty($target['negozio_nome']) && empty($target['operatore_nome'])): ?>
                               <span class="text-muted">Tutti</span>
                               <?php endif; ?>
                           </td>
                           <td>
                               <div>Dal: <?php echo date('d/m/Y', strtotime($target['data_inizio'])); ?></div>
                               <div>Al: <?php echo date('d/m/Y', strtotime($target['data_fine'])); ?></div>
                               <?php
                               $oggi = new DateTime();
                               $data_fine = new DateTime($target['data_fine']);
                               $data_inizio = new DateTime($target['data_inizio']);
                               $giorni_rimanenti = $oggi->diff($data_fine)->days;
                               $giorni_totali = $data_inizio->diff($data_fine)->days;
                               
                               if ($data_fine < $oggi) {
                                   echo '<span class="badge bg-danger">Scaduto</span>';
                               } elseif ($data_inizio > $oggi) {
                                   echo '<span class="badge bg-info">Inizia tra ' . $oggi->diff($data_inizio)->days . ' giorni</span>';
                               } elseif ($giorni_rimanenti <= 7) {
                                   echo '<span class="badge bg-warning">Scade tra ' . $giorni_rimanenti . ' giorni</span>';
                               } else {
                                   echo '<span class="badge bg-success">In corso</span>';
                               }
                               ?>
                           </td>
                           <td>
                               <div class="fw-bold"><?php echo $target['obiettivo']; ?></div>
                               <small class="text-muted">
                                   <?php 
                                   if ($target['tipo'] === 'fatturato') {
                                       echo formatImporto($target['obiettivo']);
                                   } else {
                                       echo $target['tipo'] === 'vendite' ? 'unità' : 'attivazioni';
                                   }
                                   ?>
                               </small>
                           </td>
                           <td>
                               <div class="d-flex justify-content-between align-items-center mb-1">
                                   <span><?php echo $target['raggiunto']; ?> / <?php echo $target['obiettivo']; ?></span>
                                   <span class="badge bg-<?php echo $target['percentuale'] >= 100 ? 'success' : ($target['percentuale'] >= 75 ? 'info' : ($target['percentuale'] >= 50 ? 'warning' : 'danger')); ?>">
                                       <?php echo round($target['percentuale']); ?>%
                                   </span>
                               </div>
                               <div class="progress" style="height: 6px;">
                                   <div class="progress-bar bg-<?php echo $target['percentuale'] >= 100 ? 'success' : ($target['percentuale'] >= 75 ? 'info' : ($target['percentuale'] >= 50 ? 'warning' : 'danger')); ?>" 
                                        role="progressbar" 
                                        style="width: <?php echo $target['percentuale']; ?>%" 
                                        aria-valuenow="<?php echo $target['percentuale']; ?>" 
                                        aria-valuemin="0" 
                                        aria-valuemax="100">
                                   </div>
                               </div>
                           </td>
                           <td>
                               <div>Base: <?php echo formatImporto($target['premio_base']); ?></div>
                               <div>Extra: <?php echo formatImporto($target['premio_extra']); ?></div>
                               <?php if ($target['percentuale'] >= 100): ?>
                               <span class="badge bg-success">Premio guadagnato!</span>
                               <?php endif; ?>
                           </td>
                           <td>
                               <span class="badge bg-<?php echo getStatoBadgeClass($target['stato_effettivo']); ?>">
                                   <?php echo ucfirst($target['stato_effettivo']); ?>
                               </span>
                           </td>
                           <td class="text-end">
                               <div class="btn-group">
                                   <button type="button" class="btn btn-sm btn-info view-target" data-id="<?php echo $target['id']; ?>" title="Visualizza dettagli">
                                       <i class="bi bi-eye"></i>
                                   </button>
                                   <?php if ($can_edit && in_array($target['stato_effettivo'], ['attivo', 'futuro'])): ?>
                                   <button type="button" class="btn btn-sm btn-primary edit-target" data-id="<?php echo $target['id']; ?>" title="Modifica">
                                       <i class="bi bi-pencil"></i>
                                   </button>
                                   <button type="button" class="btn btn-sm btn-success update-progress" data-id="<?php echo $target['id']; ?>" title="Aggiorna progresso">
                                       <i class="bi bi-arrow-up-circle"></i>
                                   </button>
                                   <button type="button" class="btn btn-sm btn-danger delete-target" data-id="<?php echo $target['id']; ?>" title="Elimina">
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
                   <button class="btn btn-outline-primary" id="exportTargetBtn">
                       <i class="bi bi-file-earmark-excel"></i> Esporta
                   </button>
                   <?php if ($can_edit): ?>
                   <button class="btn btn-outline-success ms-2" id="duplicateTargetBtn">
                       <i class="bi bi-copy"></i> Duplica Selezionati
                   </button>
                   <?php endif; ?>
               </div>
               <div>
                   <small class="text-muted">Totale: <?php echo count($targets); ?> target</small>
               </div>
           </div>
       </div>
   </div>

   <!-- Grafici di riepilogo -->
   <?php if (!empty($targets)): ?>
   <div class="row mt-4">
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
       <div class="col-md-6">
           <div class="card shadow-sm">
               <div class="card-header bg-light">
                   <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Target per Gestore</h5>
               </div>
               <div class="card-body">
                   <canvas id="gestoriChart" height="250"></canvas>
               </div>
           </div>
       </div>
   </div>
   <?php endif; ?>
</div>

<!-- Modal per aggiungere/modificare target -->
<div class="modal fade" id="targetModal" tabindex="-1" aria-labelledby="targetModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header bg-primary text-white">
               <h5 class="modal-title" id="targetModalLabel"><i class="bi bi-plus-circle"></i> Nuovo Target</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="targetForm">
                   <input type="hidden" id="target_id" name="id" value="">
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="nome" class="form-label">Nome Target *</label>
                           <input type="text" class="form-control" id="nome" name="nome" required>
                       </div>
                       <div class="col-md-6">
                           <label for="gestore_modal" class="form-label">Gestore *</label>
                           <select class="form-select" id="gestore_modal" name="gestore" required>
                               <?php foreach ($gestori as $key => $name): ?>
                               <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="tipo_modal" class="form-label">Tipo Target *</label>
                           <select class="form-select" id="tipo_modal" name="tipo" required>
                               <?php foreach ($tipi_target as $key => $value): ?>
                               <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="col-md-6">
                           <label for="obiettivo" class="form-label">Obiettivo *</label>
                           <input type="number" class="form-control" id="obiettivo" name="obiettivo" min="1" required>
                           <small class="form-text text-muted" id="obiettivo_hint">Numero di attivazioni da raggiungere</small>
                       </div>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="data_inizio" class="form-label">Data Inizio *</label>
                           <input type="date" class="form-control" id="data_inizio" name="data_inizio" required>
                       </div>
                       <div class="col-md-6">
                           <label for="data_fine" class="form-label">Data Fine *</label>
                           <input type="date" class="form-control" id="data_fine" name="data_fine" required>
                       </div>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="premio_base" class="form-label">Premio Base (€) *</label>
                           <input type="number" class="form-control" id="premio_base" name="premio_base" step="0.01" min="0" required>
                           <small class="form-text text-muted">Premio al raggiungimento dell'obiettivo</small>
                       </div>
                       <div class="col-md-6">
                           <label for="premio_extra" class="form-label">Premio Extra (€)</label>
                           <input type="number" class="form-control" id="premio_extra" name="premio_extra" step="0.01" min="0" value="0">
                           <small class="form-text text-muted">Premio aggiuntivo al superamento dell'obiettivo</small>
                       </div>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="negozio_id" class="form-label">Negozio</label>
                           <select class="form-select" id="negozio_id" name="negozio_id">
                               <option value="">Tutti i negozi</option>
                               <?php foreach ($negozi as $negozio): ?>
                               <option value="<?php echo $negozio['id']; ?>">
                                   <?php echo htmlspecialchars($negozio['nome'] . ' (' . $negozio['citta'] . ')'); ?>
                               </option>
                               <?php endforeach; ?>
                           </select>
                           <small class="form-text text-muted">Lasciare vuoto per target globale</small>
                       </div>
                       <div class="col-md-6">
                           <label for="operatore_id" class="form-label">Operatore</label>
                           <select class="form-select" id="operatore_id" name="operatore_id">
                               <option value="">Tutti gli operatori</option>
                               <?php foreach ($operatori as $operatore): ?>
                               <option value="<?php echo $operatore['id']; ?>">
                                   <?php echo htmlspecialchars($operatore['cognome'] . ' ' . $operatore['nome']); ?>
                               </option>
                               <?php endforeach; ?>
                           </select>
                           <small class="form-text text-muted">Lasciare vuoto per target di negozio o globale</small>
                       </div>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="raggiunto" class="form-label">Progresso Attuale</label>
                           <input type="number" class="form-control" id="raggiunto" name="raggiunto" min="0" value="0">
                           <small class="form-text text-muted">Lasciare 0 per nuovo target</small>
                       </div>
                       <div class="col-md-6">
                           <label for="stato_modal" class="form-label">Stato *</label>
                           <select class="form-select" id="stato_modal" name="stato" required>
                               <?php foreach ($stati_target as $key => $value): ?>
                               <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                   </div>
                   
                   <div class="alert alert-info">
                       <i class="bi bi-info-circle"></i> I campi contrassegnati con * sono obbligatori.
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
               <button type="button" class="btn btn-primary" id="saveTargetBtn">Salva</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal per visualizzare i dettagli del target -->
<div class="modal fade" id="viewTargetModal" tabindex="-1" aria-labelledby="viewTargetModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header bg-info text-white">
               <h5 class="modal-title" id="viewTargetModalLabel"><i class="bi bi-info-circle"></i> Dettagli Target</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Informazioni Generali</h5>
                           <p><strong>Nome:</strong> <span id="view_nome"></span></p>
                           <p><strong>Gestore:</strong> <span id="view_gestore"></span></p>
                           <p><strong>Tipo:</strong> <span id="view_tipo"></span></p>
                           <p><strong>Stato:</strong> <span id="view_stato"></span></p>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Periodo e Obiettivo</h5>
                           <p><strong>Data Inizio:</strong> <span id="view_data_inizio"></span></p>
                           <p><strong>Data Fine:</strong> <span id="view_data_fine"></span></p>
                           <p><strong>Obiettivo:</strong> <span id="view_obiettivo"></span></p>
                           <p><strong>Progresso:</strong> <span id="view_progresso"></span></p>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Premi</h5>
                           <p><strong>Premio Base:</strong> <span id="view_premio_base"></span></p>
                           <p><strong>Premio Extra:</strong> <span id="view_premio_extra"></span></p>
                           <p><strong>Premio Totale:</strong> <span id="view_premio_totale"></span></p>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Assegnazione</h5>
                           <p><strong>Negozio:</strong> <span id="view_negozio"></span></p>
                           <p><strong>Operatore:</strong> <span id="view_operatore"></span></p>
                           <p><strong>ID:</strong> <span id="view_id"></span></p>
                           <p><strong>Ultima Modifica:</strong> <span id="view_data_modifica"></span></p>
                       </div>
                   </div>
               </div>
               <div class="progress mt-3" style="height: 20px;">
                   <div class="progress-bar progress-bar-striped" id="view_progress_bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
               </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
               <?php if ($can_edit): ?>
               <button type="button" class="btn btn-success" id="updateProgressFromViewBtn">Aggiorna Progresso</button>
               <button type="button" class="btn btn-primary" id="editFromViewBtn">Modifica</button>
               <?php endif; ?>
           </div>
       </div>
   </div>
</div>

<!-- Modal per aggiornare il progresso -->
<div class="modal fade" id="updateProgressModal" tabindex="-1" aria-labelledby="updateProgressModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header bg-success text-white">
               <h5 class="modal-title" id="updateProgressModalLabel"><i class="bi bi-arrow-up-circle"></i> Aggiorna Progresso</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="progressForm">
                   <input type="hidden" id="progress_target_id" name="id" value="">
                   
                   <div class="mb-3">
                       <label for="target_name" class="form-label">Target</label>
                       <input type="text" class="form-control" id="target_name" readonly>
                   </div>
                   
                   <div class="mb-3">
                       <label for="current_progress" class="form-label">Progresso Attuale</label>
                       <input type="number" class="form-control" id="current_progress" readonly>
                   </div>
                   
                   <div class="mb-3">
                       <label for="new_progress" class="form-label">Nuovo Progresso *</label>
                       <input type="number" class="form-control" id="new_progress" name="raggiunto" min="0" required>
                       <small class="form-text text-muted">Inserire il valore totale raggiunto, non l'incremento</small>
                   </div>
                   
                   <div class="progress mt-3" style="height: 20px;">
                       <div class="progress-bar progress-bar-striped" id="progress_bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
               <button type="button" class="btn btn-success" id="saveProgressBtn">Salva Progresso</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal di conferma eliminazione -->
<div class="modal fade" id="deleteTargetModal" tabindex="-1" aria-labelledby="deleteTargetModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header bg-danger text-white">
               <h5 class="modal-title" id="deleteTargetModalLabel"><i class="bi bi-exclamation-triangle"></i> Conferma Eliminazione</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <p>Sei sicuro di voler eliminare questo target?</p>
               <p>Questa azione non può essere annullata.</p>
               <div class="alert alert-warning">
                   <i class="bi bi-exclamation-triangle"></i> Attenzione: l'eliminazione di un target potrebbe influire sui compensi degli operatori e sulle statistiche.
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
/* Stili personalizzati per i target */
.target-container {
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

/* Stili per la progress bar */
.progress {
   background-color: #f5f5f5;
   border-radius: 0.25rem;
   overflow: hidden;
}

.progress-bar-striped {
   background-image: linear-gradient(45deg, rgba(255, 255, 255, 0.15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, 0.15) 50%, rgba(255, 255, 255, 0.15) 75%, transparent 75%, transparent);
   background-size: 1rem 1rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
   // Inizializzazione date nel form
   const oggi = new Date();
   const primoDelMese = new Date(oggi.getFullYear(), oggi.getMonth(), 1);
   const ultimoDelMese = new Date(oggi.getFullYear(), oggi.getMonth() + 1, 0);
   
   document.getElementById('data_inizio').valueAsDate = primoDelMese;
   document.getElementById('data_fine').valueAsDate = ultimoDelMese;
   
   // Gestione del modal per aggiungere/modificare target
   const targetModal = new bootstrap.Modal(document.getElementById('targetModal'));
   const viewTargetModal = new bootstrap.Modal(document.getElementById('viewTargetModal'));
   const updateProgressModal = new bootstrap.Modal(document.getElementById('updateProgressModal'));
   const deleteTargetModal = new bootstrap.Modal(document.getElementById('deleteTargetModal'));
   
   // Gestione del campo periodo personalizzato
   document.getElementById('periodo').addEventListener('change', function() {
       const dateRangeFields = document.querySelectorAll('.date-range');
       if (this.value === 'custom') {
           dateRangeFields.forEach(field => field.classList.remove('d-none'));
       } else {
           dateRangeFields.forEach(field => field.classList.add('d-none'));
       }
   });
   
   // Gestione del campo tipo target per aggiornare il suggerimento dell'obiettivo
   document.getElementById('tipo_modal').addEventListener('change', function() {
       const obiettivo_hint = document.getElementById('obiettivo_hint');
       if (this.value === 'fatturato') {
           obiettivo_hint.textContent = 'Importo in euro da raggiungere';
       } else {
           obiettivo_hint.textContent = 'Numero di ' + (this.value === 'vendite' ? 'unità' : 'attivazioni') + ' da raggiungere';
       }
   });
   
   // Apertura modal per nuovo target
   document.getElementById('addTargetBtn').addEventListener('click', function() {
       // Reset form
       document.getElementById('targetForm').reset();
       document.getElementById('target_id').value = '';
       document.getElementById('targetModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Nuovo Target';
       document.getElementById('data_inizio').valueAsDate = primoDelMese;
       document.getElementById('data_fine').valueAsDate = ultimoDelMese;
       document.getElementById('stato_modal').value = 'attivo';
       document.getElementById('raggiunto').value = '0';
   });
   
   // Salvataggio target
   document.getElementById('saveTargetBtn').addEventListener('click', function() {
       const form = document.getElementById('targetForm');
       
       // Validazione base
       if (!form.checkValidity()) {
           form.reportValidity();
           return;
       }
       
       // Raccolta dati dal form
       const formData = new FormData(form);
       const targetData = Object.fromEntries(formData.entries());
       
       // Validazione aggiuntiva
       const dataInizio = new Date(targetData.data_inizio);
       const dataFine = new Date(targetData.data_fine);
       
       if (dataFine < dataInizio) {
           alert('La data di fine deve essere successiva alla data di inizio.');
           return;
       }
       
       // Qui andrebbe implementata la chiamata AJAX per salvare il target
       // Per ora simuliamo un salvataggio con successo
       
       alert('Target salvato con successo!');
       targetModal.hide();
       
       // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
       // location.reload();
   });
   
   // Gestione pulsanti di modifica
   document.querySelectorAll('.edit-target').forEach(button => {
       button.addEventListener('click', function() {
           const targetId = this.dataset.id;
           
           // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del target
           // Per ora simuliamo con dati di esempio
           const dummyData = {
               id: targetId,
               nome: 'Target Fastweb Mobile Q1',
               gestore: 'fastweb',
               tipo: 'mobile',
               obiettivo: 50,
               raggiunto: 32,
               premio_base: 500.00,
               premio_extra: 200.00,
               data_inizio: '2023-01-01',
               data_fine: '2023-03-31',
               negozio_id: 1,
               operatore_id: '',
               stato: 'attivo'
           };
           
           // Popola il form con i dati
           document.getElementById('target_id').value = dummyData.id;
           document.getElementById('nome').value = dummyData.nome;
           document.getElementById('gestore_modal').value = dummyData.gestore;
           document.getElementById('tipo_modal').value = dummyData.tipo;
           document.getElementById('obiettivo').value = dummyData.obiettivo;
           document.getElementById('raggiunto').value = dummyData.raggiunto;
           document.getElementById('premio_base').value = dummyData.premio_base;
           document.getElementById('premio_extra').value = dummyData.premio_extra;
           document.getElementById('data_inizio').value = dummyData.data_inizio;
           document.getElementById('data_fine').value = dummyData.data_fine;
           document.getElementById('negozio_id').value = dummyData.negozio_id;
           document.getElementById('operatore_id').value = dummyData.operatore_id;
           document.getElementById('stato_modal').value = dummyData.stato;
           
           // Aggiorna il suggerimento dell'obiettivo
           const obiettivo_hint = document.getElementById('obiettivo_hint');
           if (dummyData.tipo === 'fatturato') {
               obiettivo_hint.textContent = 'Importo in euro da raggiungere';
           } else {
               obiettivo_hint.textContent = 'Numero di ' + (dummyData.tipo === 'vendite' ? 'unità' : 'attivazioni') + ' da raggiungere';
           }
           
           // Aggiorna il titolo del modal
           document.getElementById('targetModalLabel').innerHTML = '<i class="bi bi-pencil"></i> Modifica Target';
           
           // Mostra il modal
           targetModal.show();
       });
   });
   
   // Gestione pulsanti di visualizzazione
   document.querySelectorAll('.view-target').forEach(button => {
       button.addEventListener('click', function() {
           const targetId = this.dataset.id;
           
           // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del target
           // Per ora simuliamo con dati di esempio
           const dummyData = {
               id: targetId,
               nome: 'Target Fastweb Mobile Q1',
               gestore: 'Fastweb',
               tipo: 'Mobile',
               obiettivo: '50 attivazioni',
               raggiunto: '32 attivazioni (64%)',
               premio_base: '500,00 €',
               premio_extra: '200,00 €',
               premio_totale: '700,00 €',
               data_inizio: '01/01/2023',
               data_fine: '31/03/2023',
               negozio: 'Negozio Milano Centro',
               operatore: '-',
               stato: '<span class="badge bg-success">Attivo</span>',
               data_modifica: '15/01/2023 14:30',
               percentuale: 64
           };
           
           // Popola il modal con i dati
           document.getElementById('view_id').textContent = dummyData.id;
           document.getElementById('view_nome').textContent = dummyData.nome;
           document.getElementById('view_gestore').textContent = dummyData.gestore;
           document.getElementById('view_tipo').textContent = dummyData.tipo;
           document.getElementById('view_obiettivo').textContent = dummyData.obiettivo;
           document.getElementById('view_progresso').textContent = dummyData.raggiunto;
           document.getElementById('view_premio_base').textContent = dummyData.premio_base;
           document.getElementById('view_premio_extra').textContent = dummyData.premio_extra;
           document.getElementById('view_premio_totale').textContent = dummyData.premio_totale;
           document.getElementById('view_data_inizio').textContent = dummyData.data_inizio;
           document.getElementById('view_data_fine').textContent = dummyData.data_fine;
           document.getElementById('view_negozio').textContent = dummyData.negozio;
           document.getElementById('view_operatore').textContent = dummyData.operatore;
           document.getElementById('view_stato').innerHTML = dummyData.stato;
           document.getElementById('view_data_modifica').textContent = dummyData.data_modifica;
           
           // Aggiorna la progress bar
           const progressBar = document.getElementById('view_progress_bar');
           progressBar.style.width = dummyData.percentuale + '%';
           progressBar.textContent = dummyData.percentuale + '%';
           progressBar.setAttribute('aria-valuenow', dummyData.percentuale);
           
           // Imposta la classe della progress bar in base alla percentuale
           if (dummyData.percentuale >= 100) {
               progressBar.className = 'progress-bar progress-bar-striped bg-success';
           } else if (dummyData.percentuale >= 75) {
               progressBar.className = 'progress-bar progress-bar-striped bg-info';
           } else if (dummyData.percentuale >= 50) {
               progressBar.className = 'progress-bar progress-bar-striped bg-warning';
           } else {
               progressBar.className = 'progress-bar progress-bar-striped bg-danger';
           }
           
           // Salva l'ID per i pulsanti
           document.getElementById('editFromViewBtn').dataset.id = targetId;
           document.getElementById('updateProgressFromViewBtn').dataset.id = targetId;
           
           // Mostra il modal
           viewTargetModal.show();
       });
   });
   
   // Gestione pulsante modifica dal modal di visualizzazione
   document.getElementById('editFromViewBtn').addEventListener('click', function() {
       const targetId = this.dataset.id;
       
       // Chiudi il modal di visualizzazione
       viewTargetModal.hide();
       
       // Simula il click sul pulsante di modifica corrispondente
       document.querySelector(`.edit-target[data-id="${targetId}"]`).click();
   });
   
   // Gestione pulsante aggiorna progresso dal modal di visualizzazione
   document.getElementById('updateProgressFromViewBtn').addEventListener('click', function() {
       const targetId = this.dataset.id;
       
       // Chiudi il modal di visualizzazione
       viewTargetModal.hide();
       
       // Simula il click sul pulsante di aggiornamento progresso corrispondente
       document.querySelector(`.update-progress[data-id="${targetId}"]`).click();
   });
   
   // Gestione pulsanti di aggiornamento progresso
   document.querySelectorAll('.update-progress').forEach(button => {
       button.addEventListener('click', function() {
           const targetId = this.dataset.id;
           
           // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del target
           // Per ora simuliamo con dati di esempio
           const dummyData = {
               id: targetId,
               nome: 'Target Fastweb Mobile Q1',
               obiettivo: 50,
               raggiunto: 32,
               percentuale: 64
           };
           
           // Popola il form con i dati
           document.getElementById('progress_target_id').value = dummyData.id;
           document.getElementById('target_name').value = dummyData.nome;
           document.getElementById('current_progress').value = dummyData.raggiunto;
           document.getElementById('new_progress').value = dummyData.raggiunto;
           
           // Aggiorna la progress bar
           const progressBar = document.getElementById('progress_bar');
           progressBar.style.width = dummyData.percentuale + '%';
           progressBar.textContent = dummyData.percentuale + '%';
           progressBar.setAttribute('aria-valuenow', dummyData.percentuale);
           
           // Imposta la classe della progress bar in base alla percentuale
           if (dummyData.percentuale >= 100) {
               progressBar.className = 'progress-bar progress-bar-striped bg-success';
           } else if (dummyData.percentuale >= 75) {
               progressBar.className = 'progress-bar progress-bar-striped bg-info';
           } else if (dummyData.percentuale >= 50) {
               progressBar.className = 'progress-bar progress-bar-striped bg-warning';
           } else {
               progressBar.className = 'progress-bar progress-bar-striped bg-danger';
           }
           
           // Mostra il modal
           updateProgressModal.show();
       });
   });
   
   // Aggiornamento della progress bar in tempo reale
   document.getElementById('new_progress').addEventListener('input', function() {
       const obiettivo = 50; // In un'implementazione reale, questo valore andrebbe recuperato dal target
       const nuovoValore = parseInt(this.value) || 0;
       const percentuale = Math.min(100, Math.round((nuovoValore / obiettivo) * 100));
       
       // Aggiorna la progress bar
       const progressBar = document.getElementById('progress_bar');
       progressBar.style.width = percentuale + '%';
       progressBar.textContent = percentuale + '%';
       progressBar.setAttribute('aria-valuenow', percentuale);
       
       // Imposta la classe della progress bar in base alla percentuale
       if (percentuale >= 100) {
           progressBar.className = 'progress-bar progress-bar-striped bg-success';
       } else if (percentuale >= 75) {
           progressBar.className = 'progress-bar progress-bar-striped bg-info';
       } else if (percentuale >= 50) {
           progressBar.className = 'progress-bar progress-bar-striped bg-warning';
       } else {
           progressBar.className = 'progress-bar progress-bar-striped bg-danger';
       }
   });
   
   // Salvataggio progresso
   document.getElementById('saveProgressBtn').addEventListener('click', function() {
       const form = document.getElementById('progressForm');
       
       // Validazione base
       if (!form.checkValidity()) {
           form.reportValidity();
           return;
       }
       
       // Raccolta dati dal form
       const formData = new FormData(form);
       const progressData = Object.fromEntries(formData.entries());
       
       // Qui andrebbe implementata la chiamata AJAX per salvare il progresso
       // Per ora simuliamo un salvataggio con successo
       
       alert('Progresso aggiornato con successo!');
       updateProgressModal.hide();
       
       // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
       // location.reload();
   });
   
   // Gestione pulsanti di eliminazione
   document.querySelectorAll('.delete-target').forEach(button => {
       button.addEventListener('click', function() {
           const targetId = this.dataset.id;
           
           // Salva l'ID per il pulsante di conferma
           document.getElementById('confirmDeleteBtn').dataset.id = targetId;
           
           // Mostra il modal
           deleteTargetModal.show();
       });
   });
   
   // Conferma eliminazione
   document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
       const targetId = this.dataset.id;
       
       // Qui andrebbe implementata la chiamata AJAX per eliminare il target
       // Per ora simuliamo un'eliminazione con successo
       
       alert('Target eliminato con successo!');
       deleteTargetModal.hide();
       
       // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
       // location.reload();
   });
   
   // Esportazione target
   document.getElementById('exportTargetBtn').addEventListener('click', function() {
       // Qui andrebbe implementata la logica per esportare i target
       alert('Funzionalità di esportazione in fase di implementazione');
   });
   
   // Duplicazione target selezionati
   document.getElementById('duplicateTargetBtn').addEventListener('click', function() {
       // Qui andrebbe implementata la logica per duplicare i target selezionati
       alert('Funzionalità di duplicazione in fase di implementazione');
   });
   
   // Inizializzazione grafici se ci sono dati
   <?php if (!empty($targets)): ?>
   // Calcola i dati per i grafici
   const statiData = {
       labels: ['Attivi', 'Completati', 'Scaduti', 'Annullati'],
       datasets: [{
           data: [
               <?php echo $stats['attivi']; ?>,
               <?php echo $stats['completati']; ?>,
               <?php echo $stats['scaduti']; ?>,
               <?php echo $stats['annullati']; ?>
           ],
           backgroundColor: [
               'rgba(25, 135, 84, 0.7)',
               'rgba(13, 110, 253, 0.7)',
               'rgba(255, 193, 7, 0.7)',
               'rgba(220, 53, 69, 0.7)'
           ],
           borderColor: [
               'rgba(25, 135, 84, 1)',
               'rgba(13, 110, 253, 1)',
               'rgba(255, 193, 7, 1)',
               'rgba(220, 53, 69, 1)'
           ],
           borderWidth: 1
       }]
   };
   
   // Dati per il grafico dei gestori
   const gestoriData = {
       labels: [
           <?php 
           $gestori_count = [];
           foreach ($targets as $target) {
               $gestore = $target['gestore'];
               if (!isset($gestori_count[$gestore])) {
                   $gestori_count[$gestore] = 0;
               }
               $gestori_count[$gestore]++;
           }
           
           foreach ($gestori_count as $gestore => $count) {
               echo "'" . ($gestori[$gestore] ?? $gestore) . "', ";
           }
           ?>
       ],
       datasets: [{
           label: 'Numero di target',
           data: [
               <?php 
               foreach ($gestori_count as $count) {
                   echo $count . ", ";
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
   
   // Configurazione comune per i grafici
   const pieChartOptions = {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
           legend: {
               position: 'right',
           }
       }
   };
   
   const barChartOptions = {
       responsive: true,
       maintainAspectRatio: false,
       plugins: {
           legend: {
               display: false
           }
       },
       scales: {
           y: {
               beginAtZero: true
           }
       }
   };
   
   // Creazione grafico stati
   const statiChart = new Chart(
       document.getElementById('statiChart').getContext('2d'),
       {
           type: 'pie',
           data: statiData,
           options: pieChartOptions
       }
   );
   
   // Creazione grafico gestori
   const gestoriChart = new Chart(
       document.getElementById('gestoriChart').getContext('2d'),
       {
           type: 'bar',
           data: gestoriData,
           options: barChartOptions
       }
   );
   <?php endif; ?>
});
</script>

