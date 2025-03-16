<?php
// Verifica dell'autenticazione
require_once 'includes/auth_check.php';

// Verifica dei permessi di accesso alla cassa
if (!hasPermission('view_cassa')) {
    echo "<div class='alert alert-danger'>Non hai i permessi per accedere a questa sezione.</div>";
    exit;
}

// Flag per i permessi specifici
$can_open_close = hasPermission('open_close_cassa');
$can_add_entrata = hasPermission('add_entrata_cassa');
$can_add_uscita = hasPermission('add_uscita_cassa');
$can_delete = hasPermission('delete_movimento_cassa');
$can_export = hasPermission('export_report_cassa');

// Recupero dati cassa dal database
$db = getDbConnection();

// Recupera il saldo attuale della cassa
$stmt = $db->prepare("SELECT 
                        COALESCE(SUM(CASE WHEN tipo = 'entrata' THEN importo ELSE 0 END), 0) - 
                        COALESCE(SUM(CASE WHEN tipo = 'uscita' THEN importo ELSE 0 END), 0) AS saldo_attuale,
                        MAX(CASE WHEN operazione = 'apertura' THEN data_operazione ELSE NULL END) AS ultima_apertura,
                        MAX(CASE WHEN operazione = 'chiusura' THEN data_operazione ELSE NULL END) AS ultima_chiusura
                      FROM movimenti_cassa");
$stmt->execute();
$cassa_info = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se la cassa è aperta
$cassa_aperta = ($cassa_info['ultima_apertura'] > $cassa_info['ultima_chiusura'] || 
                ($cassa_info['ultima_apertura'] && !$cassa_info['ultima_chiusura']));

// Recupera i movimenti di cassa (ultimi 50)
$stmt = $db->prepare("SELECT mc.*, u.nome, u.cognome 
                      FROM movimenti_cassa mc
                      LEFT JOIN utenti u ON mc.id_utente = u.id
                      ORDER BY mc.data_operazione DESC
                      LIMIT 50");
$stmt->execute();
$movimenti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recupera statistiche per il dashboard
$stmt = $db->prepare("SELECT 
                        COUNT(*) AS totale_movimenti,
                        COALESCE(SUM(CASE WHEN tipo = 'entrata' THEN importo ELSE 0 END), 0) AS totale_entrate,
                        COALESCE(SUM(CASE WHEN tipo = 'uscita' THEN importo ELSE 0 END), 0) AS totale_uscite,
                        COUNT(CASE WHEN DATE(data_operazione) = CURDATE() THEN 1 END) AS movimenti_oggi
                      FROM movimenti_cassa");
$stmt->execute();
$statistiche = $stmt->fetch(PDO::FETCH_ASSOC);

// Recupera i dati per il grafico
$stmt = $db->prepare("SELECT 
                        DATE(data_operazione) AS data,
                        SUM(CASE WHEN tipo = 'entrata' THEN importo ELSE 0 END) AS entrate,
                        SUM(CASE WHEN tipo = 'uscita' THEN importo ELSE 0 END) AS uscite
                      FROM movimenti_cassa
                      WHERE data_operazione >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                      GROUP BY DATE(data_operazione)
                      ORDER BY data");
$stmt->execute();
$dati_grafico = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatta i dati per il grafico
$labels = [];
$entrate_data = [];
$uscite_data = [];

foreach ($dati_grafico as $dato) {
    $labels[] = date('d/m', strtotime($dato['data']));
    $entrate_data[] = $dato['entrate'];
    $uscite_data[] = $dato['uscite'];
}

// Chiudi la connessione
$db = null;
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestione Cassa</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Gestione Cassa</li>
    </ol>

    <!-- Dashboard Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Saldo Attuale</div>
                            <div class="fs-4">€ <?= number_format($cassa_info['saldo_attuale'], 2, ',', '.') ?></div>
                        </div>
                        <div>
                            <i class="fas fa-cash-register fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">
                        <?php if ($cassa_aperta): ?>
                            <span class="badge bg-success">Cassa Aperta</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Cassa Chiusa</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Entrate Totali</div>
                            <div class="fs-4">€ <?= number_format($statistiche['totale_entrate'], 2, ',', '.') ?></div>
                        </div>
                        <div>
                            <i class="fas fa-arrow-circle-up fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#filtraMovimentiModal" data-tipo="entrata">Visualizza dettagli</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Uscite Totali</div>
                            <div class="fs-4">€ <?= number_format($statistiche['totale_uscite'], 2, ',', '.') ?></div>
                        </div>
                        <div>
                            <i class="fas fa-arrow-circle-down fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="#" data-bs-toggle="modal" data-bs-target="#filtraMovimentiModal" data-tipo="uscita">Visualizza dettagli</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small">Movimenti Totali</div>
                            <div class="fs-4"><?= $statistiche['totale_movimenti'] ?></div>
                        </div>
                        <div>
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <div class="small text-white">
                        <span class="badge bg-light text-dark"><?= $statistiche['movimenti_oggi'] ?> oggi</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pulsanti Azioni -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex justify-content-between">
                    <div>
                        <?php if ($can_open_close): ?>
                            <?php if ($cassa_aperta): ?>
                                <button class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#chiusuraCassaModal">
                                    <i class="fas fa-door-closed me-1"></i> Chiudi Cassa
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#aperturaCassaModal">
                                    <i class="fas fa-door-open me-1"></i> Apri Cassa
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($cassa_aperta): ?>
                                <span class="badge bg-success p-2">Cassa Aperta</span>
                            <?php else: ?>
                                <span class="badge bg-danger p-2">Cassa Chiusa</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php if ($can_add_entrata): ?>
                            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#nuovaEntrataModal" <?= !$cassa_aperta ? 'disabled' : '' ?>>
                                <i class="fas fa-plus-circle me-1"></i> Nuova Entrata
                            </button>
                        <?php endif; ?>
                        <?php if ($can_add_uscita): ?>
                            <button class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#nuovaUscitaModal" <?= !$cassa_aperta ? 'disabled' : '' ?>>
                                <i class="fas fa-minus-circle me-1"></i> Nuova Uscita
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#reportCassaModal">
                            <i class="fas fa-file-alt me-1"></i> Report
                        </button>
                        <?php if ($can_export): ?>
                            <button class="btn btn-secondary" id="esportaMovimentiBtn">
                                <i class="fas fa-file-export me-1"></i> Esporta
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafico e Tabella -->
    <div class="row">
        <!-- Grafico -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Andamento Cassa (Ultimi 30 giorni)
                </div>
                <div class="card-body">
                    <canvas id="cassaChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Filtri -->
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-filter me-1"></i>
                    Filtri Rapidi
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filtroDataInizio" class="form-label">Data Inizio</label>
                            <input type="date" class="form-control" id="filtroDataInizio">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filtroDataFine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="filtroDataFine">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filtroTipo" class="form-label">Tipo Movimento</label>
                            <select class="form-select" id="filtroTipo">
                                <option value="">Tutti</option>
                                <option value="entrata">Entrate</option>
                                <option value="uscita">Uscite</option>
                                <option value="apertura">Aperture</option>
                                <option value="chiusura">Chiusure</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filtroCategoria" class="form-label">Categoria</label>
                            <select class="form-select" id="filtroCategoria">
                                <option value="">Tutte</option>
                                <option value="vendita">Vendita</option>
                                <option value="ricarica">Ricarica</option>
                                <option value="abbonamento">Abbonamento</option>
                                <option value="fornitore">Fornitore</option>
                                <option value="spese">Spese</option>
                                <option value="altro">Altro</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary" id="applicaFiltriBtn">
                            <i class="fas fa-search me-1"></i> Applica Filtri
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabella Movimenti -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Movimenti di Cassa
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="movimentiTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Data</th>
                            <th>Operazione</th>
                            <th>Tipo</th>
                            <th>Categoria</th>
                            <th>Descrizione</th>
                            <th>Importo</th>
                            <th>Operatore</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($movimenti as $movimento): ?>
                            <tr class="<?= $movimento['tipo'] == 'entrata' ? 'table-success' : ($movimento['tipo'] == 'uscita' ? 'table-danger' : '') ?>">
                                <td><?= $movimento['id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($movimento['data_operazione'])) ?></td>
                                <td>
                                    <?php if ($movimento['operazione'] == 'movimento'): ?>
                                        <span class="badge bg-primary">Movimento</span>
                                    <?php elseif ($movimento['operazione'] == 'apertura'): ?>
                                        <span class="badge bg-success">Apertura</span>
                                    <?php elseif ($movimento['operazione'] == 'chiusura'): ?>
                                        <span class="badge bg-danger">Chiusura</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($movimento['tipo'] == 'entrata'): ?>
                                        <span class="text-success"><i class="fas fa-arrow-up me-1"></i> Entrata</span>
                                    <?php elseif ($movimento['tipo'] == 'uscita'): ?>
                                        <span class="text-danger"><i class="fas fa-arrow-down me-1"></i> Uscita</span>
                                    <?php else: ?>
                                        <span class="text-secondary">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= ucfirst($movimento['categoria'] ?: '-') ?></td>
                                <td><?= htmlspecialchars($movimento['descrizione']) ?></td>
                                <td class="text-end">
                                    <?php if ($movimento['importo'] > 0): ?>
                                        € <?= number_format($movimento['importo'], 2, ',', '.') ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= $movimento['nome'] . ' ' . $movimento['cognome'] ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info view-movimento" data-id="<?= $movimento['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($movimento['operazione'] == 'movimento' && $can_delete): ?>
                                        <button class="btn btn-sm btn-danger delete-movimento" data-id="<?= $movimento['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Apertura Cassa -->
<div class="modal fade" id="aperturaCassaModal" tabindex="-1" aria-labelledby="aperturaCassaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aperturaCassaModalLabel">Apertura Cassa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="aperturaCassaForm">
                    <div class="mb-3">
                        <label for="fondoCassa" class="form-label">Fondo Cassa Iniziale</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="fondoCassa" name="fondoCassa" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="noteApertura" class="form-label">Note</label>
                        <textarea class="form-control" id="noteApertura" name="noteApertura" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" id="confermaAperturaBtn">Conferma Apertura</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chiusura Cassa -->
<div class="modal fade" id="chiusuraCassaModal" tabindex="-1" aria-labelledby="chiusuraCassaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chiusuraCassaModalLabel">Chiusura Cassa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong>Saldo Attuale:</strong> € <?= number_format($cassa_info['saldo_attuale'], 2, ',', '.') ?>
                </div>
                <form id="chiusuraCassaForm">
                    <div class="mb-3">
                        <label for="saldoContato" class="form-label">Saldo Contato</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="saldoContato" name="saldoContato" step="0.01" min="0" required>
                        </div>
                        <div class="form-text" id="differenzaSaldo"></div>
                    </div>
                    <div class="mb-3">
                        <label for="noteChiusura" class="form-label">Note</label>
                        <textarea class="form-control" id="noteChiusura" name="noteChiusura" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confermaChiusuraBtn">Conferma Chiusura</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuova Entrata -->
<div class="modal fade" id="nuovaEntrataModal" tabindex="-1" aria-labelledby="nuovaEntrataModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuovaEntrataModalLabel">Registra Nuova Entrata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nuovaEntrataForm">
                    <div class="mb-3">
                        <label for="importoEntrata" class="form-label">Importo</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="importoEntrata" name="importoEntrata" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categoriaEntrata" class="form-label">Categoria</label>
                        <select class="form-select" id="categoriaEntrata" name="categoriaEntrata" required>
                            <option value="">Seleziona categoria</option>
                            <option value="vendita">Vendita</option>
                            <option value="ricarica">Ricarica</option>
                            <option value="abbonamento">Abbonamento</option>
                            <option value="rimborso">Rimborso</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descrizioneEntrata" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizioneEntrata" name="descrizioneEntrata" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="riferimentoEntrata" class="form-label">Riferimento (opzionale)</label>
                        <input type="text" class="form-control" id="riferimentoEntrata" name="riferimentoEntrata">
                        <div class="form-text">Es. numero fattura, nome cliente, ecc.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-success" id="confermaEntrataBtn">Registra Entrata</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nuova Uscita -->
<div class="modal fade" id="nuovaUscitaModal" tabindex="-1" aria-labelledby="nuovaUscitaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuovaUscitaModalLabel">Registra Nuova Uscita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="nuovaUscitaForm">
                    <div class="mb-3">
                        <label for="importoUscita" class="form-label">Importo</label>
                        <div class="input-group">
                            <span class="input-group-text">€</span>
                            <input type="number" class="form-control" id="importoUscita" name="importoUscita" step="0.01" min="0.01" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="categoriaUscita" class="form-label">Categoria</label>
                        <select class="form-select" id="categoriaUscita" name="categoriaUscita" required>
                            <option value="">Seleziona categoria</option>
                            <option value="fornitore">Fornitore</option>
                            <option value="spese">Spese Generali</option>
                            <option value="utenze">Utenze</option>
                            <option value="stipendi">Stipendi</option>
                            <option value="rimborso">Rimborso Cliente</option>
                            <option value="altro">Altro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descrizioneUscita" class="form-label">Descrizione</label>
                        <textarea class="form-control" id="descrizioneUscita" name="descrizioneUscita" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="riferimentoUscita" class="form-label">Riferimento (opzionale)</label>
                        <input type="text" class="form-control" id="riferimentoUscita" name="riferimentoUscita">
                        <div class="form-text">Es. numero fattura, nome fornitore, ecc.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confermaUscitaBtn">Registra Uscita</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Report Cassa -->
<div class="modal fade" id="reportCassaModal" tabindex="-1" aria-labelledby="reportCassaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportCassaModalLabel">Genera Report Cassa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reportCassaForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reportDataInizio" class="form-label">Data Inizio</label>
                            <input type="date" class="form-control" id="reportDataInizio" name="reportDataInizio" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reportDataFine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="reportDataFine" name="reportDataFine" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="reportTipo" class="form-label">Tipo Report</label>
                            <select class="form-select" id="reportTipo" name="reportTipo" required>
                                <option value="completo">Report Completo</option>
                                <option value="entrate">Solo Entrate</option>
                                <option value="uscite">Solo Uscite</option>
                                <option value="riepilogo">Riepilogo Giornaliero</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reportFormato" class="form-label">Formato</label>
                            <select class="form-select" id="reportFormato" name="reportFormato" required>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reportNote" class="form-label">Note Aggiuntive</label>
                        <textarea class="form-control" id="reportNote" name="reportNote" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="generaReportBtn">Genera Report</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Filtra Movimenti -->
<div class="modal fade" id="filtraMovimentiModal" tabindex="-1" aria-labelledby="filtraMovimentiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filtraMovimentiModalLabel">Filtra Movimenti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="filtraMovimentiForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filtroModalDataInizio" class="form-label">Data Inizio</label>
                            <input type="date" class="form-control" id="filtroModalDataInizio" name="filtroModalDataInizio">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filtroModalDataFine" class="form-label">Data Fine</label>
                            <input type="date" class="form-control" id="filtroModalDataFine" name="filtroModalDataFine">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filtroModalTipo" class="form-label">Tipo Movimento</label>
                            <select class="form-select" id="filtroModalTipo" name="filtroModalTipo">
                                <option value="">Tutti</option>
                                <option value="entrata">Entrate</option>
                                <option value="uscita">Uscite</option>
                                <option value="apertura">Aperture</option>
                                <option value="chiusura">Chiusure</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filtroModalCategoria" class="form-label">Categoria</label>
                            <select class="form-select" id="filtroModalCategoria" name="filtroModalCategoria">
                                <option value="">Tutte</option>
                                <option value="vendita">Vendita</option>
                                <option value="ricarica">Ricarica</option>
                                <option value="abbonamento">Abbonamento</option>
                                <option value="fornitore">Fornitore</option>
                                <option value="spese">Spese</option>
                                <option value="utenze">Utenze</option>
                                <option value="stipendi">Stipendi</option>
                                <option value="rimborso">Rimborso</option>
                                <option value="altro">Altro</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filtroModalImportoMin" class="form-label">Importo Minimo</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" class="form-control" id="filtroModalImportoMin" name="filtroModalImportoMin" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="filtroModalImportoMax" class="form-label">Importo Massimo</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" class="form-control" id="filtroModalImportoMax" name="filtroModalImportoMax" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="filtroModalRiferimento" class="form-label">Riferimento o Descrizione</label>
                        <input type="text" class="form-control" id="filtroModalRiferimento" name="filtroModalRiferimento">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-primary" id="applicaFiltriModalBtn">Applica Filtri</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Visualizza Movimento -->
<div class="modal fade" id="visualizzaMovimentoModal" tabindex="-1" aria-labelledby="visualizzaMovimentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="visualizzaMovimentoModalLabel">Dettaglio Movimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="dettaglioMovimentoContent">
                <!-- Contenuto caricato dinamicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="stampaMovimentoBtn">Stampa</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Conferma Eliminazione -->
<div class="modal fade" id="confermaEliminazioneModal" tabindex="-1" aria-labelledby="confermaEliminazioneModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confermaEliminazioneModalLabel">Conferma Eliminazione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler eliminare questo movimento? Questa azione non può essere annullata.</p>
                <input type="hidden" id="idMovimentoDaEliminare">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <button type="button" class="btn btn-danger" id="confermaEliminazioneBtn">Elimina</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inizializza DataTable
    const movimentiTable = new DataTable('#movimentiTable', {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/it-IT.json',
        },
        order: [[1, 'desc']], // Ordina per data (discendente)
        columnDefs: [
            { targets: [0], visible: false }, // Nascondi colonna ID
        ]
    });

    // Inizializza il grafico
    const ctx = document.getElementById('cassaChart').getContext('2d');
    const cassaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [
                {
                    label: 'Entrate',
                    data: <?= json_encode($entrate_data) ?>,
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1
                },
                {
                    label: 'Uscite',
                    data: <?= json_encode($uscite_data) ?>,
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '€ ' + value;
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': € ' + context.raw;
                        }
                    }
                }
            }
        }
    });

    // Gestione apertura cassa
    document.getElementById('confermaAperturaBtn').addEventListener('click', function() {
        const fondoCassa = document.getElementById('fondoCassa').value;
        const noteApertura = document.getElementById('noteApertura').value;
        
        if (!fondoCassa) {
            alert('Inserisci il fondo cassa iniziale');
            return;
        }
        
        // Invia i dati al server tramite AJAX
        fetch('ajax/cassa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=apertura&fondoCassa=' + fondoCassa + '&note=' + encodeURIComponent(noteApertura)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cassa aperta con successo!');
                location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante l\'apertura della cassa');
        });
    });

    // Gestione chiusura cassa
    const saldoAttuale = <?= $cassa_info['saldo_attuale'] ?>;
    document.getElementById('saldoContato').addEventListener('input', function() {
        const saldoContato = parseFloat(this.value) || 0;
        const differenza = saldoContato - saldoAttuale;
        const differenzaElement = document.getElementById('differenzaSaldo');
        
        if (differenza === 0) {
            differenzaElement.innerHTML = 'Il saldo contato corrisponde al saldo del sistema.';
            differenzaElement.className = 'form-text text-success';
        } else if (differenza > 0) {
            differenzaElement.innerHTML = 'Eccedenza di € ' + differenza.toFixed(2) + ' rispetto al saldo del sistema.';
            differenzaElement.className = 'form-text text-warning';
        } else {
            differenzaElement.innerHTML = 'Ammanco di € ' + Math.abs(differenza).toFixed(2) + ' rispetto al saldo del sistema.';
            differenzaElement.className = 'form-text text-danger';
        }
    });

    document.getElementById('confermaChiusuraBtn').addEventListener('click', function() {
        const saldoContato = document.getElementById('saldoContato').value;
        const noteChiusura = document.getElementById('noteChiusura').value;
        
        if (!saldoContato) {
            alert('Inserisci il saldo contato');
            return;
        }
        
        // Invia i dati al server tramite AJAX
        fetch('ajax/cassa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=chiusura&saldoContato=' + saldoContato + '&note=' + encodeURIComponent(noteChiusura)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cassa chiusa con successo!');
                location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante la chiusura della cassa');
        });
    });

    // Gestione nuova entrata
    document.getElementById('confermaEntrataBtn').addEventListener('click', function() {
        const importo = document.getElementById('importoEntrata').value;
        const categoria = document.getElementById('categoriaEntrata').value;
        const descrizione = document.getElementById('descrizioneEntrata').value;
        const riferimento = document.getElementById('riferimentoEntrata').value;
        
        if (!importo || !categoria || !descrizione) {
            alert('Compila tutti i campi obbligatori');
            return;
        }
        
        // Invia i dati al server tramite AJAX
        fetch('ajax/cassa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=nuovaEntrata&importo=' + importo + '&categoria=' + categoria + 
                  '&descrizione=' + encodeURIComponent(descrizione) + '&riferimento=' + encodeURIComponent(riferimento)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Entrata registrata con successo!');
                location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante la registrazione dell\'entrata');
        });
    });

    // Gestione nuova uscita
    document.getElementById('confermaUscitaBtn').addEventListener('click', function() {
        const importo = document.getElementById('importoUscita').value;
        const categoria = document.getElementById('categoriaUscita').value;
        const descrizione = document.getElementById('descrizioneUscita').value;
        const riferimento = document.getElementById('riferimentoUscita').value;
        
        if (!importo || !categoria || !descrizione) {
            alert('Compila tutti i campi obbligatori');
            return;
        }
        
        // Invia i dati al server tramite AJAX
        fetch('ajax/cassa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=nuovaUscita&importo=' + importo + '&categoria=' + categoria + 
                  '&descrizione=' + encodeURIComponent(descrizione) + '&riferimento=' + encodeURIComponent(riferimento)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Uscita registrata con successo!');
                location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante la registrazione dell\'uscita');
        });
    });

    // Gestione filtri
    document.getElementById('applicaFiltriBtn').addEventListener('click', function() {
        const dataInizio = document.getElementById('filtroDataInizio').value;
        const dataFine = document.getElementById('filtroDataFine').value;
        const tipo = document.getElementById('filtroTipo').value;
        const categoria = document.getElementById('filtroCategoria').value;
        
        // Applica i filtri alla tabella
        movimentiTable.search('').columns().search('').draw();
        
        if (dataInizio || dataFine) {
            // Implementa filtro per data (richiede plugin aggiuntivo per DataTables)
            // Per semplicità, qui ricarichiamo la pagina con i parametri di filtro
            const url = new URL(window.location.href);
            if (dataInizio) url.searchParams.set('data_inizio', dataInizio);
            if (dataFine) url.searchParams.set('data_fine', dataFine);
            if (tipo) url.searchParams.set('tipo', tipo);
            if (categoria) url.searchParams.set('categoria', categoria);
            window.location.href = url.toString();
        } else {
            // Filtri semplici
            if (tipo) movimentiTable.column(3).search(tipo).draw();
            if (categoria) movimentiTable.column(4).search(categoria).draw();
        }
    });

    // Gestione filtri modal
    document.getElementById('applicaFiltriModalBtn').addEventListener('click', function() {
        const dataInizio = document.getElementById('filtroModalDataInizio').value;
        const dataFine = document.getElementById('filtroModalDataFine').value;
        const tipo = document.getElementById('filtroModalTipo').value;
        const categoria = document.getElementById('filtroModalCategoria').value;
        const importoMin = document.getElementById('filtroModalImportoMin').value;
        const importoMax = document.getElementById('filtroModalImportoMax').value;
        const riferimento = document.getElementById('filtroModalRiferimento').value;
        
        // Chiudi il modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('filtraMovimentiModal'));
        modal.hide();
        
        // Applica i filtri (ricarica la pagina con i parametri)
        const url = new URL(window.location.href);
        if (dataInizio) url.searchParams.set('data_inizio', dataInizio);
        if (dataFine) url.searchParams.set('data_fine', dataFine);
        if (tipo) url.searchParams.set('tipo', tipo);
        if (categoria) url.searchParams.set('categoria', categoria);
        if (importoMin) url.searchParams.set('importo_min', importoMin);
        if (importoMax) url.searchParams.set('importo_max', importoMax);
        if (riferimento) url.searchParams.set('riferimento', riferimento);
        window.location.href = url.toString();
    });

    // Gestione report
    document.getElementById('generaReportBtn').addEventListener('click', function() {
        const dataInizio = document.getElementById('reportDataInizio').value;
        const dataFine = document.getElementById('reportDataFine').value;
        const tipoReport = document.getElementById('reportTipo').value;
        const formato = document.getElementById('reportFormato').value;
        const note = document.getElementById('reportNote').value;
        
        if (!dataInizio || !dataFine) {
            alert('Seleziona il periodo per il report');
            return;
        }
        
        // Genera il report (apri in una nuova finestra)
        const url = 'ajax/report_cassa.php?data_inizio=' + dataInizio + 
                    '&data_fine=' + dataFine + 
                    '&tipo=' + tipoReport + 
                    '&formato=' + formato + 
                    '&note=' + encodeURIComponent(note);
        
        window.open(url, '_blank');
        
        // Chiudi il modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('reportCassaModal'));
        modal.hide();
    });

    // Gestione esportazione
    document.getElementById('esportaMovimentiBtn').addEventListener('click', function() {
        // Recupera i filtri attualmente applicati
        const dataInizio = document.getElementById('filtroDataInizio').value;
        const dataFine = document.getElementById('filtroDataFine').value;
        const tipo = document.getElementById('filtroTipo').value;
        const categoria = document.getElementById('filtroCategoria').value;
        
        // Costruisci l'URL per l'esportazione
        let url = 'ajax/export.php?module=cassa';
        if (dataInizio) url += '&data_inizio=' + dataInizio;
        if (dataFine) url += '&data_fine=' + dataFine;
        if (tipo) url += '&tipo=' + tipo;
        if (categoria) url += '&categoria=' + categoria;
        
        // Apri in una nuova finestra
        window.open(url, '_blank');
    });

    // Gestione visualizzazione movimento
    document.querySelectorAll('.view-movimento').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            
            // Carica i dettagli del movimento
            fetch('ajax/cassa.php?action=getMovimento&id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Popola il modal con i dettagli
                    const movimento = data.movimento;
                    let html = `
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">${movimento.operazione === 'movimento' ? 
                                    (movimento.tipo === 'entrata' ? 'Entrata' : 'Uscita') : 
                                    (movimento.operazione === 'apertura' ? 'Apertura Cassa' : 'Chiusura Cassa')}
                                </h5>
                                <div class="mb-3">
                                    <strong>Data:</strong> ${movimento.data_formattata}
                                </div>
                                <div class="mb-3">
                                    <strong>Importo:</strong> € ${parseFloat(movimento.importo).toFixed(2)}
                                </div>
                                ${movimento.categoria ? `
                                <div class="mb-3">
                                    <strong>Categoria:</strong> ${movimento.categoria}
                                </div>` : ''}
                                <div class="mb-3">
                                    <strong>Descrizione:</strong> ${movimento.descrizione}
                                </div>
                                <div class="mb-3">
                                    <strong>Operatore:</strong> ${movimento.nome_operatore}
                                </div>
                                ${movimento.riferimento ? `
                                <div class="mb-3">
                                    <strong>Riferimento:</strong> ${movimento.riferimento}
                                </div>` : ''}
                            </div>
                        </div>
                    `;
                    
                    document.getElementById('dettaglioMovimentoContent').innerHTML = html;
                    
                    // Mostra il modal
                    const modal = new bootstrap.Modal(document.getElementById('visualizzaMovimentoModal'));
                    modal.show();
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore:', error);
                alert('Si è verificato un errore durante il caricamento dei dettagli');
            });
        });
    });

    // Gestione stampa movimento
    document.getElementById('stampaMovimentoBtn').addEventListener('click', function() {
        const contenuto = document.getElementById('dettaglioMovimentoContent').innerHTML;
        
        // Crea una finestra di stampa
        const stampaFinestra = window.open('', '_blank');
        stampaFinestra.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Dettaglio Movimento</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    body { padding: 20px; }
                    @media print {
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h3>Dettaglio Movimento di Cassa</h3>
                            <p>Data stampa: ${new Date().toLocaleString()}</p>
                        </div>
                    </div>
                    ${contenuto}
                    <div class="row mt-4 no-print">
                        <div class="col-12 text-center">
                            <button class="btn btn-primary" onclick="window.print()">Stampa</button>
                            <button class="btn btn-secondary" onclick="window.close()">Chiudi</button>
                        </div>
                    </div>
                </div>
            </body>
            </html>
        `);
        stampaFinestra.document.close();
    });

    // Gestione eliminazione movimento
    document.querySelectorAll('.delete-movimento').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('idMovimentoDaEliminare').value = id;
            
            // Mostra il modal di conferma
            const modal = new bootstrap.Modal(document.getElementById('confermaEliminazioneModal'));
            modal.show();
        });
    });

    document.getElementById('confermaEliminazioneBtn').addEventListener('click', function() {
        const id = document.getElementById('idMovimentoDaEliminare').value;
        
        // Invia la richiesta di eliminazione
        fetch('ajax/cassa.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=eliminaMovimento&id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Movimento eliminato con successo!');
                location.reload();
            } else {
                alert('Errore: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Si è verificato un errore durante l\'eliminazione del movimento');
        });
        
        // Chiudi il modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('confermaEliminazioneModal'));
        modal.hide();
    });

    // Imposta le date di default per i filtri
    const oggi = new Date();
    const primoDelMese = new Date(oggi.getFullYear(), oggi.getMonth(), 1);
    
    document.getElementById('filtroDataInizio').valueAsDate = primoDelMese;
    document.getElementById('filtroDataFine').valueAsDate = oggi;
    document.getElementById('reportDataInizio').valueAsDate = primoDelMese;
    document.getElementById('reportDataFine').valueAsDate = oggi;

    // Gestione del modal di filtro per tipo specifico
    document.querySelectorAll('[data-bs-target="#filtraMovimentiModal"]').forEach(link => {
        link.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            if (tipo) {
                document.getElementById('filtroModalTipo').value = tipo;
            }
        });
    });
});
</script>

