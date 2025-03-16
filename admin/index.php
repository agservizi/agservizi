<?php
// Start session
session_start();

// Include database connection
require_once '../includes/db_connect.php';
require_once '../includes/functions.php';

// Check if admin is already logged in
if (isset($_SESSION['admin_id'])) {
    // Redirect to dashboard
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error_message = "Per favore inserisci username e password.";
    } else {
        // Check if admin exists
        $stmt = $conn->prepare("SELECT id, username, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password'])) {
                // Password is correct, create session
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                
                // Redirect to dashboard
                header('Location: dashboard.php');
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
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Agenzia Plinio</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-header">
            <img src="../images/logo.svg" alt="Agenzia Plinio Logo" class="login-logo">
            <h1>Admin Panel</h1>
        </div>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="index.php" method="post" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Accedi</button>
            </div>
        </form>
        
        <div class="login-footer">
            <p>&copy; <?php echo date('Y'); ?> Agenzia Plinio. Tutti i diritti riservati.</p>
        </div>
    </div>
</body>
</html>

