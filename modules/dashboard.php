<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Ottieni il nome dell'utente corrente
$user_nome = $_SESSION['nome'] ?? '';
$user_cognome = $_SESSION['cognome'] ?? '';
$user_ruolo = $_SESSION['ruolo'] ?? '';

// Ottieni l'ora del giorno per il saluto
$ora = date('H');
if ($ora < 12) {
    $saluto = "Buongiorno";
} elseif ($ora < 18) {
    $saluto = "Buon pomeriggio";
} else {
    $saluto = "Buonasera";
}

// Date per le query
$oggi = date('Y-m-d');
$ieri = date('Y-m-d', strtotime('-1 day'));
$inizio_settimana = date('Y-m-d', strtotime('monday this week'));
$inizio_mese = date('Y-m-01');
$data_30_giorni_fa = date('Y-m-d', strtotime('-30 days'));

// Statistiche di vendita per oggi
$stmt = $pdo->prepare("SELECT COUNT(*) as num_transazioni, COALESCE(SUM(totale), 0) as totale_vendite FROM transazioni WHERE DATE(data) = ?");
$stmt->execute([$oggi]);
$stats_oggi = $stmt->fetch();

// Statistiche di vendita per questa settimana
$stmt = $pdo->prepare("SELECT COUNT(*) as num_transazioni, COALESCE(SUM(totale), 0) as totale_vendite FROM transazioni WHERE DATE(data) >= ?");
$stmt->execute([$inizio_settimana]);
$stats_settimana = $stmt->fetch();

// Statistiche di vendita per questo mese
$stmt = $pdo->prepare("SELECT COUNT(*) as num_transazioni, COALESCE(SUM(totale), 0) as totale_vendite FROM transazioni WHERE DATE(data) >= ?");
$stmt->execute([$inizio_mese]);
$stats_mese = $stmt->fetch();

// Conteggio prodotti
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM prodotti");
$stmt->execute();
$total_products = $stmt->fetch()['total'];

// Prodotti con scorte basse
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM prodotti WHERE quantita < 10");
$stmt->execute();
$low_stock_products = $stmt->fetch()['total'];

// Conteggio clienti
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM clienti");
$stmt->execute();
$total_customers = $stmt->fetch()['total'];

// Nuovi clienti questo mese (basato sulle transazioni)
$stmt = $pdo->prepare("
    SELECT COUNT(DISTINCT CONCAT(cliente_nome, cliente_cognome)) as total 
    FROM transazioni 
    WHERE DATE(data) >= ? AND cliente_nome IS NOT NULL AND cliente_nome != ''
");
$stmt->execute([$inizio_mese]);
$customers_month = $stmt->fetch()['total'];

// Conteggio assistenze
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM assistenza");
$stmt->execute();
$total_assistance = $stmt->fetch()['total'];

// Assistenze in attesa o in lavorazione
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM assistenza WHERE stato = 'in attesa' OR stato = 'in lavorazione'");
$stmt->execute();
$pending_assistance = $stmt->fetch()['total'];

// Target attivi
$stmt = $pdo->prepare("
    SELECT t.*, n.nome as negozio_nome
    FROM target t
    LEFT JOIN negozi n ON t.negozio_id = n.id
    WHERE t.data_fine >= ? AND t.stato = 'attivo'
    ORDER BY t.data_fine ASC
    LIMIT 5
");
$stmt->execute([$oggi]);
$target_attivi = $stmt->fetchAll();

// Ultime transazioni
$stmt = $pdo->prepare("
    SELECT t.*, CONCAT(o.nome, ' ', o.cognome) as operatore_nome
    FROM transazioni t
    LEFT JOIN operatori o ON t.operatore_id = o.id
    ORDER BY t.data DESC
    LIMIT 5
");
$stmt->execute();
$latest_transactions = $stmt->fetchAll();

// Ultimi clienti (usando transazioni per compatibilità)
$stmt = $pdo->prepare("
    SELECT DISTINCT cliente_nome, cliente_cognome, cliente_telefono, cliente_email, MAX(data) as ultima_visita
    FROM transazioni
    WHERE cliente_nome IS NOT NULL AND cliente_nome != ''
    GROUP BY cliente_nome, cliente_cognome, cliente_telefono, cliente_email
    ORDER BY ultima_visita DESC
    LIMIT 5
");
$stmt->execute();
$latest_customers = $stmt->fetchAll();

// Prodotti più venduti (ultimi 30 giorni)
$stmt = $pdo->prepare("
    SELECT td.nome, SUM(td.quantita) as quantita_totale
    FROM transazioni_dettaglio td
    JOIN transazioni t ON td.transazione_id = t.id
    WHERE DATE(t.data) >= ?
    GROUP BY td.nome
    ORDER BY quantita_totale DESC
    LIMIT 5
");
$stmt->execute([$data_30_giorni_fa]);
$top_products = $stmt->fetchAll();

// Dati per il grafico vendite settimanali
$start_date = date('Y-m-d', strtotime('-6 days'));
$stmt = $pdo->prepare("
    SELECT DATE(data) as giorno, COALESCE(SUM(totale), 0) as totale_vendite
    FROM transazioni
    WHERE DATE(data) >= ?
    GROUP BY DATE(data)
    ORDER BY DATE(data)
");
$stmt->execute([$start_date]);
$sales_results = $stmt->fetchAll();

// Prepara array con tutti i giorni
$weekly_sales = [];
for ($i = 6; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i days"));
    $day_formatted = date('d/m', strtotime($day));
    $weekly_sales[$day] = [
        'giorno' => $day_formatted,
        'vendite' => 0
    ];
}

// Popola con i risultati reali
foreach ($sales_results as $row) {
    $day = $row['giorno'];
    $day_formatted = date('d/m', strtotime($day));
    $weekly_sales[$day] = [
        'giorno' => $day_formatted,
        'vendite' => floatval($row['totale_vendite'])
    ];
}

// Converti in array per JSON
$weekly_sales_data = array_values($weekly_sales);
$weekly_sales_json = json_encode($weekly_sales_data);
?>

<div class="dashboard-container">
    <!-- Intestazione Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card welcome-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1"><?php echo $saluto; ?>, <?php echo htmlspecialchars($user_nome); ?>!</h2>
                            <p class="text-muted mb-0">Ecco un riepilogo delle attività del tuo negozio</p>
                        </div>
                        <div class="text-end">
                            <p class="mb-0"><i class="bi bi-calendar3"></i> <?php echo date('d/m/Y'); ?></p>
                            <p class="mb-0"><i class="bi bi-clock"></i> <?php echo date('H:i'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiche di Vendita -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Oggi</h5>
                        <div class="stats-icon bg-primary-light">
                            <i class="bi bi-calendar-day text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo number_format($stats_oggi['totale_vendite'], 2); ?> €</h3>
                    <p class="text-muted mb-0"><?php echo $stats_oggi['num_transazioni']; ?> transazioni</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Questa Settimana</h5>
                        <div class="stats-icon bg-success-light">
                            <i class="bi bi-calendar-week text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo number_format($stats_settimana['totale_vendite'], 2); ?> €</h3>
                    <p class="text-muted mb-0"><?php echo $stats_settimana['num_transazioni']; ?> transazioni</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Questo Mese</h5>
                        <div class="stats-icon bg-info-light">
                            <i class="bi bi-calendar-month text-info"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo number_format($stats_mese['totale_vendite'], 2); ?> €</h3>
                    <p class="text-muted mb-0"><?php echo $stats_mese['num_transazioni']; ?> transazioni</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Statistiche Generali -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Prodotti</h5>
                        <div class="stats-icon bg-warning-light">
                            <i class="bi bi-box text-warning"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo $total_products; ?></h3>
                    <p class="text-muted mb-0"><?php echo $low_stock_products; ?> con scorte basse</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Clienti</h5>
                        <div class="stats-icon bg-danger-light">
                            <i class="bi bi-people text-danger"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo $total_customers; ?></h3>
                    <p class="text-muted mb-0"><?php echo $customers_month; ?> nuovi questo mese</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Assistenza</h5>
                        <div class="stats-icon bg-primary-light">
                            <i class="bi bi-tools text-primary"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo $total_assistance; ?></h3>
                    <p class="text-muted mb-0"><?php echo $pending_assistance; ?> in attesa</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Target</h5>
                        <div class="stats-icon bg-success-light">
                            <i class="bi bi-bullseye text-success"></i>
                        </div>
                    </div>
                    <h3 class="mb-1"><?php echo count($target_attivi); ?></h3>
                    <p class="text-muted mb-0">Target attivi</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Accesso Rapido -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Accesso Rapido</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?module=pos" class="quick-access-card">
                                <div class="quick-access-icon bg-primary-light">
                                    <i class="bi bi-cart text-primary"></i>
                                </div>
                                <div class="quick-access-text">
                                    <h6>Punto Vendita</h6>
                                    <p class="mb-0">Gestisci vendite</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?module=listini" class="quick-access-card">
                                <div class="quick-access-icon bg-success-light">
                                    <i class="bi bi-file-text text-success"></i>
                                </div>
                                <div class="quick-access-text">
                                    <h6>Listini</h6>
                                    <p class="mb-0">Gestisci offerte</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?module=target" class="quick-access-card">
                                <div class="quick-access-icon bg-warning-light">
                                    <i class="bi bi-bullseye text-warning"></i>
                                </div>
                                <div class="quick-access-text">
                                    <h6>Target</h6>
                                    <p class="mb-0">Monitora obiettivi</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="index.php?module=assistenza" class="quick-access-card">
                                <div class="quick-access-icon bg-danger-light">
                                    <i class="bi bi-tools text-danger"></i>
                                </div>
                                <div class="quick-access-text">
                                    <h6>Assistenza</h6>
                                    <p class="mb-0">Gestisci riparazioni</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grafico Vendite Settimanali -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Andamento Vendite Settimanali</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownPeriodo" data-bs-toggle="dropdown" aria-expanded="false">
                            Ultimi 7 giorni
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownPeriodo">
                            <li><a class="dropdown-item active" href="#">Ultimi 7 giorni</a></li>
                            <li><a class="dropdown-item" href="#">Ultimi 30 giorni</a></li>
                            <li><a class="dropdown-item" href="#">Questo mese</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:300px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenuto principale -->
    <div class="row">
        <!-- Ultime Transazioni -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ultime Transazioni</h5>
                    <a href="index.php?module=pos" class="btn btn-sm btn-outline-primary">Vedi Tutte</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Data</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Importo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latest_transactions)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3">Nessuna transazione trovata</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($latest_transactions as $transazione): ?>
                                <tr>
                                    <td><?php echo $transazione['id']; ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($transazione['data'])); ?></td>
                                    <td>
                                        <?php 
                                        $cliente = trim($transazione['cliente_nome'] . ' ' . $transazione['cliente_cognome']);
                                        echo $cliente ? htmlspecialchars($cliente) : 'Cliente generico';
                                        ?>
                                    </td>
                                    <td class="text-end"><?php echo number_format($transazione['totale'], 2); ?> €</td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Ultimi Clienti -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Ultimi Clienti</h5>
                    <a href="index.php?module=personale" class="btn btn-sm btn-outline-primary">Gestisci Clienti</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Telefono</th>
                                    <th>Email</th>
                                    <th>Ultima Visita</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($latest_customers)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3">Nessun cliente trovato</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($latest_customers as $cliente): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cliente['cliente_nome'] . ' ' . $cliente['cliente_cognome']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['cliente_telefono'] ?? '-'); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['cliente_email'] ?? '-'); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($cliente['ultima_visita'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Target in Corso -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Target in Corso</h5>
                    <a href="index.php?module=target" class="btn btn-sm btn-outline-primary">Vedi Tutti</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($target_attivi)): ?>
                    <div class="text-center py-4">
                        <p class="mb-0">Nessun target attivo al momento</p>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($target_attivi as $target): ?>
                        <?php 
                        $percentuale = ($target['raggiunto'] / $target['obiettivo']) * 100;
                        $percentuale = min(100, $percentuale);
                        
                        if ($percentuale < 33) {
                            $progress_class = 'danger';
                        } elseif ($percentuale < 66) {
                            $progress_class = 'warning';
                        } else {
                            $progress_class = 'success';
                        }
                        ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="mb-0"><?php echo htmlspecialchars($target['nome']); ?></h6>
                                <span class="badge bg-<?php echo $progress_class; ?>"><?php echo number_format($percentuale, 0); ?>%</span>
                            </div>
                            <p class="text-muted small mb-1">
                                <?php echo htmlspecialchars($target['negozio_nome'] ?? 'Tutti i negozi'); ?> | 
                                Scadenza: <?php echo date('d/m/Y', strtotime($target['data_fine'])); ?>
                            </p>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-<?php echo $progress_class; ?>" role="progressbar" style="width: <?php echo $percentuale; ?>%" aria-valuenow="<?php echo $percentuale; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <small><?php echo $target['raggiunto']; ?> / <?php echo $target['obiettivo']; ?></small>
                                <small>Premio: <?php echo number_format($target['premio_base'], 2); ?> €</small>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Prodotti Più Venduti -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Prodotti Più Venduti</h5>
                    <span class="badge bg-secondary">Ultimi 30 giorni</span>
                </div>
                <div class="card-body">
                    <?php if (empty($top_products)): ?>
                    <div class="text-center py-4">
                        <p class="mb-0">Nessun prodotto venduto in questo periodo</p>
                    </div>
                    <?php else: ?>
                    <div class="chart-container">
                        <?php 
                        $max_quantita = max(array_column($top_products, 'quantita_totale'));
                        $colors = ['primary', 'success', 'info', 'warning', 'danger'];
                        
                        foreach ($top_products as $index => $prodotto): 
                            $width = ($prodotto['quantita_totale'] / $max_quantita) * 100;
                            $color = $colors[$index % count($colors)];
                        ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span><?php echo htmlspecialchars($prodotto['nome']); ?></span>
                                <span class="badge bg-<?php echo $color; ?>"><?php echo $prodotto['quantita_totale']; ?></span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-<?php echo $color; ?>" role="progressbar" style="width: <?php echo $width; ?>%" aria-valuenow="<?php echo $prodotto['quantita_totale']; ?>" aria-valuemin="0" aria-valuemax="<?php echo $max_quantita; ?>"></div>
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

<style>
/* Stili per la dashboard */
.welcome-card {
    background: linear-gradient(to right, #2e8b57, #3cb371);
    color: white;
    border: none;
    border-radius: 10px;
}

.stats-card {
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

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

.bg-info-light {
    background-color: rgba(13, 202, 240, 0.1);
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1);
}

.bg-danger-light {
    background-color: rgba(220, 53, 69, 0.1);
}

.quick-access-card {
    display: flex;
    align-items: center;
    padding: 15px;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    transition: transform 0.3s, box-shadow 0.3s;
    text-decoration: none;
    color: inherit;
}

.quick-access-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    color: inherit;
}

.quick-access-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 15px;
}

.quick-access-text {
    flex: 1;
}

.quick-access-text h6 {
    margin-bottom: 0;
    font-weight: 600;
}

.quick-access-text p {
    color: #6c757d;
    font-size: 0.85rem;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.progress {
    background-color: #f5f5f5;
}

.chart-container {
    position: relative;
    margin: auto;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dati per il grafico vendite settimanali
    const weeklyData = <?php echo $weekly_sales_json; ?>;
    
    // Prepara i dati per Chart.js
    const labels = weeklyData.map(item => item.giorno);
    const data = weeklyData.map(item => item.vendite);
    
    // Crea il grafico
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Vendite (€)',
                data: data,
                backgroundColor: 'rgba(46, 139, 87, 0.7)',
                borderColor: 'rgba(46, 139, 87, 1)',
                borderWidth: 1,
                borderRadius: 5,
                maxBarThickness: 50
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y.toFixed(2) + ' €';
                        }
                    }
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
        }
    });
    
    // Gestione del dropdown per il periodo
    const dropdownItems = document.querySelectorAll('#dropdownPeriodo + .dropdown-menu .dropdown-item');
    const dropdownButton = document.querySelector('#dropdownPeriodo');
    
    dropdownItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const periodo = this.textContent.trim();
            dropdownButton.textContent = periodo;
            
            // Qui andrebbe implementata la logica per aggiornare i dati in base al periodo
            // Per ora è solo dimostrativo
            
            // Aggiorna la classe active
            dropdownItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>

