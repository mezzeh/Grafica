<?php
$host = "31.11.39.210";
$db_name = "Sql1853582_1";
$username = "Sql1853582";
$password = "A5qSUd5JM94t-.E";

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connessione riuscita!";
} catch (PDOException $e) {
    echo "Errore di connessione: " . $e->getMessage();
}
?>