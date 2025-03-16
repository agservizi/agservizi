<?php
// Database configuration
// Sostituire le credenziali del database con quelle fornite
$db_host = '127.0.0.1:3306';
$db_user = 'u427445037_agservizi';
$db_password = 'Giogiu2123@';
$db_name = 'u427445037_agservizi';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>

