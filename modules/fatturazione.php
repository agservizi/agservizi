<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Ottieni i dati dell'utente corrente
$user_id = $_SESSION['user_id'] ?? 0;
$user_nome = $_SESSION['nome'] ?? '';
$user_cognome = $_SESSION['cognome'] ?? '';
$user_ruolo = $_SESSION['ruolo'] ?? '';
$negozio_id = $_SESSION['negozio_id'] ?? null;

// Verifica i permessi (solo amministratori e responsabili possono creare/modificare fatture)
$can_edit = in_array($user_ruolo, ['amministratore', 'responsabile']);

// Ottieni i filtri dalla query string
$periodo_filter = isset($_GET['periodo']) ? $_GET['periodo'] : 'current_month';
$stato_filter = isset($_GET['stato']) ? $_GET['stato'] : 'all';
$cliente_filter = isset($_GET['cliente']) ? $_GET['cliente'] : '';
$numero_filter = isset($_GET['numero']) ? $_GET['numero'] : '';

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

// Stati delle fatture
$stati_fattura = [
   'bozza' => 'Bozza',
   'emessa' => 'Emessa',
   'pagata' => 'Pagata',
   'annullata' => 'Annullata',
   'scaduta' => 'Scaduta'
];

// Tipi di fattura
$tipi_fattura = [
   'fattura' => 'Fattura',
   'nota_credito' => 'Nota di Credito',
   'preventivo' => 'Preventivo',
   'proforma' => 'Fattura Proforma',
   'ricevuta' => 'Ricevuta Fiscale'
];

// Metodi di pagamento
$metodi_pagamento = [
   'bonifico' => 'Bonifico Bancario',
   'contanti' => 'Contanti',
   'carta' => 'Carta di Credito/Debito',
   'paypal' => 'PayPal',
   'assegno' => 'Assegno',
   'rid' => 'RID/SDD',
   'altro' => 'Altro'
];

// Funzione per ottenere i clienti
function getClienti($pdo, $search = null) {
   try {
       $sql = "SELECT id, codice_fiscale, nome, cognome, email, telefono FROM clienti WHERE 1=1";
       $params = [];
       
       if ($search) {
           $sql .= " AND (nome LIKE ? OR cognome LIKE ? OR codice_fiscale LIKE ? OR email LIKE ? OR telefono LIKE ?)";
           $search_term = '%' . $search . '%';
           $params = array_fill(0, 5, $search_term);
       }
       
       $sql .= " ORDER BY cognome, nome LIMIT 100";
       
       $stmt = $pdo->prepare($sql);
       $stmt->execute($params);
       
       return $stmt->fetchAll();
   } catch (PDOException $e) {
       error_log("Errore nel recupero dei clienti: " . $e->getMessage());
       return [];
   }
}

// Funzione per ottenere le fatture
function getFatture($pdo, $filters = []) {
   try {
       $sql = "
           SELECT f.*, 
                  c.nome as cliente_nome, 
                  c.cognome as cliente_cognome,
                  c.codice_fiscale as cliente_cf,
                  c.partita_iva as cliente_piva,
                  o.nome as operatore_nome, 
                  o.cognome as operatore_cognome
           FROM fatture f
           LEFT JOIN clienti c ON f.cliente_id = c.id
           LEFT JOIN operatori o ON f.operatore_id = o.id
           WHERE 1=1
       ";
       $params = [];
       
       // Filtra per periodo
       if (!empty($filters['data_da'])) {
           $sql .= " AND f.data >= ?";
           $params[] = $filters['data_da'];
       }
       
       if (!empty($filters['data_a'])) {
           $sql .= " AND f.data <= ?";
           $params[] = $filters['data_a'] . ' 23:59:59';
       }
       
       // Filtra per stato
       if (!empty($filters['stato']) && $filters['stato'] !== 'all') {
           $sql .= " AND f.stato = ?";
           $params[] = $filters['stato'];
       }
       
       // Filtra per cliente
       if (!empty($filters['cliente'])) {
           $sql .= " AND (c.nome LIKE ? OR c.cognome LIKE ? OR c.codice_fiscale LIKE ? OR c.partita_iva LIKE ?)";
           $search_term = '%' . $filters['cliente'] . '%';
           $params = array_merge($params, array_fill(0, 4, $search_term));
       }
       
       // Filtra per numero fattura
       if (!empty($filters['numero'])) {
           $sql .= " AND f.numero LIKE ?";
           $params[] = '%' . $filters['numero'] . '%';
       }
       
       // Filtra per negozio (se l'utente è un responsabile)
       if (!empty($filters['negozio_id'])) {
           $sql .= " AND f.negozio_id = ?";
           $params[] = $filters['negozio_id'];
       }
       
       // Ordina per data decrescente
       $sql .= " ORDER BY f.data DESC, f.id DESC";
       
       $stmt = $pdo->prepare($sql);
       $stmt->execute($params);
       
       return $stmt->fetchAll();
   } catch (PDOException $e) {
       error_log("Errore nel recupero delle fatture: " . $e->getMessage());
       return [];
   }
}

// Funzione per calcolare i totali delle fatture
function calcolaTotaliFatture($fatture) {
   $totali = [
       'totale' => 0,
       'imponibile' => 0,
       'iva' => 0,
       'per_stato' => [
           'bozza' => 0,
           'emessa' => 0,
           'pagata' => 0,
           'annullata' => 0,
           'scaduta' => 0
       ]
   ];
   
   foreach ($fatture as $fattura) {
       // Ignora le fatture annullate nei totali
       if ($fattura['stato'] === 'annullata') {
           $totali['per_stato']['annullata'] += $fattura['totale'];
           continue;
       }
       
       // Aggiungi ai totali generali
       $totali['totale'] += $fattura['totale'];
       $totali['imponibile'] += $fattura['imponibile'];
       $totali['iva'] += $fattura['iva'];
       
       // Aggiungi ai totali per stato
       if (isset($totali['per_stato'][$fattura['stato']])) {
           $totali['per_stato'][$fattura['stato']] += $fattura['totale'];
       }
   }
   
   return $totali;
}

// Funzione per formattare l'importo
function formatImporto($importo) {
   return number_format($importo, 2, ',', '.') . ' €';
}

// Funzione per ottenere la classe del badge in base allo stato
function getStatoBadgeClass($stato) {
   switch ($stato) {
       case 'bozza': return 'secondary';
       case 'emessa': return 'primary';
       case 'pagata': return 'success';
       case 'annullata': return 'danger';
       case 'scaduta': return 'warning';
       default: return 'info';
   }
}

// Funzione per generare un nuovo numero di fattura
function generaNuovoNumeroFattura($pdo, $anno = null) {
   if ($anno === null) {
       $anno = date('Y');
   }
   
   try {
       // Trova l'ultimo numero di fattura per l'anno specificato
       $stmt = $pdo->prepare("
           SELECT MAX(CAST(SUBSTRING_INDEX(numero, '/', 1) AS UNSIGNED)) as ultimo_numero
           FROM fatture
           WHERE numero LIKE ?
       ");
       $stmt->execute([$anno . '/%']);
       $result = $stmt->fetch();
       
       $ultimo_numero = $result['ultimo_numero'] ?? 0;
       $nuovo_numero = $ultimo_numero + 1;
       
       return $nuovo_numero . '/' . $anno;
   } catch (PDOException $e) {
       error_log("Errore nella generazione del numero fattura: " . $e->getMessage());
       return '1/' . $anno; // Fallback
   }
}

// Ottieni i clienti per l'autocompletamento
$clienti = getClienti($pdo, $cliente_filter);

// Ottieni le fatture in base ai filtri
$filters = [
   'data_da' => $date_range['start'],
   'data_a' => $date_range['end'],
   'stato' => $stato_filter,
   'cliente' => $cliente_filter,
   'numero' => $numero_filter
];

// Se l'utente è un responsabile, mostra solo le fatture del suo negozio
if ($user_ruolo === 'responsabile') {
   $filters['negozio_id'] = $negozio_id;
}

$fatture = getFatture($pdo, $filters);
$totali_fatture = calcolaTotaliFatture($fatture);

// Genera un nuovo numero di fattura per il form di creazione
$nuovo_numero_fattura = generaNuovoNumeroFattura($pdo);
?>

<div class="fatturazione-container">
   <!-- Header della pagina -->
   <div class="row mb-3">
       <div class="col-12 d-flex justify-content-between align-items-center">
           <div>
               <h2><i class="bi bi-receipt"></i> Fatturazione</h2>
               <p class="text-muted mb-0">Gestisci fatture, preventivi e ricevute fiscali</p>
           </div>
           <?php if ($can_edit): ?>
           <div>
               <button class="btn btn-primary" id="addFatturaBtn" data-bs-toggle="modal" data-bs-target="#fatturaModal">
                   <i class="bi bi-plus-circle"></i> Nuova Fattura
               </button>
           </div>
           <?php endif; ?>
       </div>
   </div>

   <!-- Riepilogo fatture -->
   <div class="row mb-4">
       <div class="col-md-3 mb-3">
           <div class="card shadow-sm h-100">
               <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="card-title mb-0">Totale Fatture</h5>
                       <div class="stats-icon bg-primary-light">
                           <i class="bi bi-receipt text-primary"></i>
                       </div>
                   </div>
                   <h3 class="mb-1"><?php echo formatImporto($totali_fatture['totale']); ?></h3>
                   <p class="text-muted mb-0"><?php echo count($fatture); ?> fatture nel periodo</p>
               </div>
           </div>
       </div>
       <div class="col-md-3 mb-3">
           <div class="card shadow-sm h-100">
               <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="card-title mb-0">Fatture Pagate</h5>
                       <div class="stats-icon bg-success-light">
                           <i class="bi bi-check-circle text-success"></i>
                       </div>
                   </div>
                   <h3 class="mb-1"><?php echo formatImporto($totali_fatture['per_stato']['pagata']); ?></h3>
                   <p class="text-muted mb-0">
                       <?php 
                       $percentuale_pagate = $totali_fatture['totale'] > 0 ? 
                           round(($totali_fatture['per_stato']['pagata'] / $totali_fatture['totale']) * 100) : 0;
                       echo $percentuale_pagate . '% del totale';
                       ?>
                   </p>
               </div>
           </div>
       </div>
       <div class="col-md-3 mb-3">
           <div class="card shadow-sm h-100">
               <div class="card-body">
                   <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="card-title mb-0">Da Incassare</h5>
                       <div class="stats-icon bg-warning-light">
                           <i class="bi bi-hourglass-split text-warning"></i>
                       </div>
                   </div>
                   <h3 class="mb-1"><?php echo formatImporto($totali_fatture['per_stato']['emessa'] + $totali_fatture['per_stato']['scaduta']); ?></h3>
                   <p class="text-muted mb-0">
                       <?php 
                       $percentuale_da_incassare = $totali_fatture['totale'] > 0 ? 
                           round((($totali_fatture['per_stato']['emessa'] + $totali_fatture['per_stato']['scaduta']) / $totali_fatture['totale']) * 100) : 0;
                       echo $percentuale_da_incassare . '% del totale';
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
               <input type="hidden" name="module" value="fatturazione">
               <div class="row g-3">
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
                           <?php foreach ($stati_fattura as $key => $value): ?>
                           <option value="<?php echo $key; ?>" <?php echo $stato_filter === $key ? 'selected' : ''; ?>>
                               <?php echo $value; ?>
                           </option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   <div class="col-md-3">
                       <label for="cliente" class="form-label">Cliente</label>
                       <input type="text" class="form-control" id="cliente" name="cliente" placeholder="Nome, cognome o CF" value="<?php echo htmlspecialchars($cliente_filter); ?>">
                   </div>
                   <div class="col-md-3">
                       <label for="numero" class="form-label">Numero Fattura</label>
                       <input type="text" class="form-control" id="numero" name="numero" placeholder="Es. 123/2023" value="<?php echo htmlspecialchars($numero_filter); ?>">
                   </div>
               </div>
               <div class="row mt-3">
                   <div class="col-12 d-flex justify-content-end">
                       <button type="submit" class="btn btn-primary me-2">
                           <i class="bi bi-funnel"></i> Filtra
                       </button>
                       <a href="index.php?module=fatturazione" class="btn btn-outline-secondary">
                           <i class="bi bi-x-circle"></i> Reset
                       </a>
                   </div>
               </div>
           </form>
       </div>
   </div>

   <!-- Tabella Fatture -->
   <div class="card shadow-sm">
       <div class="card-header bg-light d-flex justify-content-between align-items-center">
           <h5 class="mb-0"><i class="bi bi-table"></i> Elenco Fatture</h5>
           <span class="badge bg-primary"><?php echo count($fatture); ?> fatture trovate</span>
       </div>
       <div class="card-body p-0">
           <?php if (empty($fatture)): ?>
           <div class="alert alert-info m-3">
               <i class="bi bi-info-circle"></i> Nessuna fattura trovata con i filtri selezionati.
           </div>
           <?php else: ?>
           <div class="table-responsive">
               <table class="table table-hover table-striped mb-0">
                   <thead class="table-light">
                       <tr>
                           <th>Numero</th>
                           <th>Data</th>
                           <th>Cliente</th>
                           <th>Tipo</th>
                           <th class="text-end">Imponibile</th>
                           <th class="text-end">IVA</th>
                           <th class="text-end">Totale</th>
                           <th>Scadenza</th>
                           <th>Stato</th>
                           <th class="text-end">Azioni</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($fatture as $fattura): ?>
                       <tr>
                           <td><?php echo htmlspecialchars($fattura['numero']); ?></td>
                           <td><?php echo date('d/m/Y', strtotime($fattura['data'])); ?></td>
                           <td>
                               <?php 
                               $cliente_nome = trim($fattura['cliente_nome'] . ' ' . $fattura['cliente_cognome']);
                               echo htmlspecialchars($cliente_nome);
                               
                               if (!empty($fattura['cliente_cf'])) {
                                   echo '<br><small class="text-muted">CF: ' . htmlspecialchars($fattura['cliente_cf']) . '</small>';
                               } elseif (!empty($fattura['cliente_piva'])) {
                                   echo '<br><small class="text-muted">P.IVA: ' . htmlspecialchars($fattura['cliente_piva']) . '</small>';
                               }
                               ?>
                           </td>
                           <td>
                               <?php 
                               $tipo_nome = $tipi_fattura[$fattura['tipo']] ?? ucfirst($fattura['tipo']);
                               $tipo_icon = 'receipt';
                               switch($fattura['tipo']) {
                                   case 'nota_credito': $tipo_icon = 'arrow-return-left'; break;
                                   case 'preventivo': $tipo_icon = 'file-earmark-text'; break;
                                   case 'proforma': $tipo_icon = 'file-earmark-ruled'; break;
                                   case 'ricevuta': $tipo_icon = 'card-checklist'; break;
                               }
                               ?>
                               <span><i class="bi bi-<?php echo $tipo_icon; ?>"></i> <?php echo htmlspecialchars($tipo_nome); ?></span>
                           </td>
                           <td class="text-end"><?php echo formatImporto($fattura['imponibile']); ?></td>
                           <td class="text-end"><?php echo formatImporto($fattura['iva']); ?></td>
                           <td class="text-end fw-bold"><?php echo formatImporto($fattura['totale']); ?></td>
                           <td>
                               <?php 
                               if (!empty($fattura['data_scadenza'])) {
                                   echo date('d/m/Y', strtotime($fattura['data_scadenza']));
                                   
                                   // Verifica se la fattura è scaduta
                                   $oggi = new DateTime();
                                   $scadenza = new DateTime($fattura['data_scadenza']);
                                   $giorni_ritardo = $oggi->diff($scadenza)->days;
                                   
                                   if ($fattura['stato'] !== 'pagata' && $fattura['stato'] !== 'annullata' && $scadenza < $oggi) {
                                       echo '<br><span class="badge bg-danger">Scaduta da ' . $giorni_ritardo . ' giorni</span>';
                                   } elseif ($fattura['stato'] !== 'pagata' && $fattura['stato'] !== 'annullata' && $giorni_ritardo <= 7) {
                                       echo '<br><span class="badge bg-warning">Scade tra ' . $giorni_ritardo . ' giorni</span>';
                                   }
                               } else {
                                   echo '-';
                               }
                               ?>
                           </td>
                           <td>
                               <span class="badge bg-<?php echo getStatoBadgeClass($fattura['stato']); ?>">
                                   <?php echo $stati_fattura[$fattura['stato']] ?? ucfirst($fattura['stato']); ?>
                               </span>
                               <?php if ($fattura['stato'] === 'pagata' && !empty($fattura['data_pagamento'])): ?>
                               <br><small class="text-muted">Pagata il: <?php echo date('d/m/Y', strtotime($fattura['data_pagamento'])); ?></small>
                               <?php endif; ?>
                           </td>
                           <td class="text-end">
                               <div class="btn-group">
                                   <button type="button" class="btn btn-sm btn-info view-fattura" data-id="<?php echo $fattura['id']; ?>" title="Visualizza dettagli">
                                       <i class="bi bi-eye"></i>
                                   </button>
                                   <button type="button" class="btn btn-sm btn-primary print-fattura" data-id="<?php echo $fattura['id']; ?>" title="Stampa">
                                       <i class="bi bi-printer"></i>
                                   </button>
                                   <?php if ($can_edit && $fattura['stato'] !== 'annullata'): ?>
                                   <button type="button" class="btn btn-sm btn-success update-stato" data-id="<?php echo $fattura['id']; ?>" title="Aggiorna stato">
                                       <i class="bi bi-arrow-up-circle"></i>
                                   </button>
                                   <?php if ($fattura['stato'] === 'bozza'): ?>
                                   <button type="button" class="btn btn-sm btn-warning edit-fattura" data-id="<?php echo $fattura['id']; ?>" title="Modifica">
                                       <i class="bi bi-pencil"></i>
                                   </button>
                                   <?php endif; ?>
                                   <button type="button" class="btn btn-sm btn-danger delete-fattura" data-id="<?php echo $fattura['id']; ?>" title="Annulla">
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
                   <button class="btn btn-outline-primary" id="exportFattureBtn">
                       <i class="bi bi-file-earmark-excel"></i> Esporta
                   </button>
                   <button class="btn btn-outline-info ms-2" id="emailFattureBtn">
                       <i class="bi bi-envelope"></i> Invia per Email
                   </button>
               </div>
               <div>
                   <small class="text-muted">Totale: <?php echo formatImporto($totali_fatture['totale']); ?></small>
               </div>
           </div>
       </div>
   </div>

   <!-- Grafici di riepilogo -->
   <?php if (!empty($fatture)): ?>
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
                   <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Fatturato Mensile</h5>
               </div>
               <div class="card-body">
                   <canvas id="fatturatoChart" height="250"></canvas>
               </div>
           </div>
       </div>
   </div>
   <?php endif; ?>
</div>

<!-- Modal per aggiungere/modificare fattura -->
<div class="modal fade" id="fatturaModal" tabindex="-1" aria-labelledby="fatturaModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-xl">
       <div class="modal-content">
           <div class="modal-header bg-primary text-white">
               <h5 class="modal-title" id="fatturaModalLabel"><i class="bi bi-plus-circle"></i> Nuova Fattura</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="fatturaForm">
                   <input type="hidden" id="fattura_id" name="id" value="">
                   
                   <div class="row mb-3">
                       <div class="col-md-4">
                           <label for="numero_fattura" class="form-label">Numero Fattura *</label>
                           <input type="text" class="form-control" id="numero_fattura" name="numero" value="<?php echo $nuovo_numero_fattura; ?>" required>
                       </div>
                       <div class="col-md-4">
                           <label for="data_fattura" class="form-label">Data Fattura *</label>
                           <input type="date" class="form-control" id="data_fattura" name="data" value="<?php echo date('Y-m-d'); ?>" required>
                       </div>
                       <div class="col-md-4">
                           <label for="tipo_fattura" class="form-label">Tipo Documento *</label>
                           <select class="form-select" id="tipo_fattura" name="tipo" required>
                               <?php foreach ($tipi_fattura as $key => $value): ?>
                               <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-6">
                           <label for="cliente_id" class="form-label">Cliente *</label>
                           <select class="form-select" id="cliente_id" name="cliente_id" required>
                               <option value="">Seleziona cliente</option>
                               <?php foreach ($clienti as $cliente): ?>
                               <option value="<?php echo $cliente['id']; ?>">
                                   <?php echo htmlspecialchars($cliente['cognome'] . ' ' . $cliente['nome'] . ' - ' . ($cliente['codice_fiscale'] ?: 'CF non disponibile')); ?>
                               </option>
                               <?php endforeach; ?>
                           </select>
                           <div class="mt-2">
                               <button type="button" class="btn btn-sm btn-outline-primary" id="newClienteBtn">
                                   <i class="bi bi-person-plus"></i> Nuovo Cliente
                               </button>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <label for="cliente_info" class="form-label">Informazioni Cliente</label>
                           <textarea class="form-control" id="cliente_info" name="cliente_info" rows="3" readonly></textarea>
                       </div>
                   </div>
                   
                   <h5 class="border-bottom pb-2 mt-4">Dettagli Fattura</h5>
                   
                   <div class="table-responsive mb-3">
                       <table class="table table-bordered" id="dettagliTable">
                           <thead class="table-light">
                               <tr>
                                   <th>Descrizione</th>
                                   <th class="text-center" style="width: 100px;">Quantità</th>
                                   <th class="text-end" style="width: 150px;">Prezzo Unitario</th>
                                   <th class="text-center" style="width: 100px;">IVA %</th>
                                   <th class="text-end" style="width: 150px;">Totale</th>
                                   <th style="width: 50px;"></th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr id="riga_template" class="d-none">
                                   <td>
                                       <input type="text" class="form-control descrizione-item" name="dettagli[0][descrizione]" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-center quantita-item" name="dettagli[0][quantita]" value="1" min="1" step="1" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-end prezzo-item" name="dettagli[0][prezzo]" value="0.00" min="0" step="0.01" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-center iva-item" name="dettagli[0][iva]" value="22" min="0" max="100" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-end totale-item" name="dettagli[0][totale]" value="0.00" readonly>
                                   </td>
                                   <td class="text-center">
                                       <button type="button" class="btn btn-sm btn-danger remove-item">
                                           <i class="bi bi-trash"></i>
                                       </button>
                                   </td>
                               </tr>
                               <tr id="riga_1">
                                   <td>
                                       <input type="text" class="form-control descrizione-item" name="dettagli[1][descrizione]" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-center quantita-item" name="dettagli[1][quantita]" value="1" min="1" step="1" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-end prezzo-item" name="dettagli[1][prezzo]" value="0.00" min="0" step="0.01" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-center iva-item" name="dettagli[1][iva]" value="22" min="0" max="100" required>
                                   </td>
                                   <td>
                                       <input type="number" class="form-control text-end totale-item" name="dettagli[1][totale]" value="0.00" readonly>
                                   </td>
                                   <td class="text-center">
                                       <button type="button" class="btn btn-sm btn-danger remove-item">
                                           <i class="bi bi-trash"></i>
                                       </button>
                                   </td>
                               </tr>
                           </tbody>
                           <tfoot>
                               <tr>
                                   <td colspan="6">
                                       <button type="button" class="btn btn-sm btn-success" id="addItemBtn">
                                           <i class="bi bi-plus-circle"></i> Aggiungi Riga
                                       </button>
                                   </td>
                               </tr>
                               <tr>
                                   <td colspan="3" rowspan="3" class="align-top">
                                       <label for="note" class="form-label">Note</label>
                                       <textarea class="form-control" id="note" name="note" rows="4"></textarea>
                                   </td>
                                   <td class="text-end fw-bold">Imponibile:</td>
                                   <td class="text-end">
                                       <input type="number" class="form-control text-end" id="imponibile" name="imponibile" value="0.00" readonly>
                                   </td>
                                   <td></td>
                               </tr>
                               <tr>
                                   <td class="text-end fw-bold">IVA:</td>
                                   <td class="text-end">
                                       <input type="number" class="form-control text-end" id="iva_totale" name="iva" value="0.00" readonly>
                                   </td>
                                   <td></td>
                               </tr>
                               <tr>
                                   <td class="text-end fw-bold">Totale:</td>
                                   <td class="text-end">
                                       <input type="number" class="form-control text-end" id="totale" name="totale" value="0.00" readonly>
                                   </td>
                                   <td></td>
                               </tr>
                           </tfoot>
                       </table>
                   </div>
                   
                   <div class="row mb-3">
                       <div class="col-md-4">
                           <label for="metodo_pagamento" class="form-label">Metodo di Pagamento *</label>
                           <select class="form-select" id="metodo_pagamento" name="metodo_pagamento" required>
                               <?php foreach ($metodi_pagamento as $key => $value): ?>
                               <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                               <?php endforeach; ?>
                           </select>
                       </div>
                       <div class="col-md-4">
                           <label for="data_scadenza" class="form-label">Data Scadenza</label>
                           <input type="date" class="form-control" id="data_scadenza" name="data_scadenza" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                       </div>
                       <div class="col-md-4">
                           <label for="stato_fattura" class="form-label">Stato *</label>
                           <select class="form-select" id="stato_fattura" name="stato" required>
                               <?php foreach ($stati_fattura as $key => $value): ?>
                               <option value="<?php echo $key; ?>" <?php echo $key === 'bozza' ? 'selected' : ''; ?>><?php echo $value; ?></option>
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
               <button type="button" class="btn btn-primary" id="saveFatturaBtn">Salva</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal per visualizzare i dettagli della fattura -->
<div class="modal fade" id="viewFatturaModal" tabindex="-1" aria-labelledby="viewFatturaModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg">
       <div class="modal-content">
           <div class="modal-header bg-info text-white">
               <h5 class="modal-title" id="viewFatturaModalLabel"><i class="bi bi-info-circle"></i> Dettagli Fattura</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <div class="row">
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Informazioni Fattura</h5>
                           <p><strong>Numero:</strong> <span id="view_numero"></span></p>
                           <p><strong>Data:</strong> <span id="view_data"></span></p>
                           <p><strong>Tipo:</strong> <span id="view_tipo"></span></p>
                           <p><strong>Stato:</strong> <span id="view_stato"></span></p>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Cliente</h5>
                           <p><strong>Nome:</strong> <span id="view_cliente_nome"></span></p>
                           <p><strong>Codice Fiscale:</strong> <span id="view_cliente_cf"></span></p>
                           <p><strong>Partita IVA:</strong> <span id="view_cliente_piva"></span></p>
                           <p><strong>Indirizzo:</strong> <span id="view_cliente_indirizzo"></span></p>
                       </div>
                   </div>
               </div>
               <div class="row">
                   <div class="col-md-12">
                       <h5 class="border-bottom pb-2">Dettagli</h5>
                       <div class="table-responsive">
                           <table class="table table-striped" id="view_dettagli_table">
                               <thead>
                                   <tr>
                                       <th>Descrizione</th>
                                       <th class="text-center">Quantità</th>
                                       <th class="text-end">Prezzo Unitario</th>
                                       <th class="text-center">IVA %</th>
                                       <th class="text-end">Totale</th>
                                   </tr>
                               </thead>
                               <tbody id="view_dettagli_body">
                                   <!-- I dettagli verranno inseriti qui dinamicamente -->
                               </tbody>
                               <tfoot>
                                   <tr>
                                       <td colspan="3" rowspan="3" class="align-top">
                                           <strong>Note:</strong><br>
                                           <span id="view_note"></span>
                                       </td>
                                       <td class="text-end fw-bold">Imponibile:</td>
                                       <td class="text-end" id="view_imponibile"></td>
                                   </tr>
                                   <tr>
                                       <td class="text-end fw-bold">IVA:</td>
                                       <td class="text-end" id="view_iva"></td>
                                   </tr>
                                   <tr>
                                       <td class="text-end fw-bold">Totale:</td>
                                       <td class="text-end fw-bold" id="view_totale"></td>
                                   </tr>
                               </tfoot>
                           </table>
                       </div>
                   </div>
               </div>
               <div class="row mt-3">
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Pagamento</h5>
                           <p><strong>Metodo:</strong> <span id="view_metodo_pagamento"></span></p>
                           <p><strong>Scadenza:</strong> <span id="view_data_scadenza"></span></p>
                           <p><strong>Data Pagamento:</strong> <span id="view_data_pagamento"></span></p>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="mb-3">
                           <h5 class="border-bottom pb-2">Informazioni di Sistema</h5>
                           <p><strong>ID:</strong> <span id="view_id"></span></p>
                           <p><strong>Operatore:</strong> <span id="view_operatore"></span></p>
                           <p><strong>Ultima Modifica:</strong> <span id="view_data_modifica"></span></p>
                       </div>
                   </div>
               </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
               <button type="button" class="btn btn-primary" id="printFromViewBtn">
                   <i class="bi bi-printer"></i> Stampa
               </button>
               <?php if ($can_edit): ?>
               <button type="button" class="btn btn-success" id="updateStatoFromViewBtn">
                   <i class="bi bi-arrow-up-circle"></i> Aggiorna Stato
               </button>
               <?php endif; ?>
           </div>
       </div>
   </div>
</div>

<!-- Modal per aggiornare lo stato della fattura -->
<div class="modal fade" id="updateStatoModal" tabindex="-1" aria-labelledby="updateStatoModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header bg-success text-white">
               <h5 class="modal-title" id="updateStatoModalLabel"><i class="bi bi-arrow-up-circle"></i> Aggiorna Stato Fattura</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="statoForm">
                   <input type="hidden" id="stato_fattura_id" name="id" value="">
                   
                   <div class="mb-3">
                       <label for="fattura_numero" class="form-label">Fattura</label>
                       <input type="text" class="form-control" id="fattura_numero" readonly>
                   </div>
                   
                   <div class="mb-3">
                       <label for="stato_attuale" class="form-label">Stato Attuale</label>
                       <input type="text" class="form-control" id="stato_attuale" readonly>
                   </div>
                   
                   <div class="mb-3">
                       <label for="nuovo_stato" class="form-label">Nuovo Stato *</label>
                       <select class="form-select" id="nuovo_stato" name="stato" required>
                           <?php foreach ($stati_fattura as $key => $value): ?>
                           <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   
                   <div class="mb-3" id="data_pagamento_container">
                       <label for="data_pagamento" class="form-label">Data Pagamento *</label>
                       <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" value="<?php echo date('Y-m-d'); ?>">
                       <small class="form-text text-muted">Richiesto solo se il nuovo stato è "Pagata"</small>
                   </div>
               </form>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
               <button type="button" class="btn btn-success" id="saveStatoBtn">Salva</button>
           </div>
       </div>
   </div>
</div>

<!-- Modal di conferma eliminazione -->
<div class="modal fade" id="deleteFatturaModal" tabindex="-1" aria-labelledby="deleteFatturaModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header bg-danger text-white">
               <h5 class="modal-title" id="deleteFatturaModalLabel"><i class="bi bi-exclamation-triangle"></i> Conferma Annullamento</h5>
               <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <p>Sei sicuro di voler annullare questa fattura?</p>
               <p>La fattura verrà contrassegnata come "Annullata" e non potrà più essere modificata.</p>
               <div class="alert alert-warning">
                   <i class="bi bi-exclamation-triangle"></i> Attenzione: l'annullamento di una fattura potrebbe influire sulle statistiche e sui report.
               </div>
           </div>
           <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
               <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Conferma</button>
           </div>
       </div>
   </div>
</div>

<style>
/* Stili personalizzati per la fatturazione */
.fatturazione-container {
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

/* Stili per la tabella dettagli */
#dettagliTable input {
   padding: 0.375rem 0.5rem;
}

#dettagliTable .form-control:disabled,
#dettagliTable .form-control[readonly] {
   background-color: #f8f9fa;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
   // Gestione del campo periodo personalizzato
   document.getElementById('periodo').addEventListener('change', function() {
       const dateRangeFields = document.querySelectorAll('.date-range');
       if (this.value === 'custom') {
           dateRangeFields.forEach(field => field.classList.remove('d-none'));
       } else {
           dateRangeFields.forEach(field => field.classList.add('d-none'));
       }
   });
   
   // Gestione del modal per aggiungere/modificare fattura
   const fatturaModal = new bootstrap.Modal(document.getElementById('fatturaModal'));
   const viewFatturaModal = new bootstrap.Modal(document.getElementById('viewFatturaModal'));
   const updateStatoModal = new bootstrap.Modal(document.getElementById('updateStatoModal'));
   const deleteFatturaModal = new bootstrap.Modal(document.getElementById('deleteFatturaModal'));
   
   // Apertura modal per nuova fattura
   document.getElementById('addFatturaBtn').addEventListener('click', function() {
       // Reset form
       document.getElementById('fatturaForm').reset();
       document.getElementById('fattura_id').value = '';
       document.getElementById('fatturaModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Nuova Fattura';
       document.getElementById('numero_fattura').value = '<?php echo $nuovo_numero_fattura; ?>';
       document.getElementById('data_fattura').valueAsDate = new Date();
       document.getElementById('data_scadenza').valueAsDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000); // +30 giorni
       document.getElementById('stato_fattura').value = 'bozza';
       
       // Reset dettagli
       const tbody = document.querySelector('#dettagliTable tbody');
       const rows = tbody.querySelectorAll('tr:not(#riga_template)');
       for (let i = 1; i < rows.length; i++) {
           rows[i].remove();
       }
       
       // Reset primo rigo
       const primaRiga = document.getElementById('riga_1');
       primaRiga.querySelector('.descrizione-item').value = '';
       primaRiga.querySelector('.quantita-item').value = '1';
       primaRiga.querySelector('.prezzo-item').value = '0.00';
       primaRiga.querySelector('.iva-item').value = '22';
       primaRiga.querySelector('.totale-item').value = '0.00';
       
       // Reset totali
       document.getElementById('imponibile').value = '0.00';
       document.getElementById('iva_totale').value = '0.00';
       document.getElementById('totale').value = '0.00';
   });
   
   // Gestione selezione cliente
   document.getElementById('cliente_id').addEventListener('change', function() {
       const clienteId = this.value;
       if (!clienteId) {
           document.getElementById('cliente_info').value = '';
           return;
       }
       
       // Qui andrebbe implementata la chiamata AJAX per ottenere i dati del cliente
       // Per ora simuliamo con dati di esempio
       const clienteInfo = "Mario Rossi\nVia Roma 123\n20100 Milano\nTel: 02 1234567\nEmail: mario.rossi@example.com\nCF: RSSMRA80A01F205X";
       document.getElementById('cliente_info').value = clienteInfo;
   });
   
   // Gestione aggiunta riga dettaglio
   document.getElementById('addItemBtn').addEventListener('click', function() {
       const tbody = document.querySelector('#dettagliTable tbody');
       const template = document.getElementById('riga_template');
       const newRow = template.cloneNode(true);
       const rowCount = tbody.querySelectorAll('tr:not(.d-none)').length + 1;
       
       newRow.id = 'riga_' + rowCount;
       newRow.classList.remove('d-none');
       
       // Aggiorna gli indici nei nomi dei campi
       const inputs = newRow.querySelectorAll('input');
       inputs.forEach(input => {
           const name = input.name.replace(/\[\d+\]/, '[' + rowCount + ']');
           input.name = name;
           input.value = input.classList.contains('quantita-item') ? '1' : 
                        input.classList.contains('iva-item') ? '22' : '0.00';
       });
       
       // Aggiungi event listeners
       addRowEventListeners(newRow);
       
       tbody.appendChild(newRow);
   });
   
   // Funzione per aggiungere event listeners alle righe
   function addRowEventListeners(row) {
       // Rimozione riga
       row.querySelector('.remove-item').addEventListener('click', function() {
           if (document.querySelectorAll('#dettagliTable tbody tr:not(.d-none)').length > 1) {
               row.remove();
               calcolaTotali();
           } else {
               alert('Non è possibile rimuovere l\'ultima riga');
           }
       });
       
       // Calcolo automatico del totale riga
       const quantitaInput = row.querySelector('.quantita-item');
       const prezzoInput = row.querySelector('.prezzo-item');
       const ivaInput = row.querySelector('.iva-item');
       const totaleInput = row.querySelector('.totale-item');
       
       [quantitaInput, prezzoInput, ivaInput].forEach(input => {
           input.addEventListener('input', function() {
               const quantita = parseFloat(quantitaInput.value) || 0;
               const prezzo = parseFloat(prezzoInput.value) || 0;
               const iva = parseFloat(ivaInput.value) || 0;
               
               const totale = quantita * prezzo * (1 + iva / 100);
               totaleInput.value = totale.toFixed(2);
               
               calcolaTotali();
           });
       });
   }
   
   // Aggiungi event listeners alla prima riga
   addRowEventListeners(document.getElementById('riga_1'));
   
   // Funzione per calcolare i totali
   function calcolaTotali() {
       let imponibile = 0;
       let iva = 0;
       
       const rows = document.querySelectorAll('#dettagliTable tbody tr:not(.d-none)');
       rows.forEach(row => {
           const quantita = parseFloat(row.querySelector('.quantita-item').value) || 0;
           const prezzo = parseFloat(row.querySelector('.prezzo-item').value) || 0;
           const ivaPercentuale = parseFloat(row.querySelector('.iva-item').value) || 0;
           
           const rigaImponibile = quantita * prezzo;
           const rigaIva = rigaImponibile * (ivaPercentuale / 100);
           
           imponibile += rigaImponibile;
           iva += rigaIva;
       });
       
       const totale = imponibile + iva;
       
       document.getElementById('imponibile').value = imponibile.toFixed(2);
       document.getElementById('iva_totale').value = iva.toFixed(2);
       document.getElementById('totale').value = totale.toFixed(2);
   }
   
   // Salvataggio fattura
   document.getElementById('saveFatturaBtn').addEventListener('click', function() {
       const form = document.getElementById('fatturaForm');
       
       // Validazione base
       if (!form.checkValidity()) {
           form.reportValidity();
           return;
       }
       
       // Raccolta dati dal form
       const formData = new FormData(form);
       const fatturaData = Object.fromEntries(formData.entries());
       
       // Validazione aggiuntiva
       if (fatturaData.stato === 'pagata' && !fatturaData.data_pagamento) {
           alert('Per le fatture pagate è necessario specificare la data di pagamento.');
           return;
       }
       
       // Qui andrebbe implementata la chiamata AJAX per salvare la fattura
       // Per ora simuliamo un salvataggio con successo
       
       alert('Fattura salvata con successo!');
       fatturaModal.hide();
       
       // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
       // location.reload();
   });
   
   // Gestione pulsanti di modifica
   document.querySelectorAll('.edit-fattura').forEach(button => {
       button.addEventListener('click', function() {
           const fatturaId = this.dataset.id;
           
           // Qui andrebbe implementata la chiamata AJAX per ottenere i dati della fattura
           // Per ora simuliamo con dati di esempio
           const dummyData = {
               id: fatturaId,
               numero: '123/2023',
               data: '2023-05-15',
               tipo: 'fattura',
               cliente_id: 1,
               cliente_info: "Mario Rossi\nVia Roma 123\n20100 Milano\nTel: 02 1234567\nEmail: mario.rossi@example.com\nCF: RSSMRA80A01F205X",
               note: 'Note di esempio',
               metodo_pagamento: 'bonifico',
               data_scadenza: '2023-06-15',
               stato: 'bozza',
               imponibile: 100.00,
               iva: 22.00,
               totale: 122.00,
               dettagli: [
                   {
                       descrizione: 'Prodotto 1',
                       quantita: 1,
                       prezzo: 100.00,
                       iva: 22,
                       totale: 122.00
                   }
               ]
           };
           
           // Popola il form con i dati
           document.getElementById('fattura_id').value = dummyData.id;
           document.getElementById('numero_fattura').value = dummyData.numero;
           document.getElementById('data_fattura').value = dummyData.data;
           document.getElementById('tipo_fattura').value = dummyData.tipo;
           document.getElementById('cliente_id').value = dummyData.cliente_id;
           document.getElementById('cliente_info').value = dummyData.cliente_info;
           document.getElementById('note').value = dummyData.note;
           document.getElementById('metodo_pagamento').value = dummyData.metodo_pagamento;
           document.getElementById('data_scadenza').value = dummyData.data_scadenza;
           document.getElementById('stato_fattura').value = dummyData.stato;
           document.getElementById('imponibile').value = dummyData.imponibile.toFixed(2);
           document.getElementById('iva_totale').value = dummyData.iva.toFixed(2);
           document.getElementById('totale').value = dummyData.totale.toFixed(2);
           
           // Popola i dettagli
           const tbody = document.querySelector('#dettagliTable tbody');
           const rows = tbody.querySelectorAll('tr:not(#riga_template)');
           for (let i = 1; i < rows.length; i++) {
               rows[i].remove();
           }
           
           // Popola la prima riga
           const primaRiga = document.getElementById('riga_1');
           primaRiga.querySelector('.descrizione-item').value = dummyData.dettagli[0].descrizione;
           primaRiga.querySelector('.quantita-item').value = dummyData.dettagli[0].quantita;
           primaRiga.querySelector('.prezzo-item').value = dummyData.dettagli[0].prezzo.toFixed(2);
           primaRiga.querySelector('.iva-item').value = dummyData.dettagli[0].iva;
           primaRiga.querySelector('.totale-item').value = dummyData.dettagli[0].totale.toFixed(2);
           
           // Aggiungi righe aggiuntive se necessario
           for (let i = 1; i < dummyData.dettagli.length; i++) {
               const dettaglio = dummyData.dettagli[i];
               document.getElementById('addItemBtn').click();
               const newRow = document.getElementById('riga_' + (i + 1));
               
               newRow.querySelector('.descrizione-item').value = dettaglio.descrizione;
               newRow.querySelector('.quantita-item').value = dettaglio.quantita;
               newRow.querySelector('.prezzo-item').value = dettaglio.prezzo.toFixed(2);
               newRow.querySelector('.iva-item').value = dettaglio.iva;
               newRow.querySelector('.totale-item').value = dettaglio.totale.toFixed(2);
           }
           
           // Aggiorna il titolo del modal
           document.getElementById('fatturaModalLabel').innerHTML = '<i class="bi bi-pencil"></i> Modifica Fattura';
           
           // Mostra il modal
           fatturaModal.show();
       });
   });
   
   // Gestione pulsanti di visualizzazione
   document.querySelectorAll('.view-fattura').forEach(button => {
       button.addEventListener('click', function() {
           const fatturaId = this.dataset.id;
           
           // Qui andrebbe implementata la chiamata AJAX per ottenere i dati della fattura
           // Per ora simuliamo con dati di esempio
           const dummyData = {
               id: fatturaId,
               numero: '123/2023',
               data: '15/05/2023',
               tipo: 'Fattura',
               cliente_nome: 'Mario Rossi',
               cliente_cf: 'RSSMRA80A01F205X',
               cliente_piva: '',
               cliente_indirizzo: 'Via Roma 123, 20100 Milano',
               note: 'Note di esempio',
               metodo_pagamento: 'Bonifico Bancario',
               data_scadenza: '15/06/2023',
               data_pagamento: '',
               stato: '<span class="badge bg-primary">Emessa</span>',
               imponibile: '100,00 €',
               iva: '22,00 €',
               totale: '122,00 €',
               operatore: 'Admin',
               data_modifica: '15/05/2023 14:30',
               dettagli: [
                   {
                       descrizione: 'Prodotto 1',
                       quantita: 1,
                       prezzo: '100,00 €',
                       iva: '22%',
                       totale: '122,00 €'
                   }
               ]
           };
           
           // Popola il modal con i dati
           document.getElementById('view_id').textContent = dummyData.id;
           document.getElementById('view_numero').textContent = dummyData.numero;
           document.getElementById('view_data').textContent = dummyData.data;
           document.getElementById('view_tipo').textContent = dummyData.tipo;
           document.getElementById('view_stato').innerHTML = dummyData.stato;
           document.getElementById('view_cliente_nome').textContent = dummyData.cliente_nome;
           document.getElementById('view_cliente_cf').textContent = dummyData.cliente_cf || '-';
           document.getElementById('view_cliente_piva').textContent = dummyData.cliente_piva || '-';
           document.getElementById('view_cliente_indirizzo').textContent = dummyData.cliente_indirizzo;
           document.getElementById('view_note').textContent = dummyData.note || 'Nessuna nota';
           document.getElementById('view_metodo_pagamento').textContent = dummyData.metodo_pagamento;
           document.getElementById('view_data_scadenza').textContent = dummyData.data_scadenza || '-';
           document.getElementById('view_data_pagamento').textContent = dummyData.data_pagamento || 'Non pagata';
           document.getElementById('view_operatore').textContent = dummyData.operatore;
           document.getElementById('view_data_modifica').textContent = dummyData.data_modifica;
           document.getElementById('view_imponibile').textContent = dummyData.imponibile;
           document.getElementById('view_iva').textContent = dummyData.iva;
           document.getElementById('view_totale').textContent = dummyData.totale;
           
           // Popola i dettagli
           const tbody = document.getElementById('view_dettagli_body');
           tbody.innerHTML = '';
           
           dummyData.dettagli.forEach(dettaglio => {
               const row = document.createElement('tr');
               row.innerHTML = `
                   <td>${dettaglio.descrizione}</td>
                   <td class="text-center">${dettaglio.quantita}</td>
                   <td class="text-end">${dettaglio.prezzo}</td>
                   <td class="text-center">${dettaglio.iva}</td>
                   <td class="text-end">${dettaglio.totale}</td>
               `;
               tbody.appendChild(row);
           });
           
           // Salva l'ID per i pulsanti
           document.getElementById('printFromViewBtn').dataset.id = fatturaId;
           document.getElementById('updateStatoFromViewBtn').dataset.id = fatturaId;
           
           // Mostra il modal
           viewFatturaModal.show();
       });
   });
   
   // Gestione pulsante stampa dal modal di visualizzazione
   document.getElementById('printFromViewBtn').addEventListener('click', function() {
       const fatturaId = this.dataset.id;
       
       // Qui andrebbe implementata la logica di stampa
       alert('Funzionalità di stampa in fase di implementazione');
   });
   
   // Gestione pulsante aggiorna stato dal modal di visualizzazione
   document.getElementById('updateStatoFromViewBtn').addEventListener('click', function() {
       const fatturaId = this.dataset.id;
       
       // Chiudi il modal di visualizzazione
       viewFatturaModal.hide();
       
       // Simula il click sul pulsante di aggiornamento stato corrispondente
       document.querySelector(`.update-stato[data-id="${fatturaId}"]`).click();
   });
   
   // Gestione pulsanti di aggiornamento stato
   document.querySelectorAll('.update-stato').forEach(button => {
       button.addEventListener('click', function() {
           const fatturaId = this.dataset.id;
           
           // Qui andrebbe implementata la chiamata AJAX per ottenere i dati della fattura
           // Per ora simuliamo con dati di esempio
           const dummyData = {
               id: fatturaId,
               numero: '123/2023',
               stato: 'emessa',
               stato_nome: 'Emessa'
           };
           
           // Popola il form con i dati
           document.getElementById('stato_fattura_id').value = dummyData.id;
           document.getElementById('fattura_numero').value = dummyData.numero;
           document.getElementById('stato_attuale').value = dummyData.stato_nome;
           document.getElementById('nuovo_stato').value = dummyData.stato;
           
           // Mostra/nascondi il campo data pagamento in base allo stato selezionato
           toggleDataPagamento();
           
           // Mostra il modal
           updateStatoModal.show();
       });
   });
   
   // Gestione del campo stato per mostrare/nascondere la data di pagamento
   document.getElementById('nuovo_stato').addEventListener('change', toggleDataPagamento);
   
   function toggleDataPagamento() {
       const nuovoStato = document.getElementById('nuovo_stato').value;
       const dataPagamentoContainer = document.getElementById('data_pagamento_container');
       
       if (nuovoStato === 'pagata') {
           dataPagamentoContainer.classList.remove('d-none');
           document.getElementById('data_pagamento').required = true;
       } else {
           dataPagamentoContainer.classList.add('d-none');
           document.getElementById('data_pagamento').required = false;
       }
   }
   
   // Salvataggio stato
   document.getElementById('saveStatoBtn').addEventListener('click', function() {
       const form = document.getElementById('statoForm');
       
       // Validazione base
       if (!form.checkValidity()) {
           form.reportValidity();
           return;
       }
       
       // Raccolta dati dal form
       const formData = new FormData(form);
       const statoData = Object.fromEntries(formData.entries());
       
       // Validazione aggiuntiva
       if (statoData.stato === 'pagata' && !statoData.data_pagamento) {
           alert('Per le fatture pagate è necessario specificare la data di pagamento.');
           return;
       }
       
       // Qui andrebbe implementata la chiamata AJAX per salvare lo stato
       // Per ora simuliamo un salvataggio con successo
       
       alert('Stato aggiornato con successo!');
       updateStatoModal.hide();
       
       // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
       // location.reload();
   });
   
   // Gestione pulsanti di eliminazione
   document.querySelectorAll('.delete-fattura').forEach(button => {
       button.addEventListener('click', function() {
           const fatturaId = this.dataset.id;
           
           // Salva l'ID per il pulsante di conferma
           document.getElementById('confirmDeleteBtn').dataset.id = fatturaId;
           
           // Mostra il modal
           deleteFatturaModal.show();
       });
   });
   
   // Conferma eliminazione
   document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
       const fatturaId = this.dataset.id;
       
       // Qui andrebbe implementata la chiamata AJAX per annullare la fattura
       // Per ora simuliamo un'operazione con successo
       
       alert('Fattura annullata con successo!');
       deleteFatturaModal.hide();
       
       // In un'implementazione reale, qui si aggiornerebbe la tabella o si ricaricherebbe la pagina
       // location.reload();
   });
   
   // Esportazione fatture
   document.getElementById('exportFattureBtn').addEventListener('click', function() {
       // Qui andrebbe implementata la logica per esportare le fatture
       alert('Funzionalità di esportazione in fase di implementazione');
   });
   
   // Invio fatture per email
   document.getElementById('emailFattureBtn').addEventListener('click', function() {
       // Qui andrebbe implementata la logica per inviare le fatture per email
       alert('Funzionalità di invio email in fase di implementazione');
   });
   
   // Gestione stampa fattura
   document.querySelectorAll('.print-fattura').forEach(button => {
       button.addEventListener('click', function() {
           const fatturaId = this.dataset.id;
           
           // Qui andrebbe implementata la logica di stampa
           alert('Funzionalità di stampa in fase di implementazione');
       });
   });
   
   // Inizializzazione grafici se ci sono dati
   <?php if (!empty($fatture)): ?>
   // Dati per il grafico degli stati
   const statiData = {
       labels: ['Bozza', 'Emessa', 'Pagata', 'Scaduta', 'Annullata'],
       datasets: [{
           data: [
               <?php echo $totali_fatture['per_stato']['bozza']; ?>,
               <?php echo $totali_fatture['per_stato']['emessa']; ?>,
               <?php echo $totali_fatture['per_stato']['pagata']; ?>,
               <?php echo $totali_fatture['per_stato']['scaduta']; ?>,
               <?php echo $totali_fatture['per_stato']['annullata']; ?>
           ],
           backgroundColor: [
               'rgba(108, 117, 125, 0.7)',
               'rgba(13, 110, 253, 0.7)',
               'rgba(25, 135, 84, 0.7)',
               'rgba(255, 193, 7, 0.7)',
               'rgba(220, 53, 69, 0.7)'
           ],
           borderColor: [
               'rgba(108, 117, 125, 1)',
               'rgba(13, 110, 253, 1)',
               'rgba(25, 135, 84, 1)',
               'rgba(255, 193, 7, 1)',
               'rgba(220, 53, 69, 1)'
           ],
           borderWidth: 1
       }]
   };
   
   // Dati per il grafico del fatturato mensile
   const fatturatoData = {
       labels: ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
       datasets: [{
           label: 'Fatturato',
           data: [
               <?php 
               // In un'implementazione reale, questi dati verrebbero calcolati dinamicamente
               echo "1000, 1200, 900, 1500, 2000, 1800, 1600, 1400, 1700, 1900, 2100, 2200";
               ?>
           ],
           backgroundColor: 'rgba(13, 110, 253, 0.7)',
           borderColor: 'rgba(13, 110, 253, 1)',
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
               beginAtZero: true,
               ticks: {
                   callback: function(value) {
                       return value + ' €';
                   }
               }
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
   
   // Creazione grafico fatturato
   const fatturatoChart = new Chart(
       document.getElementById('fatturatoChart').getContext('2d'),
       {
           type: 'bar',
           data: fatturatoData,
           options: barChartOptions
       }
   );
   <?php endif; ?>
});
</script>

