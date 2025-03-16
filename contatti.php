<?php
// Include database connection
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

$success_message = '';
$error_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $service = sanitize_input($_POST['service'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = "Per favore compila tutti i campi obbligatori.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Per favore inserisci un indirizzo email valido.";
    } else {
        // Save to database
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, service, message, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssss", $name, $email, $phone, $service, $message);
        
        if ($stmt->execute()) {
            // Send email notification
            $to = "info@agenziaplinio.it";
            $subject = "Nuovo messaggio dal sito web";
            $email_message = "Nome: $name\n";
            $email_message .= "Email: $email\n";
            $email_message .= "Telefono: $phone\n";
            $email_message .= "Servizio: $service\n\n";
            $email_message .= "Messaggio:\n$message";
            $headers = "From: noreply@agenziaplinio.it";
            
            mail($to, $subject, $email_message, $headers);
            
            // Send confirmation email to user
            $user_subject = "Grazie per averci contattato";
            $user_message = "Gentile $name,\n\n";
            $user_message .= "Grazie per averci contattato. Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.\n\n";
            $user_message .= "Cordiali saluti,\n";
            $user_message .= "Il team di Agenzia Plinio";
            $user_headers = "From: info@agenziaplinio.it";
            
            mail($email, $user_subject, $user_message, $user_headers);
            
            $success_message = "Grazie per averci contattato! Ti risponderemo al più presto.";
            
            // Clear form data
            $name = $email = $phone = $service = $message = '';
        } else {
            $error_message = "Si è verificato un errore. Riprova più tardi.";
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
    <title>Contatti - Agenzia Plinio</title>
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
            <h1>Contattaci</h1>
            <p>Siamo qui per aiutarti. Contattaci per qualsiasi informazione.</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-content">
                <div class="contact-info" data-aos="fade-right">
                    <h2>Informazioni di Contatto</h2>
                    <p>Hai domande sui nostri servizi? Contattaci e saremo felici di aiutarti.</p>
                    
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Indirizzo</h3>
                            <p>Via Plinio 72, Milano</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <h3>Telefono</h3>
                            <p>+39 02 1234567</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>info@agenziaplinio.it</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <h3>Orari di Apertura</h3>
                            <p>Lun-Ven: 9:00-18:00</p>
                            <p>Sab: 9:00-12:30</p>
                        </div>
                    </div>
                    
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="contact-form" data-aos="fade-left">
                    <h2>Inviaci un Messaggio</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-error">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="contatti.php" method="post">
                        <div class="form-group">
                            <label for="name">Nome *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Telefono</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="service">Servizio di Interesse</label>
                            <select id="service" name="service">
                                <option value="">Seleziona un servizio</option>
                                <option value="Pagamenti" <?php if(isset($service) && $service === 'Pagamenti') echo 'selected'; ?>>Pagamenti</option>
                                <option value="Spedizioni" <?php if(isset($service) && $service === 'Spedizioni') echo 'selected'; ?>>Spedizioni</option>
                                <option value="Telefonia" <?php if(isset($service) && $service === 'Telefonia') echo 'selected'; ?>>Telefonia</option>
                                <option value="Energia" <?php if(isset($service) && $service === 'Energia') echo 'selected'; ?>>Energia</option>
                                <option value="SPID" <?php if(isset($service) && $service === 'SPID') echo 'selected'; ?>>SPID</option>
                                <option value="PEC" <?php if(isset($service) && $service === 'PEC') echo 'selected'; ?>>PEC</option>
                                <option value="Firma Digitale" <?php if(isset($service) && $service === 'Firma Digitale') echo 'selected'; ?>>Firma Digitale</option>
                                <option value="Visure" <?php if(isset($service) && $service === 'Visure') echo 'selected'; ?>>Visure</option>
                                <option value="Altro" <?php if(isset($service) && $service === 'Altro') echo 'selected'; ?>>Altro</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Messaggio *</label>
                            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Invia Messaggio</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="map-container" data-aos="fade-up">
                <h2>Dove Siamo</h2>
                <div class="map">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2798.2639001835!2d9.2!3d45.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDXCsDMwJzAwLjAiTiA5wrAxMicwMC4wIkU!5e0!3m2!1sit!2sit!4v1616000000000!5m2!1sit!2sit" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (include from a separate file) -->
    <?php include 'includes/footer.php'; ?>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="js/main.js"></script>
</body>
</html>

