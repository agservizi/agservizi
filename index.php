<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Inizializza la sessione
session_start();

// Verifica se l'utente è autenticato
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';
require_once 'includes/functions.php';

// Reindirizza alla dashboard se non è specificata una pagina
if (!isset($_GET['module'])) {
   header('Location: index.php?module=dashboard');
   exit;
}

$module = $_GET['module'];
$allowed_modules = [
   'dashboard', 'pos', 'listini', 'compensi', 'target', 
   'fatturazione', 'cassa', 'imei', 'personale', 'messaggi', 'assistenza'
];

if (!in_array($module, $allowed_modules)) {
   $module = 'dashboard';
}

// Ottieni le informazioni dell'utente corrente
$user_id = $_SESSION['user_id'];
$user_nome = $_SESSION['nome'];
$user_cognome = $_SESSION['cognome'];
$user_ruolo = $_SESSION['ruolo'];
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreSuite - Sistema POS Telefonia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar per la navigazione -->
            <div class="col-md-2 sidebar bg-dark text-white p-0">
                <?php include 'includes/sidebar.php'; ?>
            </div>
            
            <!-- Contenuto principale -->
            <div class="col-md-10 main-content">
                <header class="d-flex justify-content-between align-items-center p-3 border-bottom">
                    <div class="logo d-flex align-items-center">
                        <img src="assets/img/logo.svg" alt="CoreSuite" height="40">
                        <h4 class="ms-2 mb-0">CoreSuite</h4>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="me-3"><i class="bi bi-clock"></i> <?php echo date('d/m/Y H:i'); ?></span>
                        <div class="fiscal-status me-3 <?php echo checkFiscalPrinterStatus() ? 'text-success' : 'text-danger'; ?>">
                            <i class="bi bi-printer"></i>
                            <?php echo checkFiscalPrinterStatus() ? 'Stampante fiscale attiva' : 'Stampante fiscale non abilitata'; ?>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($user_nome . ' ' . $user_cognome); ?> (<?php echo htmlspecialchars(ucfirst($user_ruolo)); ?>)
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="index.php?module=personale&action=profile"><i class="bi bi-person"></i> Profilo</a></li>
                                <li><a class="dropdown-item" href="index.php?module=dashboard&action=settings"><i class="bi bi-gear"></i> Impostazioni</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </header>
                
                <div class="content p-3">
                    <?php include "modules/{$module}.php"; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>

