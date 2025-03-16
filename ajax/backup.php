<?php
// Imposta il fuso orario italiano
date_default_timezone_set('Europe/Rome');

session_start();
require_once '../config/database.php';
require_once '../includes/auth_check.php';

// Verifica l'autenticazione e il ruolo
requireRole('amministratore');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'backup') {
    // Esegui il backup del database
    $tables = [];
    $result = $pdo->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
    
    $sql = "-- Backup del database " . $db_name . "\n";
    $sql .= "-- Data: " . date('Y-m-d H:i:s') . "\n\n";
    
    foreach ($tables as $table) {
        $result = $pdo->query("SELECT * FROM $table");
        $num_fields = $result->columnCount();
        
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        
        $row2 = $pdo->query("SHOW CREATE TABLE $table")->fetch(PDO::FETCH_NUM);
        $sql .= $row2[1] . ";\n\n";
        
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $sql .= "INSERT INTO `$table` VALUES (";
            for ($j = 0; $j < $num_fields; $j++) {
                if (isset($row[$j])) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    $sql .= '"' . $row[$j] . '"';
                } else {
                    $sql .= 'NULL';
                }
                
                if ($j < ($num_fields - 1)) {
                    $sql .= ',';
                }
            }
            $sql .= ");\n";
        }
        $sql .= "\n\n";
    }
    
    // Imposta gli header per il download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="backup_' . date('Y-m-d_H-i-s') . '.sql"');
    header('Content-Length: ' . strlen($sql));
    
    echo $sql;
    exit;
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'restore') {
    header('Content-Type: application/json');
    
    if (!isset($_FILES['backup_file']) || $_FILES['backup_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Errore durante il caricamento del file']);
        exit;
    }
    
    $file_tmp = $_FILES['backup_file']['tmp_name'];
    $file_content = file_get_contents($file_tmp);
    
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec($file_content);
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        echo json_encode(['success' => true, 'message' => 'Database ripristinato con successo']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Errore durante il ripristino del database: ' . $e->getMessage()]);
    }
}

