<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
echo "dioca";
//header("Location: app/pages/index.php");
echo "Test Aruba - " . date("Y-m-d H:i:s");

$host = "31.11.39.210";
$db_name = "Sql1853582_1";
$username = "Sql1853582";
$password = "A5qSUd5JM94t-.E";

// Correzione: usa le variabili corrette per la connessione al database
$conn = new mysqli($host, $username, $password, $db_name);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

echo "Connessione riuscita!";
?>
