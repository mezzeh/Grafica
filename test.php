<?php
// Parametri di connessione
$host = "31.11.39.210";
$db_name = "Sql1853582_1";
$username = "Sql1853582";
$password = "Patriotta1.!";

// Abilita la visualizzazione di tutti gli errori
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>Test di connessione al database</h2>";
echo "<p>Tentativo di connessione con i seguenti parametri:</p>";
echo "<ul>";
echo "<li>Host: " . $host . "</li>";
echo "<li>Database: " . $db_name . "</li>";
echo "<li>Username: " . $username . "</li>";
echo "<li>Password: " . substr($password, 0, 3) . "..." . "</li>";
echo "</ul>";

echo "<h3>Test connessione con PDO:</h3>";
try {
    $startTime = microtime(true);
    $conn = new PDO("mysql:host=" . $host . ";dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000; // in milliseconds
    
    echo "<div style='color:green; font-weight:bold;'>Connessione PDO riuscita! (Tempo: " . number_format($executionTime, 2) . " ms)</div>";
    
    // Prova a eseguire una query semplice
    $stmt = $conn->query("SELECT 1");
    echo "<div style='color:green;'>Query di test eseguita con successo</div>";
    
    $conn = null; // chiudi connessione
} catch(PDOException $e) {
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000;
    echo "<div style='color:red; font-weight:bold;'>ERRORE PDO: " . $e->getMessage() . "</div>";
    echo "<div>Codice errore: " . $e->getCode() . "</div>";
    echo "<div>Tempo prima dell'errore: " . number_format($executionTime, 2) . " ms</div>";
}

echo "<h3>Test connessione con MySQLi:</h3>";
try {
    $startTime = microtime(true);
    $mysqli = new mysqli($host, $username, $password, $db_name);
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000;
    
    if ($mysqli->connect_error) {
        throw new Exception("Connect Error (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
    }
    
    echo "<div style='color:green; font-weight:bold;'>Connessione MySQLi riuscita! (Tempo: " . number_format($executionTime, 2) . " ms)</div>";
    
    // Prova a eseguire una query semplice
    $result = $mysqli->query("SELECT 1");
    if ($result) {
        echo "<div style='color:green;'>Query di test eseguita con successo</div>";
        $result->free();
    } else {
        echo "<div style='color:red;'>Errore nell'esecuzione della query di test</div>";
    }
    
    $mysqli->close();
} catch(Exception $e) {
    $endTime = microtime(true);
    $executionTime = ($endTime - $startTime) * 1000;
    echo "<div style='color:red; font-weight:bold;'>ERRORE MySQLi: " . $e->getMessage() . "</div>";
    echo "<div>Tempo prima dell'errore: " . number_format($executionTime, 2) . " ms</div>";
}

echo "<h3>Informazioni sulla configurazione PHP:</h3>";
echo "<ul>";
echo "<li>Versione PHP: " . phpversion() . "</li>";
echo "<li>Estensione PDO disponibile: " . (extension_loaded('pdo') ? 'Sì' : 'No') . "</li>";
echo "<li>Driver PDO MySQL disponibile: " . (extension_loaded('pdo_mysql') ? 'Sì' : 'No') . "</li>";
echo "<li>Estensione MySQLi disponibile: " . (extension_loaded('mysqli') ? 'Sì' : 'No') . "</li>";
echo "</ul>";

echo "<h3>Test connessione a MySQL con porta alternativa (3306):</h3>";
try {
    $conn = new PDO("mysql:host=" . $host . ";port=3306;dbname=" . $db_name, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div style='color:green; font-weight:bold;'>Connessione con porta 3306 riuscita!</div>";
    $conn = null;
} catch(PDOException $e) {
    echo "<div style='color:red; font-weight:bold;'>ERRORE con porta 3306: " . $e->getMessage() . "</div>";
}
?>