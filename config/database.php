<?php
/**
 * Configurazione e gestione della connessione al database
 * 
 * Questo file contiene le funzioni per la connessione al database
 * e le utility per operazioni comuni di query.
 */

/**
 * Restituisce una connessione al database (singleton)
 * 
 * @return PDO L'istanza di connessione al database
 */
function getDbConnection() {
    static $db = null;
    
    if ($db === null) {
        // Configurazione del database
        $db_host = '127.0.0.1:3306';
        $db_user = 'u427445037_coresuiteIT';
        $db_pass = 'Giogiu2123@';
        $db_name = 'u427445037_coresuiteIT';

// Connessione al database
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(                PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(                PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
            // Gestione dell'errore di connessione
            error_log("Errore di connessione al database: " . $e->getMessage());
            die("Impossibile connettersi al database. Contattare l'amministratore di sistema.");
        }
    }
    
    return $db;
}

/**
 * Esegue una query e restituisce tutti i risultati
 * 
 * @param string $sql La query SQL da eseguire
 * @param array $params I parametri da legare alla query
 * @return array I risultati della query
 */
function dbQuery($sql, $params = []) {
    $db = getDbConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Esegue una query e restituisce una singola riga
 * 
 * @param string $sql La query SQL da eseguire
 * @param array $params I parametri da legare alla query
 * @return array|false La riga di risultato o false se non trovata
 */
function dbQuerySingle($sql, $params = []) {
    $db = getDbConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetch();
}

/**
 * Esegue una query INSERT e restituisce l'ID dell'ultima riga inserita
 * 
 * @param string $sql La query SQL INSERT da eseguire
 * @param array $params I parametri da legare alla query
 * @return int L'ID dell'ultima riga inserita
 */
function dbInsert($sql, $params = []) {
    $db = getDbConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $db->lastInsertId();
}

/**
 * Esegue una query e restituisce il numero di righe interessate
 * 
 * @param string $sql La query SQL da eseguire
 * @param array $params I parametri da legare alla query
 * @return int Il numero di righe interessate
 */
function dbExecute($sql, $params = []) {
    $db = getDbConnection();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}
?>

