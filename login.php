<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Inizializza la sessione
session_start();

// Se l'utente è già loggato, reindirizza alla dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: index.php?module=dashboard');
    exit;
}

// Includi la configurazione del database
require_once 'config/database.php';

$error = '';

// Gestione del form di login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Verifica se le credenziali sono admin/admin (per accesso rapido)
    if ($username === 'admin' && $password === 'admin') {
        // Imposta le variabili di sessione
        $_SESSION['user_id'] = 1;
        $_SESSION['username'] = 'admin';
        $_SESSION['nome'] = 'Amministratore';
        $_SESSION['cognome'] = 'Sistema';
        $_SESSION['ruolo'] = 'amministratore';
        
        // Reindirizza alla dashboard
        header('Location: index.php?module=dashboard');
        exit;
    } else {
        // Verifica nel database
        try {
            $stmt = $pdo->prepare("SELECT * FROM operatori WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Aggiorna l'ultimo accesso
                $updateStmt = $pdo->prepare("UPDATE operatori SET ultimo_accesso = NOW() WHERE id = ?");
                $updateStmt->execute([$user['id']]);
                
                // Imposta le variabili di sessione
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nome'] = $user['nome'];
                $_SESSION['cognome'] = $user['cognome'];
                $_SESSION['ruolo'] = $user['ruolo'];
                $_SESSION['negozio_id'] = $user['negozio_id'];
                
                // Reindirizza alla dashboard
                header('Location: index.php?module=dashboard');
                exit;
            } else {
                $error = 'Credenziali non valide. Riprova.';
            }
        } catch (PDOException $e) {
            $error = 'Errore durante il login: ' . $e->getMessage();
        }
    }
}

// Ottieni le impostazioni del negozio
try {
    $stmt = $pdo->prepare("SELECT valore FROM impostazioni WHERE chiave = 'store_name'");
    $stmt->execute();
    $result = $stmt->fetch();
    $store_name = $result ? $result['valore'] : 'CoreSuite';
} catch (PDOException $e) {
    $store_name = 'CoreSuite';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo htmlspecialchars($store_name); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo img {
            max-height: 80px;
        }
        .login-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .login-card .card-header {
            background-color: var(--primary-color);
            color: white;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 20px;
        }
        .login-form {
            padding: 20px;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <img src="assets/img/logo.svg" alt="<?php echo htmlspecialchars($store_name); ?>">
            <h2><?php echo htmlspecialchars($store_name); ?></h2>
        </div>
        
        <div class="card login-card">
            <div class="card-header">
                <h4 class="mb-0">Accesso al Sistema</h4>
            </div>
            <div class="card-body login-form">
                <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>
                
                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Ricordami</label>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Accedi</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="login-footer">
            <p>Data e ora: <?php echo date('d/m/Y H:i'); ?></p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($store_name); ?> - Tutti i diritti riservati</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

