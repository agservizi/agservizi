<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

// Script per l'inizializzazione del database del sistema CoreSuite
// Questo script crea tutte le tabelle necessarie e inserisce i dati di esempio

// Disabilita il timeout per operazioni lunghe
set_time_limit(0);

// Includi la configurazione del database
require_once 'config/database.php';

// Funzione per eseguire le query SQL
function executeQuery($pdo, $query, $description) {
   try {
       $pdo->exec($query);
       return "<div style='color: green;'>✓ $description completato con successo</div>";
   } catch (PDOException $e) {
       return "<div style='color: red;'>✗ Errore durante $description: " . $e->getMessage() . "</div>";
   }
}

// Inizia l'output HTML
?>
<!DOCTYPE html>
<html lang="it">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Setup Database - CoreSuite</title>
   <style>
       body {
           font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
           line-height: 1.6;
           max-width: 800px;
           margin: 0 auto;
           padding: 20px;
       }
       h1, h2 {
           color: #333;
       }
       .container {
           border: 1px solid #ddd;
           padding: 20px;
           border-radius: 5px;
           margin-bottom: 20px;
       }
       .log {
           background-color: #f5f5f5;
           padding: 15px;
           border-radius: 5px;
           margin-top: 20px;
           max-height: 400px;
           overflow-y: auto;
       }
       .success {
           color: green;
           font-weight: bold;
       }
       .error {
           color: red;
           font-weight: bold;
       }
       .warning {
           color: orange;
           font-weight: bold;
       }
       .button {
           display: inline-block;
           background-color: #2e8b57;
           color: white;
           padding: 10px 15px;
           text-decoration: none;
           border-radius: 4px;
           margin-top: 20px;
       }
       .info {
           margin-top: 10px;
           padding: 10px;
           background-color: #f0f8ff;
           border-radius: 5px;
           border-left: 4px solid #2e8b57;
       }
   </style>
</head>
<body>
   <h1>Setup Database - CoreSuite</h1>
   
   <div class="container">
       <h2>Informazioni Connessione</h2>
       <p><strong>Host:</strong> <?php echo $db_host; ?></p>
       <p><strong>Database:</strong> <?php echo $db_name; ?></p>
       <p><strong>Utente:</strong> <?php echo $db_user; ?></p>
       <div class="info">
           <p><strong>Data e ora attuale:</strong> <?php echo date('d/m/Y H:i:s'); ?> (Fuso orario: Europe/Rome)</p>
       </div>
   </div>
   
   <div class="container">
       <h2>Log Operazioni</h2>
       <div class="log">
<?php
// Verifica la connessione al database
try {
   $pdo->query("SELECT 1");
   echo "<div class='success'>✓ Connessione al database stabilita con successo</div>";
} catch (PDOException $e) {
   echo "<div class='error'>✗ Errore di connessione al database: " . $e->getMessage() . "</div>";
   echo "</div></div></body></html>";
   exit;
}

// Disabilita i controlli delle chiavi esterne durante la creazione delle tabelle
echo executeQuery($pdo, "SET FOREIGN_KEY_CHECKS = 0", "disabilitazione controlli chiavi esterne");

// Imposta il fuso orario del database
echo executeQuery($pdo, "SET time_zone = '+01:00'", "impostazione fuso orario database");

// Crea la tabella dei prodotti
$create_products_table = "
CREATE TABLE IF NOT EXISTS prodotti (
   id INT AUTO_INCREMENT PRIMARY KEY,
   codice VARCHAR(50) NOT NULL,
   nome VARCHAR(255) NOT NULL,
   descrizione TEXT,
   gestore VARCHAR(50) NOT NULL,
   tipo VARCHAR(50) NOT NULL,
   prezzo DECIMAL(10, 2) NOT NULL,
   iva DECIMAL(5, 2) NOT NULL DEFAULT 22.00,
   quantita INT NOT NULL DEFAULT 0,
   imei VARCHAR(50) NULL,
   iccid VARCHAR(50) NULL,
   costo_acquisto DECIMAL(10, 2) NULL,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY (codice)
)";
echo executeQuery($pdo, $create_products_table, "creazione tabella prodotti");

// Crea la tabella delle transazioni
$create_transactions_table = "
CREATE TABLE IF NOT EXISTS transazioni (
   id INT AUTO_INCREMENT PRIMARY KEY,
   data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   totale DECIMAL(10, 2) NOT NULL,
   iva DECIMAL(10, 2) NOT NULL,
   sconto DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
   metodo_pagamento VARCHAR(50) NOT NULL DEFAULT 'contanti',
   cliente_nome VARCHAR(255),
   cliente_cognome VARCHAR(255),
   cliente_telefono VARCHAR(50),
   cliente_email VARCHAR(255),
   cliente_cf VARCHAR(16),
   numero_scontrino VARCHAR(50),
   data_scontrino TIMESTAMP NULL,
   operatore_id INT NULL,
   negozio_id INT NULL,
   stato VARCHAR(20) DEFAULT 'completata'
)";
echo executeQuery($pdo, $create_transactions_table, "creazione tabella transazioni");

// Crea la tabella dei dettagli delle transazioni
$create_transaction_details_table = "
CREATE TABLE IF NOT EXISTS transazioni_dettaglio (
   id INT AUTO_INCREMENT PRIMARY KEY,
   transazione_id INT NOT NULL,
   prodotto_id INT NOT NULL,
   codice VARCHAR(50) NOT NULL,
   nome VARCHAR(255) NOT NULL,
   prezzo DECIMAL(10, 2) NOT NULL,
   iva DECIMAL(5, 2) NOT NULL,
   quantita INT NOT NULL,
   imei VARCHAR(50) NULL,
   iccid VARCHAR(50) NULL,
   FOREIGN KEY (transazione_id) REFERENCES transazioni(id) ON DELETE CASCADE
)";
echo executeQuery($pdo, $create_transaction_details_table, "creazione tabella dettagli transazioni");

// Crea la tabella delle impostazioni
$create_settings_table = "
CREATE TABLE IF NOT EXISTS impostazioni (
   chiave VARCHAR(50) PRIMARY KEY,
   valore TEXT NOT NULL,
   data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
echo executeQuery($pdo, $create_settings_table, "creazione tabella impostazioni");

// Crea la tabella dei listini operatori
$create_operator_lists_table = "
CREATE TABLE IF NOT EXISTS listini_operatori (
   id INT AUTO_INCREMENT PRIMARY KEY,
   nome VARCHAR(255) NOT NULL,
   gestore VARCHAR(50) NOT NULL,
   tipo_offerta VARCHAR(50) NOT NULL,
   descrizione TEXT,
   prezzo_attivazione DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
   canone_mensile DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
   compenso_operatore DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
   data_inizio DATE NOT NULL,
   data_fine DATE NULL,
   attivo BOOLEAN DEFAULT TRUE,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
echo executeQuery($pdo, $create_operator_lists_table, "creazione tabella listini operatori");

// Crea la tabella dei compensi operatore
$create_operator_compensations_table = "
CREATE TABLE IF NOT EXISTS compensi_operatore (
   id INT AUTO_INCREMENT PRIMARY KEY,
   operatore_id INT NOT NULL,
   transazione_id INT NULL,
   listino_id INT NULL,
   importo DECIMAL(10, 2) NOT NULL,
   tipo VARCHAR(50) NOT NULL,
   descrizione TEXT,
   data DATE NOT NULL,
   stato VARCHAR(20) DEFAULT 'pendente',
   data_pagamento DATE NULL,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
echo executeQuery($pdo, $create_operator_compensations_table, "creazione tabella compensi operatore");

// Crea la tabella dei target
$create_targets_table = "
CREATE TABLE IF NOT EXISTS target (
   id INT AUTO_INCREMENT PRIMARY KEY,
   nome VARCHAR(255) NOT NULL,
   gestore VARCHAR(50) NOT NULL,
   tipo VARCHAR(50) NOT NULL,
   obiettivo INT NOT NULL,
   raggiunto INT DEFAULT 0,
   premio_base DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
   premio_extra DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
   data_inizio DATE NOT NULL,
   data_fine DATE NOT NULL,
   negozio_id INT NULL,
   operatore_id INT NULL,
   stato VARCHAR(20) DEFAULT 'attivo',
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
echo executeQuery($pdo, $create_targets_table, "creazione tabella target");

// Crea la tabella dei negozi
$create_stores_table = "
CREATE TABLE IF NOT EXISTS negozi (
   id INT AUTO_INCREMENT PRIMARY KEY,
   nome VARCHAR(255) NOT NULL,
   indirizzo VARCHAR(255) NOT NULL,
   citta VARCHAR(100) NOT NULL,
   cap VARCHAR(10) NOT NULL,
   telefono VARCHAR(20) NULL,
   email VARCHAR(100) NULL,
   partita_iva VARCHAR(20) NULL,
   codice_fiscale VARCHAR(20) NULL,
   responsabile VARCHAR(100) NULL,
   attivo BOOLEAN DEFAULT TRUE,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
echo executeQuery($pdo, $create_stores_table, "creazione tabella negozi");

// Crea la tabella degli operatori (personale)
$create_operators_table = "
CREATE TABLE IF NOT EXISTS operatori (
   id INT AUTO_INCREMENT PRIMARY KEY,
   username VARCHAR(50) NOT NULL UNIQUE,
   password VARCHAR(255) NOT NULL,
   nome VARCHAR(100) NOT NULL,
   cognome VARCHAR(100) NOT NULL,
   email VARCHAR(100) NULL,
   telefono VARCHAR(20) NULL,
   ruolo VARCHAR(50) DEFAULT 'operatore',
   negozio_id INT NULL,
   attivo BOOLEAN DEFAULT TRUE,
   ultimo_accesso TIMESTAMP NULL,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (negozio_id) REFERENCES negozi(id) ON DELETE SET NULL
)";
echo executeQuery($pdo, $create_operators_table, "creazione tabella operatori");

// Crea la tabella per la gestione IMEI/ICCID
$create_imei_iccid_table = "
CREATE TABLE IF NOT EXISTS imei_iccid (
   id INT AUTO_INCREMENT PRIMARY KEY,
   tipo VARCHAR(10) NOT NULL,
   codice VARCHAR(50) NOT NULL UNIQUE,
   prodotto_id INT NULL,
   stato VARCHAR(20) DEFAULT 'disponibile',
   prezzo_acquisto DECIMAL(10, 2) NULL,
   fornitore VARCHAR(100) NULL,
   data_acquisto DATE NULL,
   transazione_id INT NULL,
   data_vendita TIMESTAMP NULL,
   note TEXT,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (prodotto_id) REFERENCES prodotti(id) ON DELETE SET NULL,
   FOREIGN KEY (transazione_id) REFERENCES transazioni(id) ON DELETE SET NULL
)";
echo executeQuery($pdo, $create_imei_iccid_table, "creazione tabella IMEI/ICCID");

// Crea la tabella per i messaggi
$create_messages_table = "
CREATE TABLE IF NOT EXISTS messaggi (
   id INT AUTO_INCREMENT PRIMARY KEY,
   tipo VARCHAR(20) NOT NULL,
   destinatario VARCHAR(100) NOT NULL,
   contenuto TEXT NOT NULL,
   stato VARCHAR(20) DEFAULT 'in attesa',
   data_invio TIMESTAMP NULL,
   data_programmata TIMESTAMP NULL,
   operatore_id INT NULL,
   cliente_id INT NULL,
   assistenza_id INT NULL,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (operatore_id) REFERENCES operatori(id) ON DELETE SET NULL
)";
echo executeQuery($pdo, $create_messages_table, "creazione tabella messaggi");

// Crea la tabella clienti
$create_customers_table = "
CREATE TABLE IF NOT EXISTS clienti (
   id INT AUTO_INCREMENT PRIMARY KEY,
   codice_fiscale VARCHAR(16) NULL UNIQUE,
   nome VARCHAR(100) NOT NULL,
   cognome VARCHAR(100) NOT NULL,
   telefono VARCHAR(20) NULL,
   email VARCHAR(100) NULL,
   indirizzo VARCHAR(255) NULL,
   citta VARCHAR(100) NULL,
   cap VARCHAR(10) NULL,
   tessera_fidelity VARCHAR(50) NULL UNIQUE,
   abilita_sms BOOLEAN DEFAULT FALSE,
   abilita_email BOOLEAN DEFAULT FALSE,
   data_ricontatto DATE NULL,
   note TEXT,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
echo executeQuery($pdo, $create_customers_table, "creazione tabella clienti");

// Crea la tabella per l'assistenza
$create_assistance_table = "
CREATE TABLE IF NOT EXISTS assistenza (
   id INT AUTO_INCREMENT PRIMARY KEY,
   cliente_id INT NOT NULL,
   tipo_dispositivo VARCHAR(50) NOT NULL,
   marca VARCHAR(50) NOT NULL,
   modello VARCHAR(100) NOT NULL,
   imei VARCHAR(50) NULL,
   problema TEXT NOT NULL,
   stato VARCHAR(20) DEFAULT 'in attesa',
   preventivo DECIMAL(10, 2) NULL,
   costo_ricambi DECIMAL(10, 2) DEFAULT 0.00,
   costo_manodopera DECIMAL(10, 2) DEFAULT 0.00,
   data_consegna DATE NULL,
   data_ritiro DATE NULL,
   operatore_id INT NULL,
   note TEXT,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (cliente_id) REFERENCES clienti(id) ON DELETE CASCADE,
   FOREIGN KEY (operatore_id) REFERENCES operatori(id) ON DELETE SET NULL
)";
echo executeQuery($pdo, $create_assistance_table, "creazione tabella assistenza");

// Crea la tabella per il ritiro usato
$create_used_devices_table = "
CREATE TABLE IF NOT EXISTS dispositivi_usati (
   id INT AUTO_INCREMENT PRIMARY KEY,
   cliente_id INT NOT NULL,
   tipo_dispositivo VARCHAR(50) NOT NULL,
   marca VARCHAR(50) NOT NULL,
   modello VARCHAR(100) NOT NULL,
   imei VARCHAR(50) NULL,
   stato_dispositivo VARCHAR(50) NOT NULL,
   valore_stimato DECIMAL(10, 2) NOT NULL,
   valore_pagato DECIMAL(10, 2) NOT NULL,
   transazione_id INT NULL,
   operatore_id INT NULL,
   note TEXT,
   data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (cliente_id) REFERENCES clienti(id) ON DELETE CASCADE,
   FOREIGN KEY (transazione_id) REFERENCES transazioni(id) ON DELETE SET NULL,
   FOREIGN KEY (operatore_id) REFERENCES operatori(id) ON DELETE SET NULL
)";
echo executeQuery($pdo, $create_used_devices_table, "creazione tabella dispositivi usati");

// Aggiungi la tabella cassa_movimenti se non esiste già
$create_cassa_movimenti_table = "CREATE TABLE IF NOT EXISTS cassa_movimenti (
    id INT(11) NOT NULL AUTO_INCREMENT,
    tipo ENUM('apertura', 'chiusura', 'entrata', 'uscita') NOT NULL,
    importo DECIMAL(10,2) NOT NULL,
    data_ora DATETIME NOT NULL,
    categoria VARCHAR(100) NULL,
    descrizione TEXT NULL,
    riferimento VARCHAR(100) NULL,
    user_id INT(11) NOT NULL,
    negozio_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY negozio_id (negozio_id),
    CONSTRAINT fk_cassa_movimenti_user FOREIGN KEY (user_id) REFERENCES operatori (id) ON DELETE CASCADE,
    CONSTRAINT fk_cassa_movimenti_negozio FOREIGN KEY (negozio_id) REFERENCES negozi (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
echo executeQuery($pdo, $create_cassa_movimenti_table, "creazione tabella movimenti cassa");

// Aggiungi la tabella cassa_stato se non esiste già
$create_cassa_stato_table = "CREATE TABLE IF NOT EXISTS cassa_stato (
    id INT(11) NOT NULL AUTO_INCREMENT,
    negozio_id INT(11) NOT NULL,
    stato ENUM('aperta', 'chiusa') NOT NULL DEFAULT 'chiusa',
    saldo_attuale DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ultimo_aggiornamento DATETIME NOT NULL,
    user_id INT(11) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY negozio_id (negozio_id),
    KEY user_id (user_id),
    CONSTRAINT fk_cassa_stato_negozio FOREIGN KEY (negozio_id) REFERENCES negozi (id) ON DELETE CASCADE,
    CONSTRAINT fk_cassa_stato_user FOREIGN KEY (user_id) REFERENCES operatori (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
echo executeQuery($pdo, $create_cassa_stato_table, "creazione tabella stato cassa");

// Aggiungi la tabella cassa_categorie se non esiste già
$create_cassa_categorie_table = "CREATE TABLE IF NOT EXISTS cassa_categorie (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    tipo ENUM('entrata', 'uscita', 'entrambi') NOT NULL DEFAULT 'entrambi',
    descrizione TEXT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
echo executeQuery($pdo, $create_cassa_categorie_table, "creazione tabella categorie cassa");

// Riabilita i controlli delle chiavi esterne
echo executeQuery($pdo, "SET FOREIGN_KEY_CHECKS = 1", "riabilitazione controlli chiavi esterne");

// Verifica se ci sono già prodotti nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM prodotti");
$product_count = $stmt->fetch()['count'];

if ($product_count == 0) {
   // Inserisci i dati di esempio per i prodotti
   $insert_products = "
   INSERT INTO prodotti (codice, nome, descrizione, gestore, tipo, prezzo, iva, quantita) VALUES
   ('FW-SIM-01', 'SIM Fastweb', 'SIM Fastweb con 10€ di credito', 'fastweb', 'sim', 10.00, 22.00, 50),
   ('IL-SIM-01', 'SIM Iliad', 'SIM Iliad con 10€ di credito', 'iliad', 'sim', 9.99, 22.00, 50),
   ('WT-SIM-01', 'SIM WindTre', 'SIM WindTre con 10€ di credito', 'windtre', 'sim', 10.00, 22.00, 50),
   ('SK-SIM-01', 'SIM Sky Wifi', 'SIM Sky Wifi con 10€ di credito', 'skywifi', 'sim', 10.00, 22.00, 50),
   ('PF-SIM-01', 'SIM Pianeta Fibra', 'SIM Pianeta Fibra con 10€ di credito', 'pianetafibra', 'sim', 10.00, 22.00, 50),
   ('FW-RIC-10', 'Ricarica Fastweb 10€', 'Ricarica Fastweb da 10€', 'fastweb', 'ricarica', 10.00, 0.00, 100),
   ('FW-RIC-20', 'Ricarica Fastweb 20€', 'Ricarica Fastweb da 20€', 'fastweb', 'ricarica', 20.00, 0.00, 100),
   ('IL-RIC-10', 'Ricarica Iliad 10€', 'Ricarica Iliad da 10€', 'iliad', 'ricarica', 10.00, 0.00, 100),
   ('IL-RIC-20', 'Ricarica Iliad 20€', 'Ricarica Iliad da 20€', 'iliad', 'ricarica', 20.00, 0.00, 100),
   ('WT-RIC-10', 'Ricarica WindTre 10€', 'Ricarica WindTre da 10€', 'windtre', 'ricarica', 10.00, 0.00, 100),
   ('WT-RIC-20', 'Ricarica WindTre 20€', 'Ricarica WindTre da 20€', 'windtre', 'ricarica', 20.00, 0.00, 100),
   ('WT-RIC-25', 'Ricarica WindTre 25€', 'Ricarica WindTre da 25€', 'windtre', 'ricarica', 25.00, 0.00, 100),
   ('WT-RIC-50', 'Ricarica WindTre 50€', 'Ricarica WindTre da 50€', 'windtre', 'ricarica', 50.00, 0.00, 100),
   ('FW-DEV-01', 'Router Fastweb', 'Router Wi-Fi Fastweb', 'fastweb', 'dispositivo', 49.99, 22.00, 10),
   ('IL-DEV-01', 'Router Iliad', 'Router Wi-Fi Iliad', 'iliad', 'dispositivo', 39.99, 22.00, 10),
   ('WT-DEV-01', 'Router WindTre', 'Router Wi-Fi WindTre', 'windtre', 'dispositivo', 59.99, 22.00, 10),
   ('SK-DEV-01', 'Decoder Sky', 'Decoder Sky Q', 'skytv', 'dispositivo', 99.99, 22.00, 5),
   ('PF-DEV-01', 'Router Pianeta Fibra', 'Router Wi-Fi Pianeta Fibra', 'pianetafibra', 'dispositivo', 49.99, 22.00, 10),
   ('SM-A54-01', 'Samsung Galaxy A54', 'Smartphone Samsung Galaxy A54 128GB', 'samsung', 'dispositivo', 399.99, 22.00, 5),
   ('IP-14-01', 'iPhone 14', 'Apple iPhone 14 128GB', 'apple', 'dispositivo', 799.99, 22.00, 3),
   ('XM-RN12-01', 'Xiaomi Redmi Note 12', 'Xiaomi Redmi Note 12 64GB', 'xiaomi', 'dispositivo', 249.99, 22.00, 8)
   ";
   echo executeQuery($pdo, $insert_products, "inserimento prodotti di esempio");
} else {
   echo "<div class='warning'>⚠ Prodotti già presenti nel database. Salto l'inserimento dei dati di esempio.</div>";
}

// Verifica se ci sono già impostazioni nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM impostazioni");
$settings_count = $stmt->fetch()['count'];

if ($settings_count == 0) {
   // Inserisci le impostazioni di default
   $insert_settings = "
   INSERT INTO impostazioni (chiave, valore) VALUES
   ('store_name', 'CoreSuite'),
   ('store_address', 'Via Roma 123'),
   ('store_city', 'Milano'),
   ('store_zip', '20100'),
   ('store_phone', '02 1234567'),
   ('store_email', 'info@coresuite.it'),
   ('store_vat', '12345678901'),
   ('printer_model', 'custom'),
   ('printer_connection', 'ethernet'),
   ('printer_ip', '192.168.1.100'),
   ('printer_port', '9100'),
   ('sms_api_key', ''),
   ('sms_sender', 'CoreSuite'),
   ('whatsapp_enabled', 'false'),
   ('company_logo', 'assets/img/logo.svg'),
   ('date_format', 'd/m/Y'),
   ('time_format', 'H:i'),
   ('timezone', 'Europe/Rome')
   ";
   echo executeQuery($pdo, $insert_settings, "inserimento impostazioni di default");
} else {
   echo "<div class='warning'>⚠ Impostazioni già presenti nel database. Salto l'inserimento delle impostazioni di default.</div>";
}

// Verifica se ci sono già negozi nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM negozi");
$stores_count = $stmt->fetch()['count'];

if ($stores_count == 0) {
   // Inserisci i negozi di esempio
   $insert_stores = "
   INSERT INTO negozi (nome, indirizzo, citta, cap, telefono, email, partita_iva, responsabile) VALUES
   ('Negozio Milano Centro', 'Via Dante 15', 'Milano', '20121', '02 1234567', 'milano.centro@coresuite.it', '12345678901', 'Mario Rossi'),
   ('Negozio Milano Nord', 'Viale Fulvio Testi 100', 'Milano', '20126', '02 7654321', 'milano.nord@coresuite.it', '12345678901', 'Luigi Bianchi'),
   ('Negozio Roma', 'Via del Corso 50', 'Roma', '00186', '06 1234567', 'roma@coresuite.it', '12345678901', 'Giuseppe Verdi')
   ";
   echo executeQuery($pdo, $insert_stores, "inserimento negozi di esempio");
} else {
   echo "<div class='warning'>⚠ Negozi già presenti nel database. Salto l'inserimento dei negozi di esempio.</div>";
}

// Verifica se ci sono già operatori nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM operatori");
$operators_count = $stmt->fetch()['count'];

if ($operators_count == 0) {
   // Inserisci gli operatori di esempio (password: password)
   $insert_operators = "
   INSERT INTO operatori (username, password, nome, cognome, email, telefono, ruolo, negozio_id) VALUES
   ('admin', '" . password_hash('admin', PASSWORD_DEFAULT) . "', 'Amministratore', 'Sistema', 'admin@coresuite.it', '333 1234567', 'amministratore', 1),
   ('mario.rossi', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'Mario', 'Rossi', 'mario.rossi@coresuite.it', '333 2345678', 'responsabile', 1),
   ('luigi.bianchi', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'Luigi', 'Bianchi', 'luigi.bianchi@coresuite.it', '333 3456789', 'responsabile', 2),
   ('giuseppe.verdi', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'Giuseppe', 'Verdi', 'giuseppe.verdi@coresuite.it', '333 4567890', 'responsabile', 3),
   ('operatore1', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'Paolo', 'Neri', 'paolo.neri@coresuite.it', '333 5678901', 'operatore', 1),
   ('operatore2', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'Anna', 'Gialli', 'anna.gialli@coresuite.it', '333 6789012', 'operatore', 2)
   ";
   echo executeQuery($pdo, $insert_operators, "inserimento operatori di esempio");
} else {
   // Verifica se esiste l'utente admin e aggiorna la password se necessario
   $stmt = $pdo->prepare("SELECT id FROM operatori WHERE username = 'admin'");
   $stmt->execute();
   $admin = $stmt->fetch();
   
   if ($admin) {
      $update_admin = $pdo->prepare("UPDATE operatori SET password = ? WHERE username = 'admin'");
      $update_admin->execute([password_hash('admin', PASSWORD_DEFAULT)]);
      echo "<div class='info'>✓ Password dell'utente admin aggiornata</div>";
   } else {
      $insert_admin = $pdo->prepare("INSERT INTO operatori (username, password, nome, cognome, email, ruolo, negozio_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
      $insert_admin->execute(['admin', password_hash('admin', PASSWORD_DEFAULT), 'Amministratore', 'Sistema', 'admin@coresuite.it', 'amministratore', 1]);
      echo "<div class='info'>✓ Utente admin creato</div>";
   }
   
   echo "<div class='warning'>⚠ Operatori già presenti nel database. Salto l'inserimento degli operatori di esempio.</div>";
}

// Verifica se ci sono già listini operatori nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM listini_operatori");
$lists_count = $stmt->fetch()['count'];

if ($lists_count == 0) {
   // Data attuale in formato italiano
   $data_oggi = date('Y-m-d');
   $data_fine = date('Y-m-d', strtotime('+1 year'));
   
   // Inserisci i listini operatori di esempio
   $insert_lists = "
   INSERT INTO listini_operatori (nome, gestore, tipo_offerta, descrizione, prezzo_attivazione, canone_mensile, compenso_operatore, data_inizio, data_fine) VALUES
   ('Fastweb Mobile 100GB', 'fastweb', 'mobile', 'Offerta mobile con 100GB, minuti illimitati e 100 SMS', 10.00, 7.95, 15.00, '$data_oggi', '$data_fine'),
   ('Fastweb Casa Light', 'fastweb', 'fisso', 'Offerta casa con FTTC fino a 200Mbps', 39.00, 27.95, 30.00, '$data_oggi', '$data_fine'),
   ('Fastweb Casa Plus', 'fastweb', 'fisso', 'Offerta casa con FTTH fino a 1Gbps', 39.00, 34.95, 35.00, '$data_oggi', '$data_fine'),
   ('Iliad Giga 120', 'iliad', 'mobile', 'Offerta mobile con 120GB in 5G, minuti e SMS illimitati', 9.99, 9.99, 10.00, '$data_oggi', '$data_fine'),
   ('Iliad Fibra', 'iliad', 'fisso', 'Offerta casa con FTTH fino a 5Gbps', 39.99, 19.99, 25.00, '$data_oggi', '$data_fine'),
   ('WindTre GO 150GB', 'windtre', 'mobile', 'Offerta mobile con 150GB, minuti illimitati e 200 SMS', 10.00, 8.99, 12.00, '$data_oggi', '$data_fine'),
   ('WindTre Super Fibra', 'windtre', 'fisso', 'Offerta casa con FTTH fino a 1Gbps e chiamate illimitate', 39.99, 26.99, 28.00, '$data_oggi', '$data_fine'),
   ('Sky WiFi', 'skywifi', 'fisso', 'Offerta casa con FTTH fino a 1Gbps', 49.00, 29.90, 32.00, '$data_oggi', '$data_fine'),
   ('Sky TV', 'skytv', 'tv', 'Pacchetto base Sky TV', 0.00, 14.90, 20.00, '$data_oggi', '$data_fine'),
   ('Sky TV + Cinema', 'skytv', 'tv', 'Pacchetto Sky TV + Cinema', 0.00, 24.90, 25.00, '$data_oggi', '$data_fine'),
   ('Pianeta Fibra Casa', 'pianetafibra', 'fisso', 'Offerta casa con FTTH fino a 1Gbps', 29.00, 24.90, 27.00, '$data_oggi', '$data_fine')
   ";
   echo executeQuery($pdo, $insert_lists, "inserimento listini operatori di esempio");
} else {
   echo "<div class='warning'>⚠ Listini operatori già presenti nel database. Salto l'inserimento dei listini di esempio.</div>";
}

// Verifica se ci sono già target nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM target");
$targets_count = $stmt->fetch()['count'];

if ($targets_count == 0) {
   // Date in formato italiano per i target
   $data_inizio_trimestre = date('Y-m-d', strtotime('first day of this month'));
   $data_fine_trimestre = date('Y-m-d', strtotime('last day of +2 month'));
   
   // Inserisci i target di esempio
   $insert_targets = "
   INSERT INTO target (nome, gestore, tipo, obiettivo, raggiunto, premio_base, premio_extra, data_inizio, data_fine, negozio_id) VALUES
   ('Target Fastweb Mobile Q1', 'fastweb', 'mobile', 50, 32, 500.00, 200.00, '$data_inizio_trimestre', '$data_fine_trimestre', 1),
   ('Target Fastweb Casa Q1', 'fastweb', 'fisso', 30, 18, 600.00, 300.00, '$data_inizio_trimestre', '$data_fine_trimestre', 1),
   ('Target Iliad Q1', 'iliad', 'mobile', 40, 35, 400.00, 150.00, '$data_inizio_trimestre', '$data_fine_trimestre', 1),
   ('Target WindTre Q1', 'windtre', 'mobile', 60, 45, 600.00, 250.00, '$data_inizio_trimestre', '$data_fine_trimestre', 1),
   ('Target Sky Q1', 'skytv', 'tv', 20, 12, 300.00, 100.00, '$data_inizio_trimestre', '$data_fine_trimestre', 1),
   ('Target Fastweb Mobile Q1', 'fastweb', 'mobile', 40, 28, 400.00, 150.00, '$data_inizio_trimestre', '$data_fine_trimestre', 2),
   ('Target Fastweb Casa Q1', 'fastweb', 'fisso', 25, 20, 500.00, 200.00, '$data_inizio_trimestre', '$data_fine_trimestre', 2),
   ('Target Iliad Q1', 'iliad', 'mobile', 35, 30, 350.00, 120.00, '$data_inizio_trimestre', '$data_fine_trimestre', 2),
   ('Target WindTre Q1', 'windtre', 'mobile', 50, 38, 500.00, 200.00, '$data_inizio_trimestre', '$data_fine_trimestre', 2),
   ('Target Sky Q1', 'skytv', 'tv', 15, 10, 250.00, 80.00, '$data_inizio_trimestre', '$data_fine_trimestre', 2)
   ";
   echo executeQuery($pdo, $insert_targets, "inserimento target di esempio");
} else {
   echo "<div class='warning'>⚠ Target già presenti nel database. Salto l'inserimento dei target di esempio.</div>";
}

// Verifica se ci sono già clienti nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM clienti");
$customers_count = $stmt->fetch()['count'];

if ($customers_count == 0) {
   // Inserisci i clienti di esempio
   $insert_customers = "
   INSERT INTO clienti (codice_fiscale, nome, cognome, telefono, email, indirizzo, citta, cap, tessera_fidelity, abilita_sms, abilita_email) VALUES
   ('RSSMRA80A01F205X', 'Mario', 'Rossi', '333 1234567', 'mario.rossi@email.it', 'Via Roma 1', 'Milano', '20121', 'FID001', 1, 1),
   ('VRDGPP75B02F205Y', 'Giuseppe', 'Verdi', '333 2345678', 'giuseppe.verdi@email.it', 'Via Dante 2', 'Milano', '20121', 'FID002', 1, 0),
   ('BNCNNA82C03F205Z', 'Anna', 'Bianchi', '333 3456789', 'anna.bianchi@email.it', 'Via Montenapoleone 3', 'Milano', '20121', 'FID003', 0, 1),
   ('NRILCU70D04F205A', 'Luca', 'Neri', '333 4567890', 'luca.neri@email.it', 'Corso Buenos Aires 4', 'Milano', '20124', 'FID004', 1, 1),
   ('GLLSRA85E05F205B', 'Sara', 'Gialli', '333 5678901', 'sara.gialli@email.it', 'Viale Certosa 5', 'Milano', '20155', 'FID005', 0, 0)
   ";
   echo executeQuery($pdo, $insert_customers, "inserimento clienti di esempio");
} else {
   echo "<div class='warning'>⚠ Clienti già presenti nel database. Salto l'inserimento dei clienti di esempio.</div>";
}

// Verifica se ci sono già IMEI/ICCID nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM imei_iccid");
$imei_count = $stmt->fetch()['count'];

if ($imei_count == 0) {
   // Data di acquisto in formato italiano
   $data_acquisto1 = date('Y-m-d', strtotime('-2 months'));
   $data_acquisto2 = date('Y-m-d', strtotime('-1 month'));
   $data_acquisto3 = date('Y-m-d', strtotime('-3 weeks'));
   
   // Inserisci gli IMEI/ICCID di esempio
   $insert_imei = "
   INSERT INTO imei_iccid (tipo, codice, prodotto_id, stato, prezzo_acquisto, fornitore, data_acquisto) VALUES
   ('IMEI', '123456789012345', 19, 'disponibile', 300.00, 'Samsung Italia', '$data_acquisto1'),
   ('IMEI', '234567890123456', 19, 'disponibile', 300.00, 'Samsung Italia', '$data_acquisto1'),
   ('IMEI', '345678901234567', 20, 'disponibile', 650.00, 'Apple Italia', '$data_acquisto2'),
   ('IMEI', '456789012345678', 21, 'disponibile', 180.00, 'Xiaomi Italia', '$data_acquisto3'),
   ('IMEI', '567890123456789', 21, 'disponibile', 180.00, 'Xiaomi Italia', '$data_acquisto3'),
   ('ICCID', '8939010000000001', 1, 'disponibile', 5.00, 'Fastweb', '$data_acquisto1'),
   ('ICCID', '8939010000000002', 1, 'disponibile', 5.00, 'Fastweb', '$data_acquisto1'),
   ('ICCID', '8939020000000001', 2, 'disponibile', 5.00, 'Iliad', '$data_acquisto2'),
   ('ICCID', '8939020000000002', 2, 'disponibile', 5.00, 'Iliad', '$data_acquisto2'),
   ('ICCID', '8939030000000001', 3, 'disponibile', 5.00, 'WindTre', '$data_acquisto3')
   ";
   echo executeQuery($pdo, $insert_imei, "inserimento IMEI/ICCID di esempio");
} else {
   echo "<div class='warning'>⚠ IMEI/ICCID già presenti nel database. Salto l'inserimento degli IMEI/ICCID di esempio.</div>";
}

// Verifica se ci sono già assistenze nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM assistenza");
$assistance_count = $stmt->fetch()['count'];

if ($assistance_count == 0) {
   // Inserisci le assistenze di esempio
   $insert_assistance = "
   INSERT INTO assistenza (cliente_id, tipo_dispositivo, marca, modello, imei, problema, stato, preventivo, costo_ricambi, costo_manodopera, operatore_id) VALUES
   (1, 'Smartphone', 'Samsung', 'Galaxy S21', '123456789054321', 'Schermo rotto', 'in lavorazione', 150.00, 100.00, 50.00, 5),
   (2, 'Smartphone', 'Apple', 'iPhone 13', '987654321054321', 'Non si accende', 'in attesa', NULL, 0.00, 0.00, 5),
   (3, 'Tablet', 'Apple', 'iPad Pro', '567891234054321', 'Batteria da sostituire', 'completata', 120.00, 80.00, 40.00, 6),
   (4, 'Smartphone', 'Xiaomi', 'Redmi Note 11', '432156789054321', 'Problemi di connessione', 'in lavorazione', 60.00, 20.00, 40.00, 6)
   ";
   echo executeQuery($pdo, $insert_assistance, "inserimento assistenze di esempio");
} else {
   echo "<div class='warning'>⚠ Assistenze già presenti nel database. Salto l'inserimento delle assistenze di esempio.</div>";
}

// Verifica se ci sono già dispositivi usati nel database
$stmt = $pdo->query("SELECT COUNT(*) as count FROM dispositivi_usati");
$used_count = $stmt->fetch()['count'];

if ($used_count == 0) {
   // Inserisci i dispositivi usati di esempio
   $insert_used = "
   INSERT INTO dispositivi_usati (cliente_id, tipo_dispositivo, marca, modello, imei, stato_dispositivo, valore_stimato, valore_pagato, operatore_id) VALUES
   (1, 'Smartphone', 'Samsung', 'Galaxy S20', '123456789054322', 'Buono', 200.00, 180.00, 5),
   (2, 'Smartphone', 'Apple', 'iPhone 12', '987654321054322', 'Ottimo', 350.00, 320.00, 5),
   (3, 'Tablet', 'Samsung', 'Galaxy Tab S7', '567891234054322', 'Discreto', 150.00, 130.00, 6)
   ";
   echo executeQuery($pdo, $insert_used, "inserimento dispositivi usati di esempio");
} else {
   echo "<div class='warning'>⚠ Dispositivi usati già presenti nel database. Salto l'inserimento dei dispositivi usati di esempio.</div>";
}

// Inserisci alcune categorie predefinite
$insert_cassa_categorie = "INSERT INTO cassa_categorie (nome, tipo, descrizione) VALUES 
('Vendita', 'entrata', 'Entrate da vendite di prodotti o servizi'),
('Ricarica telefonica', 'entrata', 'Entrate da ricariche telefoniche'),
('Pagamento bolletta', 'entrata', 'Entrate da pagamento bollette'),
('Incasso fattura', 'entrata', 'Entrate da incasso fatture'),
('Stipendio', 'uscita', 'Uscite per pagamento stipendi'),
('Fornitore', 'uscita', 'Uscite per pagamento fornitori'),
('Affitto', 'uscita', 'Uscite per pagamento affitto'),
('Utenze', 'uscita', 'Uscite per pagamento utenze'),
('Altro', 'entrambi', 'Altre entrate o uscite non categorizzate')";
echo executeQuery($pdo, $insert_cassa_categorie, "inserimento categorie cassa di default");

// Aggiungi i seguenti permessi per la gestione cassa dopo gli altri INSERT INTO permissions
$insert_permissions = "INSERT INTO permissions (name, description) VALUES 
('view_cassa', 'Visualizzazione della cassa'),
('open_close_cassa', 'Apertura e chiusura della cassa'),
('add_entrata_cassa', 'Registrazione entrate in cassa'),
('add_uscita_cassa', 'Registrazione uscite dalla cassa'),
('delete_movimento_cassa', 'Eliminazione movimenti di cassa'),
('export_report_cassa', 'Esportazione report di cassa')";
echo executeQuery($pdo, $insert_permissions, "inserimento permessi cassa");

// Assegna i permessi ai ruoli dopo gli altri INSERT INTO roles_permissions
// Amministratore (tutti i permessi)
$insert_roles_permissions_admin = "INSERT INTO roles_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions WHERE name IN ('view_cassa', 'open_close_cassa', 'add_entrata_cassa', 'add_uscita_cassa', 'delete_movimento_cassa', 'export_report_cassa')";
echo executeQuery($pdo, $insert_roles_permissions_admin, "assegnazione permessi cassa ad admin");

// Manager (tutti tranne eliminazione)
$insert_roles_permissions_manager = "INSERT INTO roles_permissions (role_id, permission_id) 
SELECT 2, id FROM permissions WHERE name IN ('view_cassa', 'open_close_cassa', 'add_entrata_cassa', 'add_uscita_cassa', 'export_report_cassa')";
echo executeQuery($pdo, $insert_roles_permissions_manager, "assegnazione permessi cassa a manager");

// Cassiere (visualizzazione, apertura/chiusura, entrate e uscite)
$insert_roles_permissions_cassiere = "INSERT INTO roles_permissions (role_id, permission_id) 
SELECT 3, id FROM permissions WHERE name IN ('view_cassa', 'open_close_cassa', 'add_entrata_cassa', 'add_uscita_cassa')";
echo executeQuery($pdo, $insert_roles_permissions_cassiere, "assegnazione permessi cassa a cassiere");

// Venditore (solo visualizzazione)
$insert_roles_permissions_venditore = "INSERT INTO roles_permissions (role_id, permission_id) 
SELECT 4, id FROM permissions WHERE name IN ('view_cassa')";
echo executeQuery($pdo, $insert_roles_permissions_venditore, "assegnazione permessi cassa a venditore");

// Verifica finale
try {
   $tables = [
      'prodotti', 'transazioni', 'transazioni_dettaglio', 'impostazioni', 
      'listini_operatori', 'compensi_operatore', 'target', 'negozi', 
      'operatori', 'imei_iccid', 'messaggi', 'clienti', 'assistenza', 'dispositivi_usati',
      'cassa_movimenti', 'cassa_stato', 'cassa_categorie'
   ];
   $all_tables_exist = true;
   
   foreach ($tables as $table) {
       $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
       if ($stmt->rowCount() == 0) {
           $all_tables_exist = false;
           echo "<div class='error'>✗ La tabella '$table' non esiste!</div>";
       }
   }
   
   if ($all_tables_exist) {
       echo "<div class='success'>✓ Tutte le tabelle sono state create correttamente</div>";
       echo "<div class='success'>✓ Setup del database completato con successo!</div>";
       echo "<div class='info'>Data e ora di completamento: " . date('d/m/Y H:i:s') . "</div>";
   }
} catch (PDOException $e) {
   echo "<div class='error'>✗ Errore durante la verifica finale: " . $e->getMessage() . "</div>";
}
?>
       </div>
   </div>
   
   <a href="index.php?module=dashboard" class="button">Vai alla Dashboard</a>
</body>
</html>

