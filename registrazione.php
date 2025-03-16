<?php
$page_title = "Registrazione";
include 'includes/header.php';

// Initialize variables
$name = '';
$email = '';
$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate form data
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Per favore compila tutti i campi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Per favore inserisci un indirizzo email valido.';
    } elseif (strlen($password) < 8) {
        $error = 'La password deve contenere almeno 8 caratteri.';
    } elseif ($password !== $confirm_password) {
        $error = 'Le password non corrispondono.';
    } else {
        // In a real application, you would save the user to a database
        // For this example, we'll use a simplified approach
        
        // Simulate user registration
        // In a real application, you would hash the password with password_hash()
        $success = 'Registrazione completata con successo! Ora puoi accedere al tuo account.';
        
        // Clear form data
        $name = '';
        $email = '';
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Registrazione</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Registrazione</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Registration Section -->
<section class="auth-section">
    <div class="container">
        <div class="auth-container" data-aos="fade-up">
            <div class="auth-form">
                <h2>Crea un nuovo account</h2>
                
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
                
                <form action="registrazione.php" method="post">
                    <div class="form-group">
                        <label for="name">Nome completo</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                        <small>La password deve contenere almeno 8 caratteri.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Conferma Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <div class="form-group">
                        <div class="privacy-check">
                            <input type="checkbox" id="privacy" name="privacy" required>
                            <label for="privacy">Ho letto e accetto la <a href="/privacy-policy.php">Privacy Policy</a> e i <a href="/termini-condizioni.php">Termini e Condizioni</a> *</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Registrati</button>
                </form>
                
                <div class="auth-footer">
                    <p>Hai già un account? <a href="login.php">Accedi</a></p>
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

