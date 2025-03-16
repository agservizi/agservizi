<?php
// Start session
session_start();

// Include database connection
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to dashboard
    header('Location: area-clienti/dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate form data
    if (empty($email) || empty($password)) {
        $error_message = "Per favore inserisci email e password.";
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                // Redirect to dashboard
                header('Location: area-clienti/dashboard.php');
                exit;
            } else {
                $error_message = "Password non valida.";
            }
        } else {
            $error_message = "Utente non trovato.";
        }
        
        $stmt->close();
    }
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "Per favore compila tutti i campi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Per favore inserisci un indirizzo email valido.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Le password non corrispondono.";
    } elseif (strlen($password) < 8) {
        $error_message = "La password deve contenere almeno 8 caratteri.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Questo indirizzo email è già registrato.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("sss", $name, $email, $hashed_password);
            
            if ($stmt->execute()) {
                $success_message = "Registrazione completata con successo! Ora puoi accedere.";
                
                // Send welcome email
                $to = $email;
                $subject = "Benvenuto in Agenzia Plinio";
                $message = "Gentile $name,\n\n";
                $message .= "Grazie per esserti registrato sul nostro sito. Ora puoi accedere alla tua area personale per gestire i tuoi servizi.\n\n";
                $message .= "Cordiali saluti,\n";
                $message .= "Il team di Agenzia Plinio";
                $headers = "From: info@agenziaplinio.it";
                
                mail($to, $subject, $message, $headers);
            } else {
                $error_message = "Si è verificato un errore durante la registrazione. Riprova più tardi.";
            }
        }
        
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi o Registrati - Agenzia Plinio</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Header (include from a separate file) -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Banner -->
    <section class="page-banner">
        <div class="container">
            <h1>Area Clienti</h1>
            <p>Accedi o registrati per gestire i tuoi servizi</p>
        </div>
    </section>

    <!-- Login/Register Section -->
    <section class="auth-section">
        <div class="container">
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="auth-container">
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">Accedi</button>
                    <button class="auth-tab" data-tab="register">Registrati</button>
                </div>
                
                <div class="auth-content">
                    <div class="auth-form active" id="login-form">
                        <h2>Accedi al tuo account</h2>
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <label for="login-email">Email</label>
                                <input type="email" id="login-email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="login-password">Password</label>
                                <input type="password" id="login-password" name="password" required>
                            </div>
                            
                            <div class="form-group">
                                <div class="remember-forgot">
                                    <div class="remember-me">
                                        <input type="checkbox" id="remember" name="remember">
                                        <label for="remember">Ricordami</label>
                                    </div>
                                    <a href="recupera-password.php" class="forgot-password">Password dimenticata?</a>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="login" class="btn btn-primary">Accedi</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="auth-form" id="register-form">
                        <h2>Crea un nuovo account</h2>
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <label for="register-name">Nome completo</label>
                                <input type="text" id="register-name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-email">Email</label>
                                <input type="email" id="register-email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-password">Password</label>
                                <input type="password" id="register-password" name="password" required>
                                <small>La password deve contenere almeno 8 caratteri</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm-password">Conferma Password</label>
                                <input type="password" id="confirm-password" name="confirm_password" required>
                            </div>
                            
                            <div class="form-group">
                                <div class="terms">
                                    <input type="checkbox" id="terms" name="terms" required>
                                    <label for="terms">Accetto i <a href="termini-condizioni.html">Termini e Condizioni</a> e la <a href="privacy-policy.html">Privacy Policy</a></label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="register" class="btn btn-primary">Registrati</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (include from a separate file) -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Auth tabs functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.auth-tab');
            const forms = document.querySelectorAll('.auth-form');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and forms
                    tabs.forEach(t => t.classList.remove('active'));
                    forms.forEach(f => f.classList.remove('active'));
                    
                    // Add active class to current tab and form
                    this.classList.add('active');
                    document.getElementById(target + '-form').classList.add('active');
                });
            });
        });
    </script>
</body>
</html>

