<?php
// Inizializza la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Includi i file necessari
require_once 'config/database.php';
require_once 'includes/functions.php';

// Connessione al database
$conn = connectDB();

// Inizializza le variabili per i messaggi
$success_message = '';
$error_message = '';

// Funzione per recuperare le impostazioni dal database
function getSettings($conn) {
    $settings = [];
    $query = "SELECT * FROM settings";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

// Funzione per aggiornare un'impostazione
function updateSetting($conn, $key, $value) {
    $key = mysqli_real_escape_string($conn, $key);
    $value = mysqli_real_escape_string($conn, $value);
    
    $query = "INSERT INTO settings (setting_key, setting_value) 
              VALUES ('$key', '$value') 
              ON DUPLICATE KEY UPDATE setting_value = '$value'";
    
    return mysqli_query($conn, $query);
}

// Funzione per caricare il logo
function uploadLogo($file) {
    $target_dir = "uploads/";
    
    // Crea la directory se non esiste
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . "logo.png";
    $upload_ok = 1;
    $image_file_type = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // Controlla se il file è un'immagine
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return "Il file non è un'immagine.";
    }
    
    // Controlla la dimensione del file (max 2MB)
    if ($file["size"] > 2000000) {
        return "Il file è troppo grande. Dimensione massima: 2MB.";
    }
    
    // Permetti solo alcuni formati di file
    if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif") {
        return "Sono permessi solo file JPG, JPEG, PNG e GIF.";
    }
    
    // Prova a caricare il file
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return true;
    } else {
        return "Si è verificato un errore durante il caricamento del file.";
    }
}

// Recupera le impostazioni attuali
$settings = getSettings($conn);

// Gestisci l'invio del form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // Inizia una transazione
    mysqli_begin_transaction($conn);
    
    try {
        // Impostazioni generali
        updateSetting($conn, 'company_name', sanitizeInput($_POST['company_name'] ?? ''));
        updateSetting($conn, 'company_address', sanitizeInput($_POST['company_address'] ?? ''));
        updateSetting($conn, 'company_city', sanitizeInput($_POST['company_city'] ?? ''));
        updateSetting($conn, 'company_zip', sanitizeInput($_POST['company_zip'] ?? ''));
        updateSetting($conn, 'company_province', sanitizeInput($_POST['company_province'] ?? ''));
        updateSetting($conn, 'company_country', sanitizeInput($_POST['company_country'] ?? ''));
        updateSetting($conn, 'company_vat', sanitizeInput($_POST['company_vat'] ?? ''));
        updateSetting($conn, 'company_tax_code', sanitizeInput($_POST['company_tax_code'] ?? ''));
        updateSetting($conn, 'company_phone', sanitizeInput($_POST['company_phone'] ?? ''));
        updateSetting($conn, 'company_email', sanitizeInput($_POST['company_email'] ?? ''));
        updateSetting($conn, 'company_website', sanitizeInput($_POST['company_website'] ?? ''));
        
        // Impostazioni negozio
        updateSetting($conn, 'store_name', sanitizeInput($_POST['store_name'] ?? ''));
        updateSetting($conn, 'store_code', sanitizeInput($_POST['store_code'] ?? ''));
        
        // Carica il logo se è stato fornito
        if (isset($_FILES['store_logo']) && $_FILES['store_logo']['size'] > 0) {
            $logo_result = uploadLogo($_FILES['store_logo']);
            if ($logo_result === true) {
                updateSetting($conn, 'store_logo', 'uploads/logo.png');
            } else {
                throw new Exception($logo_result);
            }
        }
        
        // Impostazioni ricevuta
        updateSetting($conn, 'receipt_header', sanitizeInput($_POST['receipt_header'] ?? ''));
        updateSetting($conn, 'receipt_footer', sanitizeInput($_POST['receipt_footer'] ?? ''));
        updateSetting($conn, 'receipt_show_vat', isset($_POST['receipt_show_vat']) ? '1' : '0');
        updateSetting($conn, 'receipt_show_tax_code', isset($_POST['receipt_show_tax_code']) ? '1' : '0');
        updateSetting($conn, 'receipt_show_store_info', isset($_POST['receipt_show_store_info']) ? '1' : '0');
        
        // Impostazioni sistema
        updateSetting($conn, 'items_per_page', sanitizeInput($_POST['items_per_page'] ?? '10'));
        updateSetting($conn, 'date_format', sanitizeInput($_POST['date_format'] ?? 'd/m/Y'));
        updateSetting($conn, 'time_format', sanitizeInput($_POST['time_format'] ?? 'H:i'));
        updateSetting($conn, 'currency_symbol', sanitizeInput($_POST['currency_symbol'] ?? '€'));
        updateSetting($conn, 'currency_code', sanitizeInput($_POST['currency_code'] ?? 'EUR'));
        updateSetting($conn, 'decimal_separator', sanitizeInput($_POST['decimal_separator'] ?? ','));
        updateSetting($conn, 'thousands_separator', sanitizeInput($_POST['thousands_separator'] ?? '.'));
        
        // Commit della transazione
        mysqli_commit($conn);
        
        // Aggiorna le impostazioni in memoria
        $settings = getSettings($conn);
        
        $success_message = "Impostazioni salvate con successo!";
    } catch (Exception $e) {
        // Rollback della transazione in caso di errore
        mysqli_rollback($conn);
        $error_message = "Errore: " . $e->getMessage();
    }
}

// Titolo della pagina
$page_title = "Impostazioni";
include 'includes/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Impostazioni</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Impostazioni</li>
    </ol>
    
    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-cogs me-1"></i>
            Gestione Impostazioni
        </div>
        <div class="card-body">
            <form id="settings-form" method="post" enctype="multipart/form-data">
                <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">Generali</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="store-tab" data-bs-toggle="tab" data-bs-target="#store" type="button" role="tab" aria-controls="store" aria-selected="false">Negozio</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="receipt-tab" data-bs-toggle="tab" data-bs-target="#receipt" type="button" role="tab" aria-controls="receipt" aria-selected="false">Ricevuta</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab" aria-controls="system" aria-selected="false">Sistema</button>
                    </li>
                </ul>
                
                <div class="tab-content p-4" id="settingsTabsContent">
                    <!-- Tab Impostazioni Generali -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                        <h4 class="mb-3">Informazioni Aziendali</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Nome Azienda</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="company_vat" class="form-label">Partita IVA</label>
                                <input type="text" class="form-control" id="company_vat" name="company_vat" value="<?php echo htmlspecialchars($settings['company_vat'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_tax_code" class="form-label">Codice Fiscale</label>
                                <input type="text" class="form-control" id="company_tax_code" name="company_tax_code" value="<?php echo htmlspecialchars($settings['company_tax_code'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="company_phone" class="form-label">Telefono</label>
                                <input type="text" class="form-control" id="company_phone" name="company_phone" value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="company_email" name="company_email" value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="company_website" class="form-label">Sito Web</label>
                                <input type="url" class="form-control" id="company_website" name="company_website" value="<?php echo htmlspecialchars($settings['company_website'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Indirizzo</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="company_address" class="form-label">Indirizzo</label>
                                <input type="text" class="form-control" id="company_address" name="company_address" value="<?php echo htmlspecialchars($settings['company_address'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="company_city" class="form-label">Città</label>
                                <input type="text" class="form-control" id="company_city" name="company_city" value="<?php echo htmlspecialchars($settings['company_city'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="company_zip" class="form-label">CAP</label>
                                <input type="text" class="form-control" id="company_zip" name="company_zip" value="<?php echo htmlspecialchars($settings['company_zip'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="company_province" class="form-label">Provincia</label>
                                <input type="text" class="form-control" id="company_province" name="company_province" value="<?php echo htmlspecialchars($settings['company_province'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_country" class="form-label">Paese</label>
                                <input type="text" class="form-control" id="company_country" name="company_country" value="<?php echo htmlspecialchars($settings['company_country'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Impostazioni Negozio -->
                    <div class="tab-pane fade" id="store" role="tabpanel" aria-labelledby="store-tab">
                        <h4 class="mb-3">Informazioni Negozio</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="store_name" class="form-label">Nome Negozio</label>
                                <input type="text" class="form-control" id="store_name" name="store_name" value="<?php echo htmlspecialchars($settings['store_name'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="store_code" class="form-label">Codice Negozio</label>
                                <input type="text" class="form-control" id="store_code" name="store_code" value="<?php echo htmlspecialchars($settings['store_code'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="store_logo" class="form-label">Logo Negozio</label>
                                <input type="file" class="form-control" id="store_logo" name="store_logo" accept="image/*">
                                <div class="form-text">Formati supportati: JPG, JPEG, PNG, GIF. Dimensione massima: 2MB.</div>
                            </div>
                            <div class="col-md-6">
                                <?php if (!empty($settings['store_logo']) && file_exists($settings['store_logo'])): ?>
                                    <label class="form-label">Logo Attuale</label>
                                    <div>
                                        <img src="<?php echo htmlspecialchars($settings['store_logo']); ?>" alt="Logo Negozio" class="img-thumbnail" style="max-height: 100px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Impostazioni Ricevuta -->
                    <div class="tab-pane fade" id="receipt" role="tabpanel" aria-labelledby="receipt-tab">
                        <h4 class="mb-3">Personalizzazione Ricevuta</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="receipt_header" class="form-label">Intestazione Ricevuta</label>
                                <textarea class="form-control" id="receipt_header" name="receipt_header" rows="3"><?php echo htmlspecialchars($settings['receipt_header'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="receipt_footer" class="form-label">Piè di Pagina Ricevuta</label>
                                <textarea class="form-control" id="receipt_footer" name="receipt_footer" rows="3"><?php echo htmlspecialchars($settings['receipt_footer'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="receipt_show_vat" name="receipt_show_vat" <?php echo (isset($settings['receipt_show_vat']) && $settings['receipt_show_vat'] == '1') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="receipt_show_vat">Mostra Partita IVA</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="receipt_show_tax_code" name="receipt_show_tax_code" <?php echo (isset($settings['receipt_show_tax_code']) && $settings['receipt_show_tax_code'] == '1') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="receipt_show_tax_code">Mostra Codice Fiscale</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="receipt_show_store_info" name="receipt_show_store_info" <?php echo (isset($settings['receipt_show_store_info']) && $settings['receipt_show_store_info'] == '1') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="receipt_show_store_info">Mostra Info Negozio</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Impostazioni Sistema -->
                    <div class="tab-pane fade" id="system" role="tabpanel" aria-labelledby="system-tab">
                        <h4 class="mb-3">Impostazioni di Sistema</h4>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="items_per_page" class="form-label">Elementi per Pagina</label>
                                <input type="number" class="form-control" id="items_per_page" name="items_per_page" min="5" max="100" value="<?php echo htmlspecialchars($settings['items_per_page'] ?? '10'); ?>">
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Formato Data e Ora</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="date_format" class="form-label">Formato Data</label>
                                <select class="form-select" id="date_format" name="date_format">
                                    <option value="d/m/Y" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'd/m/Y') ? 'selected' : ''; ?>>DD/MM/YYYY (31/12/2023)</option>
                                    <option value="m/d/Y" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'm/d/Y') ? 'selected' : ''; ?>>MM/DD/YYYY (12/31/2023)</option>
                                    <option value="Y-m-d" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'Y-m-d') ? 'selected' : ''; ?>>YYYY-MM-DD (2023-12-31)</option>
                                    <option value="d.m.Y" <?php echo (isset($settings['date_format']) && $settings['date_format'] == 'd.m.Y') ? 'selected' : ''; ?>>DD.MM.YYYY (31.12.2023)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="time_format" class="form-label">Formato Ora</label>
                                <select class="form-select" id="time_format" name="time_format">
                                    <option value="H:i" <?php echo (isset($settings['time_format']) && $settings['time_format'] == 'H:i') ? 'selected' : ''; ?>>24 ore (14:30)</option>
                                    <option value="h:i A" <?php echo (isset($settings['time_format']) && $settings['time_format'] == 'h:i A') ? 'selected' : ''; ?>>12 ore (02:30 PM)</option>
                                </select>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3">Impostazioni Valuta</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="currency_symbol" class="form-label">Simbolo Valuta</label>
                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" value="<?php echo htmlspecialchars($settings['currency_symbol'] ?? '€'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="currency_code" class="form-label">Codice Valuta</label>
                                <input type="text" class="form-control" id="currency_code" name="currency_code" value="<?php echo htmlspecialchars($settings['currency_code'] ?? 'EUR'); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="decimal_separator" class="form-label">Separatore Decimale</label>
                                <input type="text" class="form-control" id="decimal_separator" name="decimal_separator" maxlength="1" value="<?php echo htmlspecialchars($settings['decimal_separator'] ?? ','); ?>">
                            </div>
                            <div class="col-md-3">
                                <label for="thousands_separator" class="form-label">Separatore Migliaia</label>
                                <input type="text" class="form-control" id="thousands_separator" name="thousands_separator" maxlength="1" value="<?php echo htmlspecialchars($settings['thousands_separator'] ?? '.'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" name="save_settings" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Salva Impostazioni
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Avviso quando si tenta di lasciare la pagina con modifiche non salvate
let formChanged = false;

document.getElementById('settings-form').addEventListener('change', function() {
    formChanged = true;
});

document.getElementById('settings-form').addEventListener('submit', function() {
    formChanged = false;
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'Hai modifiche non salvate. Sei sicuro di voler lasciare la pagina?';
    }
});

// Anteprima del logo caricato
document.getElementById('store_logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewContainer = document.querySelector('#store .col-md-6:last-child');
            previewContainer.innerHTML = `
                <label class="form-label">Anteprima Logo</label>
                <div>
                    <img src="${e.target.result}" alt="Anteprima Logo" class="img-thumbnail" style="max-height: 100px;">
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php
// Includi il footer
include 'includes/footer.php';

// Chiudi la connessione al database
mysqli_close($conn);
?>

