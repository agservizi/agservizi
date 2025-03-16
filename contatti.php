<?php
$page_title = "Contatti";
include 'includes/header.php';

// Process form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $service = filter_input(INPUT_POST, 'service', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    // Validate form data
    if (empty($name) || empty($email) || empty($message)) {
        $error_message = 'Per favore compila tutti i campi obbligatori.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Per favore inserisci un indirizzo email valido.';
    } else {
        // Save to database (simplified example)
        // In a real application, you would use a database connection
        
        // Send email notification
        $to = 'info@agserviziplinio.it';
        $subject = 'Nuovo messaggio dal modulo di contatto';
        $email_message = "Nome: $name\n";
        $email_message .= "Email: $email\n";
        $email_message .= "Telefono: $phone\n";
        $email_message .= "Servizio: $service\n";
        $email_message .= "Messaggio:\n$message\n";
        $headers = "From: $email";
        
        if (mail($to, $subject, $email_message, $headers)) {
            $success_message = 'Grazie per averci contattato! Ti risponderemo al più presto.';
            
            // Send confirmation email to user
            $user_subject = 'Conferma di ricezione - AG Servizi Via Plinio 72';
            $user_message = "Gentile $name,\n\n";
            $user_message .= "Grazie per averci contattato. Abbiamo ricevuto il tuo messaggio e ti risponderemo al più presto.\n\n";
            $user_message .= "Cordiali saluti,\n";
            $user_message .= "Il team di AG Servizi Via Plinio 72";
            $user_headers = "From: info@agserviziplinio.it";
            
            mail($email, $user_subject, $user_message, $user_headers);
        } else {
            $error_message = 'Si è verificato un errore durante l\'invio del messaggio. Riprova più tardi.';
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contatti</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contatti</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-container">
            <div class="contact-info" data-aos="fade-right">
                <h2>Informazioni di Contatto</h2>
                <p>Siamo qui per aiutarti. Contattaci per qualsiasi informazione sui nostri servizi o per richiedere assistenza.</p>
                
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h3>Indirizzo</h3>
                        <p>Via Plinio 72, Milano, Italia</p>
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
                        <p>info@agserviziplinio.it</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <div>
                        <h3>Orari di Apertura</h3>
                        <p>Lunedì - Venerdì: 9:00 - 18:00</p>
                        <p>Sabato: 9:00 - 12:30</p>
                        <p>Domenica: Chiuso</p>
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
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
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="contatti.php" method="post">
                    <div class="form-group">
                        <label for="name">Nome *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Telefono</label>
                        <input type="tel" id="phone" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label for="service">Servizio di Interesse</label>
                        <select id="service" name="service">
                            <option value="">Seleziona un servizio</option>
                            <option value="Pagamenti">Pagamenti</option>
                            <option value="Spedizioni">Spedizioni</option>
                            <option value="Telefonia">Telefonia</option>
                            <option value="Energia">Energia</option>
                            <option value="SPID">SPID</option>
                            <option value="PEC">PEC</option>
                            <option value="Firma Digitale">Firma Digitale</option>
                            <option value="Visure">Visure</option>
                            <option value="Altro">Altro</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Messaggio *</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="privacy-check">
                            <input type="checkbox" id="privacy" name="privacy" required>
                            <label for="privacy">Ho letto e accetto la <a href="/privacy-policy.php">Privacy Policy</a> *</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Invia Messaggio</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title">Dove Siamo</h2>
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2798.2353308266726!2d9.2!3d45.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDXCsDMwJzAwLjAiTiA5wrAxMicwMC4wIkU!5e0!3m2!1sit!2sit!4v1616000000000!5m2!1sit!2sit" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

