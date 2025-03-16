<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verifica che l'utente sia autenticato
if (!isset($_SESSION['user_id'])) {
    die('Utente non autenticato');
}

// Controllo dei permessi
if (!in_array($_SESSION['user_role'], ['admin', 'manager', 'cassiere'])) {
    die('Permessi insufficienti');
}

// Recupera i parametri
$data_inizio = isset($_GET['data_inizio']) ? $_GET['data_inizio'] : date('Y-m-01');
$data_fine = isset($_GET['data_fine']) ? $_GET['data_fine'] : date('Y-m-d');
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'completo';
$formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
$note = isset($_GET['note']) ? $_GET['note'] : '';

// Connessione al database
$db = getDbConnection();

// Costruisci la query in base al tipo di report
$query = "SELECT mc.*, u.nome, u.cognome, 
          CONCAT(u.nome, ' ', u.cognome) AS nome_operatore,
          DATE_FORMAT(mc.data_operazione, '%d/%m/%Y %H:%i') AS data_formattata
          FROM movimenti_cassa mc
          LEFT JOIN utenti u ON mc.id_utente = u.id
          WHERE mc.data_operazione BETWEEN :data_inizio AND DATE_ADD(:data_fine, INTERVAL 1 DAY)";

if ($tipo === 'entrate') {
    $query .= " AND mc.tipo = 'entrata'";
} else if ($tipo === 'uscite') {
    $query .= " AND mc.tipo = 'uscita'";
}

$query .= " ORDER BY mc.data_operazione";

$stmt = $db->prepare($query);
$stmt->bindParam(':data_inizio', $data_inizio);
$stmt->bindParam(':data_fine', $data_fine);
$stmt->execute();
$movimenti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcola i totali
$totale_entrate = 0;
$totale_uscite = 0;

foreach ($movimenti as $movimento) {
    if ($movimento['tipo'] === 'entrata') {
        $totale_entrate += $movimento['importo'];
    } else if ($movimento['tipo'] === 'uscita') {
        $totale_uscite += $movimento['importo'];
    }
}

$saldo = $totale_entrate - $totale_uscite;

// Genera il report in base al formato richiesto
switch ($formato) {
    case 'pdf':
        generaPDF($movimenti, $data_inizio, $data_fine, $tipo, $totale_entrate, $totale_uscite, $saldo, $note);
        break;
    case 'excel':
        generaExcel($movimenti, $data_inizio, $data_fine, $tipo, $totale_entrate, $totale_uscite, $saldo, $note);
        break;
    case 'csv':
        generaCSV($movimenti, $data_inizio, $data_fine, $tipo);
        break;
    default:
        die('Formato non supportato');
}

// Funzione per generare il report in PDF
function generaPDF($movimenti, $data_inizio, $data_fine, $tipo, $totale_entrate, $totale_uscite, $saldo, $note) {
    // Qui utilizzerai una libreria per generare PDF come TCPDF o FPDF
    // Per semplicità, generiamo un HTML che può essere stampato come PDF dal browser
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <title>Report Cassa</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1 { text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .totale { font-weight: bold; }
            .entrata { color: green; }
            .uscita { color: red; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; }
            @media print {
                .no-print { display: none; }
                body { margin: 0; }
            }
        </style>
    </head>
    <body>
        <h1>Report Movimenti di Cassa</h1>
        <div>
            <p><strong>Periodo:</strong> ' . date('d/m/Y', strtotime($data_inizio)) . ' - ' . date('d/m/Y', strtotime($data_fine)) . '</p>
            <p><strong>Tipo Report:</strong> ' . ucfirst($tipo) . '</p>
            ' . ($note ? '<p><strong>Note:</strong> ' . htmlspecialchars($note) . '</p>' : '') . '
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Operazione</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Descrizione</th>
                    <th>Importo</th>
                    <th>Operatore</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($movimenti as $movimento) {
        $html .= '<tr>
                <td>' . $movimento['data_formattata'] . '</td>
                <td>' . ucfirst($movimento['operazione']) . '</td>
                <td>' . ($movimento['tipo'] ? ucfirst($movimento['tipo']) : '-') . '</td>
                <td>' . ($movimento['categoria'] ? ucfirst($movimento['categoria']) : '-') . '</td>
                <td>' . htmlspecialchars($movimento['descrizione']) . '</td>
                <td class="' . ($movimento['tipo'] === 'entrata' ? 'entrata' : ($movimento['tipo'] === 'uscita' ? 'uscita' : '')) . '">
                    ' . ($movimento['importo'] > 0 ? '€ ' . number_format($movimento['importo'], 2, ',', '.') : '-') . '
                </td>
                <td>' . $movimento['nome_operatore'] . '</td>
            </tr>';
    }
    
    $html .= '</tbody>
            <tfoot>
                <tr class="totale">
                    <td colspan="5" align="right">Totale Entrate:</td>
                    <td class="entrata">€ ' . number_format($totale_entrate, 2, ',', '.') . '</td>
                    <td></td>
                </tr>
                <tr class="totale">
                    <td colspan="5" align="right">Totale Uscite:</td>
                    <td class="uscita">€ ' . number_format($totale_uscite, 2, ',', '.') . '</td>
                    <td></td>
                </tr>
                <tr class="totale">
                    <td colspan="5" align="right">Saldo:</td>
                    <td class="' . ($saldo >= 0 ? 'entrata' : 'uscita') . '">€ ' . number_format($saldo, 2, ',', '.') . '</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="footer">
            <p>Report generato il ' . date('d/m/Y H:i') . ' da ' . $_SESSION['user_name'] . ' ' . $_SESSION['user_surname'] . '</p>
        </div>
        
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()">Stampa Report</button>
            <button onclick="window.close()">Chiudi</button>
        </div>
    </body>
    </html>';
    
    echo $html;
    exit;
}

// Funzione per generare il report in Excel
function generaExcel($movimenti, $data_inizio, $data_fine, $tipo, $totale_entrate, $totale_uscite, $saldo, $note) {
    // Imposta l'header per il download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="report_cassa_' . date('Ymd') . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Genera un file Excel semplice (in realtà è HTML che Excel può aprire)
    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
    <body>
        <table border="1">
            <caption>
                <h2>Report Movimenti di Cassa</h2>
                <p>Periodo: ' . date('d/m/Y', strtotime($data_inizio)) . ' - ' . date('d/m/Y', strtotime($data_fine)) . '</p>
                <p>Tipo Report: ' . ucfirst($tipo) . '</p>
                ' . ($note ? '<p>Note: ' . htmlspecialchars($note) . '</p>' : '') . '
            </caption>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Operazione</th>
                    <th>Tipo</th>
                    <th>Categoria</th>
                    <th>Descrizione</th>
                    <th>Importo</th>
                    <th>Operatore</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($movimenti as $movimento) {
        echo '<tr>
                <td>' . $movimento['data_formattata'] . '</td>
                <td>' . ucfirst($movimento['operazione']) . '</td>
                <td>' . ($movimento['tipo'] ? ucfirst($movimento['tipo']) : '-') . '</td>
                <td>' . ($movimento['categoria'] ? ucfirst($movimento['categoria']) : '-') . '</td>
                <td>' . htmlspecialchars($movimento['descrizione']) . '</td>
                <td>' . ($movimento['importo'] > 0 ? '€ ' . number_format($movimento['importo'], 2, ',', '.') : '-') . '</td>
                <td>' . $movimento['nome_operatore'] . '</td>
            </tr>';
    }
    
    echo '</tbody>
            <tfoot>
                <tr>
                    <td colspan="5" align="right"><strong>Totale Entrate:</strong></td>
                    <td><strong>€ ' . number_format($totale_entrate, 2, ',', '.') . '</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" align="right"><strong>Totale Uscite:</strong></td>
                    <td><strong>€ ' . number_format($totale_uscite, 2, ',', '.') . '</strong></td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" align="right"><strong>Saldo:</strong></td>
                    <td><strong>€ ' . number_format($saldo, 2, ',', '.') . '</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        <p>Report generato il ' . date('d/m/Y H:i') . ' da ' . $_SESSION['user_name'] . ' ' . $_SESSION['user_surname'] . '</p>
    </body>
    </html>';
    exit;
}

// Funzione per generare il report in CSV
function generaCSV($movimenti, $data_inizio, $data_fine, $tipo) {
    // Imposta l'header per il download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="report_cassa_' . date('Ymd') . '.csv"');
    
    // Crea un file handle per l'output
    $output = fopen('php://output', 'w');
    
    // Aggiungi BOM per supportare caratteri UTF-8 in Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Intestazione CSV
    fputcsv($output, ['Data', 'Operazione', 'Tipo', 'Categoria', 'Descrizione', 'Importo', 'Operatore']);
    
    // Dati
    foreach ($movimenti as $movimento) {
        fputcsv($output, [
            $movimento['data_formattata'],
            ucfirst($movimento['operazione']),
            $movimento['tipo'] ? ucfirst($movimento['tipo']) : '-',
            $movimento['categoria'] ? ucfirst($movimento['categoria']) : '-',
            $movimento['descrizione'],
            $movimento['importo'] > 0 ? '€ ' . number_format($movimento['importo'], 2, ',', '.') : '-',
            $movimento['nome_operatore']
        ]);
    }
    
    fclose($output);
    exit;
}

// Chiudi la connessione
$db = null;
?>

