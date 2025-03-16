<?php
session_start();
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verifica che l'utente sia autenticato
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Controllo dei permessi
if (!in_array($_SESSION['user_role'], ['admin', 'manager', 'cassiere'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Permessi insufficienti']);
    exit;
}

$db = getDbConnection();

// Gestione delle richieste AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    switch ($action) {
        case 'apertura':
            // Apertura cassa
            $fondoCassa = isset($_POST['fondoCassa']) ? floatval($_POST['fondoCassa']) : 0;
            $note = isset($_POST['note']) ? $_POST['note'] : '';
            
            // Verifica se la cassa è già aperta
            $stmt = $db->prepare("SELECT 
                                    MAX(CASE WHEN operazione = 'apertura' THEN data_operazione ELSE NULL END) AS ultima_apertura,
                                    MAX(CASE WHEN operazione = 'chiusura' THEN data_operazione ELSE NULL END) AS ultima_chiusura
                                  FROM movimenti_cassa");
            $stmt->execute();
            $cassa_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $cassa_aperta = ($cassa_info['ultima_apertura'] > $cassa_info['ultima_chiusura'] || 
                            ($cassa_info['ultima_apertura'] && !$cassa_info['ultima_chiusura']));
            
            if ($cassa_aperta) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'La cassa è già aperta']);
                exit;
            }
            
            // Inserisci il movimento di apertura
            $stmt = $db->prepare("INSERT INTO movimenti_cassa 
                                  (operazione, tipo, importo, descrizione, id_utente, data_operazione) 
                                  VALUES ('apertura', 'entrata', :importo, :descrizione, :id_utente, NOW())");
            $stmt->bindParam(':importo', $fondoCassa);
            $descrizione = "Apertura cassa con fondo iniziale" . ($note ? " - " . $note : "");
            $stmt->bindParam(':descrizione', $descrizione);
            $stmt->bindParam(':id_utente', $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                // Registra l'evento nel log
                logAction($_SESSION['user_id'], 'apertura_cassa', 'Apertura cassa con fondo di € ' . $fondoCassa);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Cassa aperta con successo']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'apertura della cassa']);
            }
            break;
            
        case 'chiusura':
            // Chiusura cassa
            $saldoContato = isset($_POST['saldoContato']) ? floatval($_POST['saldoContato']) : 0;
            $note = isset($_POST['noteChiusura']) ? $_POST['noteChiusura'] : '';
            
            // Verifica se la cassa è aperta
            $stmt = $db->prepare("SELECT 
                                    MAX(CASE WHEN operazione = 'apertura' THEN data_operazione ELSE NULL END) AS ultima_apertura,
                                    MAX(CASE WHEN operazione = 'chiusura' THEN data_operazione ELSE NULL END) AS ultima_chiusura,
                                    COALESCE(SUM(CASE WHEN tipo = 'entrata' THEN importo ELSE 0 END), 0) - 
                                    COALESCE(SUM(CASE WHEN tipo = 'uscita' THEN importo ELSE 0 END), 0) AS saldo_attuale
                                  FROM movimenti_cassa");
            $stmt->execute();
            $cassa_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $cassa_aperta = ($cassa_info['ultima_apertura'] > $cassa_info['ultima_chiusura'] || 
                            ($cassa_info['ultima_apertura'] && !$cassa_info['ultima_chiusura']));
            
            if (!$cassa_aperta) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'La cassa è già chiusa']);
                exit;
            }
            
            // Calcola la differenza tra saldo contato e saldo del sistema
            $differenza = $saldoContato - $cassa_info['saldo_attuale'];
            
            // Inserisci il movimento di chiusura
            $stmt = $db->prepare("INSERT INTO movimenti_cassa 
                                  (operazione, tipo, importo, descrizione, id_utente, data_operazione) 
                                  VALUES ('chiusura', :tipo, :importo, :descrizione, :id_utente, NOW())");
            
            // Se c'è una differenza, registrala come movimento
            if ($differenza != 0) {
                $tipo = $differenza > 0 ? 'entrata' : 'uscita';
                $importo = abs($differenza);
                $descrizioneDifferenza = $differenza > 0 ? "Eccedenza di cassa" : "Ammanco di cassa";
                $stmt->bindParam(':tipo', $tipo);
                $stmt->bindParam(':importo', $importo);
            } else {
                $stmt->bindValue(':tipo', NULL);
                $stmt->bindValue(':importo', 0);
                $descrizioneDifferenza = "Saldo corrispondente al sistema";
            }
            
            $descrizione = "Chiusura cassa - Saldo contato: € " . number_format($saldoContato, 2, '.', '') . 
                           " - " . $descrizioneDifferenza . ($note ? " - " . $note : "");
            $stmt->bindParam(':descrizione', $descrizione);
            $stmt->bindParam(':id_utente', $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                // Registra l'evento nel log
                logAction($_SESSION['user_id'], 'chiusura_cassa', 'Chiusura cassa con saldo di € ' . $saldoContato . 
                          ' (' . $descrizioneDifferenza . ')');
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Cassa chiusa con successo']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Errore durante la chiusura della cassa']);
            }
            break;
            
        case 'nuovaEntrata':
            // Registrazione nuova entrata
            $importo = isset($_POST['importo']) ? floatval($_POST['importo']) : 0;
            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $descrizione = isset($_POST['descrizione']) ? $_POST['descrizione'] : '';
            $riferimento = isset($_POST['riferimento']) ? $_POST['riferimento'] : '';
            
            // Verifica se la cassa è aperta
            $stmt = $db->prepare("SELECT 
                                    MAX(CASE WHEN operazione = 'apertura' THEN data_operazione ELSE NULL END) AS ultima_apertura,
                                    MAX(CASE WHEN operazione = 'chiusura' THEN data_operazione ELSE NULL END) AS ultima_chiusura
                                  FROM movimenti_cassa");
            $stmt->execute();
            $cassa_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $cassa_aperta = ($cassa_info['ultima_apertura'] > $cassa_info['ultima_chiusura'] || 
                            ($cassa_info['ultima_apertura'] && !$cassa_info['ultima_chiusura']));
            
            if (!$cassa_aperta) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Impossibile registrare l\'entrata: la cassa è chiusa']);
                exit;
            }
            
            // Inserisci il movimento
            $stmt = $db->prepare("INSERT INTO movimenti_cassa 
                                  (operazione, tipo, categoria, importo, descrizione, riferimento, id_utente, data_operazione) 
                                  VALUES ('movimento', 'entrata', :categoria, :importo, :descrizione, :riferimento, :id_utente, NOW())");
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':importo', $importo);
            $stmt->bindParam(':descrizione', $descrizione);
            $stmt->bindParam(':riferimento', $riferimento);
            $stmt->bindParam(':id_utente', $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                // Registra l'evento nel log
                logAction($_SESSION['user_id'], 'nuova_entrata', 'Registrata entrata di € ' . $importo . ' - ' . $descrizione);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Entrata registrata con successo']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Errore durante la registrazione dell\'entrata']);
            }
            break;
            
        case 'nuovaUscita':
            // Registrazione nuova uscita
            $importo = isset($_POST['importoUscita']) ? floatval($_POST['importoUscita']) : 0;
            $categoria = isset($_POST['categoriaUscita']) ? $_POST['categoriaUscita'] : '';
            $descrizione = isset($_POST['descrizioneUscita']) ? $_POST['descrizioneUscita'] : '';
            $riferimento = isset($_POST['riferimentoUscita']) ? $_POST['riferimentoUscita'] : '';
            
            // Verifica se la cassa è aperta
            $stmt = $db->prepare("SELECT 
                                    MAX(CASE WHEN operazione = 'apertura' THEN data_operazione ELSE NULL END) AS ultima_apertura,
                                    MAX(CASE WHEN operazione = 'chiusura' THEN data_operazione ELSE NULL END) AS ultima_chiusura,
                                    COALESCE(SUM(CASE WHEN tipo = 'entrata' THEN importo ELSE 0 END), 0) - 
                                    COALESCE(SUM(CASE WHEN tipo = 'uscita' THEN importo ELSE 0 END), 0) AS saldo_attuale
                                  FROM movimenti_cassa");
            $stmt->execute();
            $cassa_info = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $cassa_aperta = ($cassa_info['ultima_apertura'] > $cassa_info['ultima_chiusura'] || 
                            ($cassa_info['ultima_apertura'] && !$cassa_info['ultima_chiusura']));
            
            if (!$cassa_aperta) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Impossibile registrare l\'uscita: la cassa è chiusa']);
                exit;
            }
            
            // Verifica se c'è abbastanza saldo
            if ($importo > $cassa_info['saldo_attuale']) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Saldo insufficiente per questa operazione']);
                exit;
            }
            
            // Inserisci il movimento
            $stmt = $db->prepare("INSERT INTO movimenti_cassa 
                                  (operazione, tipo, categoria, importo, descrizione, riferimento, id_utente, data_operazione) 
                                  VALUES ('movimento', 'uscita', :categoria, :importo, :descrizione, :riferimento, :id_utente, NOW())");
            $stmt->bindParam(':categoria', $categoria);
            $stmt->bindParam(':importo', $importo);
            $stmt->bindParam(':descrizione', $descrizione);
            $stmt->bindParam(':riferimento', $riferimento);
            $stmt->bindParam(':id_utente', $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                // Registra l'evento nel log
                logAction($_SESSION['user_id'], 'nuova_uscita', 'Registrata uscita di € ' . $importo . ' - ' . $descrizione);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Uscita registrata con successo']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Errore durante la registrazione dell\'uscita']);
            }
            break;
            
        case 'eliminaMovimento':
            // Eliminazione movimento (solo per admin)
            if ($_SESSION['user_role'] !== 'admin') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Permessi insufficienti']);
                exit;
            }
            
            $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
            
            // Recupera i dettagli del movimento prima di eliminarlo
            $stmt = $db->prepare("SELECT * FROM movimenti_cassa WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $movimento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$movimento) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Movimento non trovato']);
                exit;
            }
            
            // Verifica che non sia un'operazione di apertura o chiusura
            if ($movimento['operazione'] !== 'movimento') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Non è possibile eliminare operazioni di apertura o chiusura']);
                exit;
            }
            
            // Elimina il movimento
            $stmt = $db->prepare("DELETE FROM movimenti_cassa WHERE id = :id");
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                // Registra l'evento nel log
                logAction($_SESSION['user_id'], 'elimina_movimento', 'Eliminato movimento ID ' . $id . ' - ' . 
                          $movimento['tipo'] . ' di € ' . $movimento['importo']);
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Movimento eliminato con successo']);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Errore durante l\'eliminazione del movimento']);
            }
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch ($action) {
        case 'getMovimento':
            // Recupera i dettagli di un movimento
            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            
            $stmt = $db->prepare("SELECT mc.*, u.nome, u.cognome, 
                                  CONCAT(u.nome, ' ', u.cognome) AS nome_operatore,
                                  DATE_FORMAT(mc.data_operazione, '%d/%m/%Y %H:%i') AS data_formattata
                                  FROM movimenti_cassa mc
                                  LEFT JOIN utenti u ON mc.id_utente = u.id
                                  WHERE mc.id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $movimento = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($movimento) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'movimento' => $movimento]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Movimento non trovato']);
            }
            break;
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Azione non valida']);
            break;
    }
}

// Chiudi la connessione
$db = null;
?>

