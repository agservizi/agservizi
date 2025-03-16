<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'];
$db = getDbConnection();

// Inizializza le variabili per i messaggi
$successMsg = '';
$errorMsg = '';

// Recupera le impostazioni attuali
function getSettings($db) {
    $stmt = $db->prepare("SELECT * FROM settings");
    $stmt->execute();
    $settings = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

// Aggiorna un'impostazione
function updateSetting($db, $key, $value) {
    $stmt = $db->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

// Gestisci il salvataggio delle impostazioni
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    try {
        $db->beginTransaction();
        
        // Impostazioni generali
        if (isset($_POST['company_name'])) {
            updateSetting($db, 'company_name', sanitizeInput($_POST['company_name']));
        }
        
        if (isset($_POST['address'])) {
            updateSetting($db, 'address', sanitizeInput($_POST['address']));
        }
        
        if (isset($_POST['phone'])) {
            updateSetting($db, 'phone', sanitizeInput($_POST['phone']));
        }
        
        if (isset($_POST['email'])) {
            updateSetting($db, 'email', sanitizeInput($_POST['email']));
        }
        
        if (isset($_POST['vat_number'])) {
            updateSetting($db, 'vat_number', sanitizeInput($_POST['vat_number']));
        }
        
        if (isset($_POST['tax_rate'])) {
            updateSetting($db, 'tax_rate', floatval($_POST['tax_rate']));
        }
        
        if (isset($_POST['currency'])) {
            updateSetting($db, 'currency', sanitizeInput($_POST['currency']));
        }
        
        // Impostazioni di stampa
        if (isset($_POST['receipt_header'])) {
            updateSetting($db, 'receipt_header', sanitizeInput($_POST['receipt_header']));
        }
        
        if (isset($_POST['receipt_footer'])) {
            updateSetting($db, 'receipt_footer', sanitizeInput($_POST['receipt_footer']));
        }
        
        if (isset($_POST['show_tax_on_receipt'])) {
            updateSetting($db, 'show_tax_on_receipt', 1);
        } else {
            updateSetting($db, 'show_tax_on_receipt', 0);
        }
        
        // Impostazioni di sistema
        if (isset($_POST['items_per_page'])) {
            updateSetting($db, 'items_per_page', intval($_POST['items_per_page']));
        }
        
        if (isset($_POST['low_stock_threshold'])) {
            updateSetting($db, 'low_stock_threshold', intval($_POST['low_stock_threshold']));
        }
        
        if (isset($_POST['date_format'])) {
            updateSetting($db, 'date_format', sanitizeInput($_POST['date_format']));
        }
        
        if (isset($_POST['time_format'])) {
            updateSetting($db, 'time_format', sanitizeInput($_POST['time_format']));
        }
        
        // Impostazioni del negozio
        if (isset($_POST['store_name'])) {
            updateSetting($db, 'store_name', sanitizeInput($_POST['store_name']));
        }
        
        if (isset($_POST['store_code'])) {
            updateSetting($db, 'store_code', sanitizeInput($_POST['store_code']));
        }
        
        // Logo del negozio
        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            if (!in_array($_FILES['store_logo']['type'], $allowedTypes)) {
                throw new Exception('Tipo di file non supportato. Utilizzare JPG, PNG o GIF.');
            }
            
            if ($_FILES['store_logo']['size'] > $maxSize) {
                throw new Exception('Il file è troppo grande. La dimensione massima è 2MB.');
            }
            
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = 'store_logo_' . time() . '_' . basename($_FILES['store_logo']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['store_logo']['tmp_name'], $targetPath)) {
                updateSetting($db, 'store_logo', $targetPath);
            } else {
                throw new Exception('Errore durante il caricamento del file.');
            }
        }
        
        $db->commit();
        $successMsg = 'Impostazioni salvate con successo!';
    } catch (Exception $e) {
        $db->rollBack();
        $errorMsg = 'Errore: ' . $e->getMessage();
    }
}

// Recupera le impostazioni attuali
$settings = getSettings($db);

// Imposta valori predefiniti se mancanti
$defaultSettings = [
    'company_name' => '',
    'address' => '',
    'phone' => '',
    'email' => '',
    'vat_number' => '',
    'tax_rate' => '22',
    'currency' => '€',
    'receipt_header' => '',
    'receipt_footer' => 'Grazie per il vostro acquisto!',
    'show_tax_on_receipt' => '1',
    'items_per_page' => '10',
    'low_stock_threshold' => '5',
    'date_format' => 'd/m/Y',
    'time_format' => 'H:i',
    'store_name' => '',
    'store_code' => '',
    'store_logo' => ''
];

foreach ($defaultSettings as $key => $value) {
    if (!isset($settings[$key])) {
        $settings[$key] = $value;
    }
}

// Chiudi la connessione al database
$db = null;
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impostazioni - Sistema POS</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include 'includes/sidebar.php'; ?>
            </div>
            
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4><i class="fas fa-cogs"></i> Impostazioni</h4>
                    </div>
                    
                    <div class="card-body">
                        <?php if (!empty($successMsg)): ?>
                            <div class="alert alert-success">
                                <?php echo $successMsg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($errorMsg)): ?>
                            <div class="alert alert-danger">
                                <?php echo $errorMsg; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="" enctype="multipart/form-data">
                            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">Generali</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="store-tab" data-toggle="tab" href="#store" role="tab">Negozio</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="receipt-tab" data-toggle="tab" href="#receipt" role="tab">Stampa</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">Sistema</a>
                                </li>
                            </ul>
                            
                            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="settingsTabContent">
                                <!-- Impostazioni Generali -->
                                <div class="tab-pane fade show active" id="general" role="tabpanel">
                                    <h5 class="mb-3">Informazioni Aziendali</h5>
                                    
                                    <div class="form-group row">
                                        <label for="company_name" class="col-sm-3 col-form-label">Nome Azienda</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($settings['company_name']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="address" class="col-sm-3 col-form-label">Indirizzo</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($settings['address']); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="phone" class="col-sm-3 col-form-label">Telefono</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($settings['phone']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="email" class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($settings['email']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="vat_number" class="col-sm-3 col-form-label">Partita IVA</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="vat_number" name="vat_number" value="<?php echo htmlspecialchars($settings['vat_number']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="tax_rate" class="col-sm-3 col-form-label">Aliquota IVA (%)</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($settings['tax_rate']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="currency" class="col-sm-3 col-form-label">Valuta</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="currency" name="currency" value="<?php echo htmlspecialchars($settings['currency']); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Impostazioni Negozio -->
                                <div class="tab-pane fade" id="store" role="tabpanel">
                                    <h5 class="mb-3">Informazioni Negozio</h5>
                                    
                                    <div class="form-group row">
                                        <label for="store_name" class="col-sm-3 col-form-label">Nome Negozio</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="store_name" name="store_name" value="<?php echo htmlspecialchars($settings['store_name']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="store_code" class="col-sm-3 col-form-label">Codice Negozio</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="store_code" name="store_code" value="<?php echo htmlspecialchars($settings['store_code']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="store_logo" class="col-sm-3 col-form-label">Logo Negozio</label>
                                        <div class="col-sm-9">
                                            <?php if (!empty($settings['store_logo'])): ?>
                                                <div class="mb-2">
                                                    <img src="<?php echo htmlspecialchars($settings['store_logo']); ?>" alt="Logo Negozio" class="img-thumbnail" style="max-height: 100px;">
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" class="form-control-file" id="store_logo" name="store_logo">
                                            <small class="form-text text-muted">Formati supportati: JPG, PNG, GIF. Dimensione massima: 2MB.</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Impostazioni Stampa -->
                                <div class="tab-pane fade" id="receipt" role="tabpanel">
                                    <h5 class="mb-3">Impostazioni Ricevuta</h5>
                                    
                                    <div class="form-group row">
                                        <label for="receipt_header" class="col-sm-3 col-form-label">Intestazione Ricevuta</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="receipt_header" name="receipt_header" rows="3"><?php echo htmlspecialchars($settings['receipt_header']); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="receipt_footer" class="col-sm-3 col-form-label">Piè di Pagina Ricevuta</label>
                                        <div class="col-sm-9">
                                            <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="3"><?php echo htmlspecialchars($settings['receipt_footer']); ?></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <div class="col-sm-3">Mostra IVA sulla Ricevuta</div>
                                        <div class="col-sm-9">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="show_tax_on_receipt" name="show_tax_on_receipt" <?php echo ($settings['show_tax_on_receipt'] == 1) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="show_tax_on_receipt">
                                                    Mostra dettagli IVA sulla ricevuta
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Impostazioni Sistema -->
                                <div class="tab-pane fade" id="system" role="tabpanel">
                                    <h5 class="mb-3">Impostazioni di Sistema</h5>
                                    
                                    <div class="form-group row">
                                        <label for="items_per_page" class="col-sm-3 col-form-label">Elementi per Pagina</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" id="items_per_page" name="items_per_page" min="5" max="100" value="<?php echo htmlspecialchars($settings['items_per_page']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="low_stock_threshold" class="col-sm-3 col-form-label">Soglia Scorte Basse</label>
                                        <div class="col-sm-9">
                                            <input type="number" class="form-control" id="low_stock_threshold" name="low_stock_threshold" min="0" value="<?php echo htmlspecialchars($settings['low_stock_threshold']); ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="date_format" class="col-sm-3 col-form-label">Formato Data</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="date_format" name="date_format">
                                                <option value="d/m/Y" <?php echo ($settings['date_format'] == 'd/m/Y') ? 'selected' : ''; ?>>DD/MM/YYYY (31/12/2023)</option>
                                                <option value="m/d/Y" <?php echo ($settings['date_format'] == 'm/d/Y') ? 'selected' : ''; ?>>MM/DD/YYYY (12/31/2023)</option>
                                                <option value="Y-m-d" <?php echo ($settings['date_format'] == 'Y-m-d') ? 'selected' : ''; ?>>YYYY-MM-DD (2023-12-31)</option>
                                                <option value="d.m.Y" <?php echo ($settings['date_format'] == 'd.m.Y') ? 'selected' : ''; ?>>DD.MM.YYYY (31.12.2023)</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="time_format" class="col-sm-3 col-form-label">Formato Ora</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="time_format" name="time_format">
                                                <option value="H:i" <?php echo ($settings['time_format'] == 'H:i') ? 'selected' : ''; ?>>24 ore (14:30)</option>
                                                <option value="h:i A" <?php echo ($settings['time_format'] == 'h:i A') ? 'selected' : ''; ?>>12 ore (02:30 PM)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4 text-center">
                                <button type="submit" name="save_settings" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva Impostazioni
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Gestione delle tab
            $('#settingsTabs a').on('click', function (e) {
                e.preventDefault();
                $(this).tab('show');
            });
            
            // Conferma prima di lasciare la pagina se ci sono modifiche non salvate
            let formChanged = false;
            
            $('form :input').on('change', function() {
                formChanged = true;
            });
            
            $(window).on('beforeunload', function() {
                if (formChanged) {
                    return 'Ci sono modifiche non salvate. Sei sicuro di voler lasciare la pagina?';
                }
            });
            
            $('form').on('submit', function() {
                formChanged = false;
            });
        });
    </script>
</body>
</html>

