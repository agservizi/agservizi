-- Schema del database per il sistema POS

-- Tabella dei prodotti
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
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY (codice)
);

-- Tabella delle transazioni
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
    data_scontrino TIMESTAMP NULL
);

-- Tabella dei dettagli delle transazioni
CREATE TABLE IF NOT EXISTS transazioni_dettaglio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transazione_id INT NOT NULL,
    prodotto_id INT NOT NULL,
    codice VARCHAR(50) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    prezzo DECIMAL(10, 2) NOT NULL,
    iva DECIMAL(5, 2) NOT NULL,
    quantita INT NOT NULL,
    FOREIGN KEY (transazione_id) REFERENCES transazioni(id) ON DELETE CASCADE
);

-- Tabella delle impostazioni
CREATE TABLE IF NOT EXISTS impostazioni (
    chiave VARCHAR(50) PRIMARY KEY,
    valore TEXT NOT NULL,
    data_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserimento di alcuni dati di esempio
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
('PF-DEV-01', 'Router Pianeta Fibra', 'Router Wi-Fi Pianeta Fibra', 'pianetafibra', 'dispositivo', 49.99, 22.00, 10);

-- Inserimento delle impostazioni di default
INSERT INTO impostazioni (chiave, valore) VALUES
('store_name', 'Negozio Telefonia'),
('store_address', 'Via Roma 123'),
('store_city', 'Milano'),
('store_zip', '20100'),
('store_phone', '02 1234567'),
('store_email', 'info@negoziotelefonia.it'),
('store_vat', '12345678901'),
('printer_model', 'custom'),
('printer_connection', 'ethernet'),
('printer_ip', '192.168.1.100'),
('printer_port', '9100');

