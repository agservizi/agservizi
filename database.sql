-- Create database
CREATE DATABASE IF NOT EXISTS agenzia_plinio;
USE agenzia_plinio;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create contacts table
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
);

-- Create services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create blog_posts table
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
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES admins(id) ON DELETE SET NULL
);

-- Insert default admin
INSERT INTO admins (username, password, email, created_at)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@agenziaplinio.it', NOW());
-- Default password: password

-- Insert default services
INSERT INTO services (name, slug, description, icon, is_active, created_at) VALUES
('Pagamenti', 'pagamenti', 'Paga bollette, ricariche e molto altro in modo semplice e veloce.', 'fa-money-bill-wave', 1, NOW()),
('Spedizioni', 'spedizioni', 'Spedisci pacchi e documenti in Italia e all\'estero con i migliori corrieri.', 'fa-shipping-fast', 1, NOW()),
('Telefonia', 'telefonia', 'Attiva offerte telefoniche e internet con i migliori operatori.', 'fa-mobile-alt', 1, NOW()),
('Energia', 'energia', 'Risparmia sulle bollette di luce e gas con le migliori offerte.', 'fa-bolt', 1, NOW()),
('SPID', 'spid', 'Attiva la tua identità digitale per accedere ai servizi online.', 'fa-id-card', 1, NOW()),
('PEC', 'pec', 'Attiva la tua casella di Posta Elettronica Certificata.', 'fa-envelope', 1, NOW()),
('Firma Digitale', 'firma-digitale', 'Firma documenti digitali con valore legale.', 'fa-signature', 1, NOW()),
('Visure', 'visure', 'Richiedi visure camerali, catastali e certificati.', 'fa-search', 1, NOW());

-- Insert sample blog posts
INSERT INTO blog_posts (title, slug, content, excerpt, image, author_id, status, created_at) VALUES
('Come attivare lo SPID in 5 semplici passi', 'come-attivare-spid-5-semplici-passi', 'Contenuto dell\'articolo...', 'Una guida completa per attivare la tua identità digitale e accedere ai servizi online della Pubblica Amministrazione.', 'blog/spid-guide.jpg', 1, 'published', NOW()),
('Risparmiare sulla bolletta con le offerte luce e gas', 'risparmiare-bolletta-offerte-luce-gas', 'Contenuto dell\'articolo...', 'Consigli pratici per ridurre i costi delle bollette e scegliere l\'offerta più adatta alle tue esigenze.', 'blog/energia-risparmio.jpg', 1, 'published', NOW()),
('I vantaggi della PEC per privati e aziende', 'vantaggi-pec-privati-aziende', 'Contenuto dell\'articolo...', 'Scopri perché la Posta Elettronica Certificata è diventata uno strumento indispensabile per la comunicazione ufficiale.', 'blog/pec-vantaggi.jpg', 1, 'published', NOW());

