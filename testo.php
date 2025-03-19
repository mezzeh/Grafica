<?php echo "Test Aruba - " . date("Y-m-d H:i:s");

 $host = "31.11.39.210";
 $db_name = "Sql1853582_1";
 $user = "Sql1853582";
 $password = "A5qSUd5JM94t-.E";
$conn = new mysqli($host, $user, $password, $db_name);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
 ?>