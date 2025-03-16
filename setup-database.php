<?php
// Includi il file di connessione al database
require_once 'includes/db_connect.php';

// Array per memorizzare i messaggi di stato
$messages = [];

// Funzione per eseguire una query e registrare il risultato
function executeQuery($conn, $query, $description) {
    global $messages;
    
    try {
        if ($conn->query($query)) {
            $messages[] = "✅ SUCCESSO: " . $description;
            return true;
        } else {
            $messages[] = "❌ ERRORE: " . $description . " - " . $conn->error;
            return false;
        }
    } catch (Exception $e) {
        $messages[] = "❌ ECCEZIONE: " . $description . " - " . $e->getMessage();
        return false;
    }
}

// 1. Creazione delle tabelle
$create_users_table = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$create_admins_table = "
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$create_contacts_table = "
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service VARCHAR(50),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$create_services_table = "
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

$create_blog_posts_table = "
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    excerpt TEXT,
    image VARCHAR(255),
    author_id INT,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

// Esecuzione delle query di creazione tabelle
executeQuery($conn, $create_users_table, "Creazione tabella users");
executeQuery($conn, $create_admins_table, "Creazione tabella admins");
executeQuery($conn, $create_contacts_table, "Creazione tabella contacts");
executeQuery($conn, $create_services_table, "Creazione tabella services");
// Creazione della tabella blog_posts se non esiste
$sql = "CREATE TABLE IF NOT EXISTS blog_posts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    excerpt TEXT,
    featured_image VARCHAR(255),
    status ENUM('published', 'draft') NOT NULL DEFAULT 'draft',
    author_id INT(11) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Tabella blog_posts creata con successo o già esistente<br>";
} else {
    echo "Errore nella creazione della tabella blog_posts: " . $conn->error . "<br>";
}
//executeQuery($conn, $create_blog_posts_table, "Creazione tabella blog_posts");

// 2. Inserimento dati di default
// Admin predefinito (password: password)
$admin_password = password_hash('password', PASSWORD_DEFAULT);
$insert_admin = "
INSERT INTO admins (username, password, email, created_at)
VALUES ('admin', '$admin_password', 'admin@agenziaplinio.it', NOW())
ON DUPLICATE KEY UPDATE username = 'admin'";

executeQuery($conn, $insert_admin, "Inserimento admin predefinito");

// Servizi predefiniti
$services = [
    ['Pagamenti', 'pagamenti', 'Paga bollette, ricariche e molto altro in modo semplice e veloce.', 'fa-money-bill-wave'],
    ['Spedizioni', 'spedizioni', 'Spedisci pacchi e documenti in Italia e all\'estero con i migliori corrieri.', 'fa-shipping-fast'],
    ['Telefonia', 'telefonia', 'Attiva offerte telefoniche e internet con i migliori operatori.', 'fa-mobile-alt'],
    ['Energia', 'energia', 'Risparmia sulle bollette di luce e gas con le migliori offerte.', 'fa-bolt'],
    ['SPID', 'spid', 'Attiva la tua identità digitale per accedere ai servizi online.', 'fa-id-card'],
    ['PEC', 'pec', 'Attiva la tua casella di Posta Elettronica Certificata.', 'fa-envelope'],
    ['Firma Digitale', 'firma-digitale', 'Firma documenti digitali con valore legale.', 'fa-signature'],
    ['Visure', 'visure', 'Richiedi visure camerali, catastali e certificati.', 'fa-search']
];

foreach ($services as $service) {
    $name = $conn->real_escape_string($service[0]);
    $slug = $conn->real_escape_string($service[1]);
    $description = $conn->real_escape_string($service[2]);
    $icon = $conn->real_escape_string($service[3]);
    
    $insert_service = "
    INSERT INTO services (name, slug, description, icon, is_active, created_at)
    VALUES ('$name', '$slug', '$description', '$icon', 1, NOW())
    ON DUPLICATE KEY UPDATE name = '$name'";
    
    executeQuery($conn, $insert_service, "Inserimento servizio: $name");
}

// Blog post predefiniti
$blog_posts = [
    [
        'Come attivare lo SPID in 5 semplici passi',
        'come-attivare-spid-5-semplici-passi',
        'Contenuto dell\'articolo...',
        'Una guida completa per attivare la tua identità digitale e accedere ai servizi online della Pubblica Amministrazione.',
        'blog/spid-guide.jpg'
    ],
    [
        'Risparmiare sulla bolletta con le offerte luce e gas',
        'risparmiare-bolletta-offerte-luce-gas',
        'Contenuto dell\'articolo...',
        'Consigli pratici per ridurre i costi delle bollette e scegliere l\'offerta più adatta alle tue esigenze.',
        'blog/energia-risparmio.jpg'
    ],
    [
        'I vantaggi della PEC per privati e aziende',
        'vantaggi-pec-privati-aziende',
        'Contenuto dell\'articolo...',
        'Scopri perché la Posta Elettronica Certificata è diventata uno strumento indispensabile per la comunicazione ufficiale.',
        'blog/pec-vantaggi.jpg'
    ]
];

foreach ($blog_posts as $post) {
    $title = $conn->real_escape_string($post[0]);
    $slug = $conn->real_escape_string($post[1]);
    $content = $conn->real_escape_string($post[2]);
    $excerpt = $conn->real_escape_string($post[3]);
    $image = $conn->real_escape_string($post[4]);
    
    $insert_post = "
    INSERT INTO blog_posts (title, slug, content, excerpt, image, author_id, status, created_at)
    VALUES ('$title', '$slug', '$content', '$excerpt', '$image', 1, 'published', NOW())
    ON DUPLICATE KEY UPDATE title = '$title'";
    
    executeQuery($conn, $insert_post, "Inserimento blog post: $title");
}

// Chiudi la connessione
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Database - Agenzia Plinio</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            color: #333;
        }
        h1 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
        }
        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            border-left: 4px solid #28a745;
        }
        .error {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
        }
        .actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Setup Database - Agenzia Plinio</h1>
    
    <div class="results">
        <h2>Risultati dell'operazione:</h2>
        <?php foreach ($messages as $message): ?>
            <div class="message <?php echo strpos($message, '✅') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="actions">
        <p>Operazioni completate. Ora puoi:</p>
        <a href="index.html" class="btn">Vai alla Home</a>
    </div>
</body>
</html>

