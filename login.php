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
    <style>
        /* Stili specifici per la pagina di login */
        .auth-section {
            padding: 80px 0;
            background-color: #f8f9fa;
            min-height: calc(100vh - 300px);
            display: flex;
            align-items: center;
        }
        
        .auth-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
        }
        
        .auth-header {
            background-color: #007BFF;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .auth-header h1 {
            margin: 0;
            font-size: 2rem;
        }
        
        .auth-header p {
            margin-top: 10px;
            opacity: 0.9;
        }
        
        .auth-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
        }
        
        .auth-tab {
            flex: 1;
            text-align: center;
            padding: 20px;
            background: none;
            border: none;
            font-size: 1.1rem;
            font-weight: 500;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .auth-tab:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background-color: #007BFF;
            transition: all 0.3s ease;
        }
        
        .auth-tab.active {
            color: #007BFF;
        }
        
        .auth-tab.active:after {
            width: 100%;
        }
        
        .auth-content {
            padding: 30px;
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        
        .auth-form h2 {
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        
        .form-group small {
            display: block;
            margin-top: 5px;
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
        }
        
        .remember-me input {
            margin-right: 8px;
        }
        
        .forgot-password {
            color: #007BFF;
            font-size: 0.9rem;
        }
        
        .terms {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }
        
        .terms input {
            margin-right: 10px;
            margin-top: 5px;
        }
        
        .terms label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .terms a {
            color: #007BFF;
        }
        
        .btn-primary {
            width: 100%;
            padding: 12px;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .social-login {
            margin-top: 30px;
            text-align: center;
        }
        
        .social-login p {
            margin-bottom: 15px;
            color: #6c757d;
            position: relative;
        }
        
        .social-login p:before,
        .social-login p:after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background-color: #ddd;
        }
        
        .social-login p:before {
            left: 0;
        }
        
        .social-login p:after {
            right: 0;
        }
        
        .social-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .social-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #f8f9fa;
            color: #333;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            border: 1px solid #ddd;
        }
        
        .social-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        
        .social-button.facebook {
            background-color: #3b5998;
            color: white;
            border-color: #3b5998;
        }
        
        .social-button.google {
            background-color: #db4437;
            color: white;
            border-color: #db4437;
        }
        
        .social-button.linkedin {
            background-color: #0077b5;
            color: white;
            border-color: #0077b5;
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
        }
        
        .password-strength {
            height: 5px;
            margin-top: 10px;
            border-radius: 5px;
            background-color: #eee;
            overflow: hidden;
        }
        
        .password-strength-meter {
            height: 100%;
            width: 0;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        
        .strength-weak {
            background-color: #dc3545;
            width: 25%;
        }
        
        .strength-medium {
            background-color: #ffc107;
            width: 50%;
        }
        
        .strength-good {
            background-color: #28a745;
            width: 75%;
        }
        
        .strength-strong {
            background-color: #20c997;
            width: 100%;
        }
        
        .password-feedback {
            font-size: 0.85rem;
            margin-top: 5px;
            color: #6c757d;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .auth-section {
                padding: 50px 15px;
            }
            
            .auth-header {
                padding: 20px;
            }
            
            .auth-content {
                padding: 20px;
            }
            
            .remember-forgot {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .forgot-password {
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Header (include from a separate file) -->
    <?php include 'includes/header.php'; ?>

    <!-- Auth Section -->
    <section class="auth-section">
        <div class="container">
            <div class="auth-container" data-aos="fade-up" data-aos-duration="800">
                <div class="auth-header">
                    <h1>Area Clienti</h1>
                    <p>Accedi o registrati per gestire i tuoi servizi</p>
                </div>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
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
                                <input type="email" id="login-email" name="email" placeholder="Inserisci la tua email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="login-password">Password</label>
                                <div class="password-field">
                                    <input type="password" id="login-password" name="password" placeholder="Inserisci la tua password" required>
                                    <button type="button" class="password-toggle" aria-label="Mostra/nascondi password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="remember-forgot">
                                <div class="remember-me">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Ricordami</label>
                                </div>
                                <a href="recupera-password.php" class="forgot-password">Password dimenticata?</a>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="login" class="btn btn-primary">Accedi</button>
                            </div>
                        </form>
                        
                        <div class="social-login">
                            <p>Oppure accedi con</p>
                            <div class="social-buttons">
                                <a href="#" class="social-button facebook" aria-label="Accedi con Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-button google" aria-label="Accedi con Google">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a href="#" class="social-button linkedin" aria-label="Accedi con LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="auth-form" id="register-form">
                        <h2>Crea un nuovo account</h2>
                        <form action="login.php" method="post">
                            <div class="form-group">
                                <label for="register-name">Nome completo</label>
                                <input type="text" id="register-name" name="name" placeholder="Inserisci il tuo nome completo" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-email">Email</label>
                                <input type="email" id="register-email" name="email" placeholder="Inserisci la tua email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="register-password">Password</label>
                                <div class="password-field">
                                    <input type="password" id="register-password" name="password" placeholder="Crea una password" required>
                                    <button type="button" class="password-toggle" aria-label="Mostra/nascondi password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="password-strength-meter"></div>
                                </div>
                                <div class="password-feedback">La password deve contenere almeno 8 caratteri</div>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm-password">Conferma Password</label>
                                <div class="password-field">
                                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Conferma la tua password" required>
                                    <button type="button" class="password-toggle" aria-label="Mostra/nascondi password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="terms">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">Accetto i <a href="termini-condizioni.html">Termini e Condizioni</a> e la <a href="privacy-policy.html">Privacy Policy</a></label>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="register" class="btn btn-primary">Registrati</button>
                            </div>
                        </form>
                        
                        <div class="social-login">
                            <p>Oppure registrati con</p>
                            <div class="social-buttons">
                                <a href="#" class="social-button facebook" aria-label="Registrati con Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-button google" aria-label="Registrati con Google">
                                    <i class="fab fa-google"></i>
                                </a>
                                <a href="#" class="social-button linkedin" aria-label="Registrati con LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            AOS.init();
            
            // Auth tabs functionality
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
            
            // Password toggle functionality
            const passwordToggles = document.querySelectorAll('.password-toggle');
            
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordField = this.previousElementSibling;
                    const icon = this.querySelector('i');
                    
                    if (passwordField.type === 'password') {
                        passwordField.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordField.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            });
            
            // Password strength meter
            const passwordInput = document.getElementById('register-password');
            const strengthMeter = document.querySelector('.password-strength-meter');
            const passwordFeedback = document.querySelector('.password-feedback');
            
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    let feedback = 'La password deve contenere almeno 8 caratteri';
                    
                    if (password.length >= 8) {
                        strength += 1;
                    }
                    
                    if (password.match(/[A-Z]/)) {
                        strength += 1;
                    }
                    
                    if (password.match(/[0-9]/)) {
                        strength += 1;
                    }
                    
                    if (password.match(/[^A-Za-z0-9]/)) {
                        strength += 1;
                    }
                    
                    // Update strength meter
                    strengthMeter.className = 'password-strength-meter';
                    
                    if (password.length === 0) {
                        strengthMeter.style.width = '0';
                        feedback = 'La password deve contenere almeno 8 caratteri';
                    } else if (strength === 1) {
                        strengthMeter.classList.add('strength-weak');
                        feedback = 'Password debole - Aggiungi lettere maiuscole, numeri o simboli';
                    } else if (strength === 2) {
                        strengthMeter.classList.add('strength-medium');
                        feedback = 'Password media - Aggiungi più variazioni';
                    } else if (strength === 3) {
                        strengthMeter.classList.add('strength-good');
                        feedback = 'Password buona';
                    } else if (strength === 4) {
                        strengthMeter.classList.add('strength-strong');
                        feedback = 'Password forte';
                    }
                    
                    passwordFeedback.textContent = feedback;
                });
            }
            
            // Confirm password validation
            const confirmPassword = document.getElementById('confirm-password');
            
            if (confirmPassword && passwordInput) {
                confirmPassword.addEventListener('input', function() {
                    if (this.value !== passwordInput.value) {
                        this.setCustomValidity('Le password non corrispondono');
                    } else {
                        this.setCustomValidity('');
                    }
                });
                
                passwordInput.addEventListener('input', function() {
                    if (confirmPassword.value !== '') {
                        if (confirmPassword.value !== this.value) {
                            confirmPassword.setCustomValidity('Le password non corrispondono');
                        } else {
                            confirmPassword.setCustomValidity('');
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>

