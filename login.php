<?php
$page_title = "Accedi";
include 'includes/header.php';

// Initialize variables
$email = '';
$error = '';
$success = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($email) || empty($password)) {
        $error = 'Per favore inserisci email e password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Per favore inserisci un indirizzo email valido.';
    } else {
        // In a real application, you would check the credentials against a database
        // For this example, we'll use a simplified approach
        
        // Simulate user authentication
        // In a real application, you would use password_verify() to check the hashed password
        if ($email === 'utente@esempio.it' && $password === 'password123') {
            // Set session variables
            $_SESSION['user_id'] = 1;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = 'Utente Demo';
            
            // Redirect to dashboard
            header('Location: area-clienti/dashboard.php');
            exit;
        } else {
            $error = 'Email o password non validi. Riprova.';
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Accedi</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Accedi</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Login Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container" data-aos="fade-up">
            <div class="auth-form">
                <h2>Accedi al tuo account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form action="login.php" method="post">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Ricordami</label>
                        </div>
                        <a href="recupera-password.php" class="forgot-password">Password dimenticata?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Accedi</button>
                </form>
                
                <div class="auth-footer">
                    <p>Non hai un account? <a href="registrazione.php">Registrati</a></p>
                </div>
            </div>
            
            <div class="auth-info">
                <h3>Vantaggi dell'Area Clienti</h3>
                <ul>
                    <li><i class="fas fa-check"></i> Gestisci i tuoi servizi attivi</li>
                    <li><i class="fas fa-check"></i> Visualizza lo storico delle transazioni</li>
                    <li><i class="fas fa-check"></i> Richiedi assistenza in modo rapido</li>
                    <li><i class="fas fa-check"></i> Accedi a offerte esclusive</li>
                    <li><i class="fas fa-check"></i> Ricevi aggiornamenti sui nuovi servizi</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

