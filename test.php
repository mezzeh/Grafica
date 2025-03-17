<?php
$host = 'localhost';         // Cambiare se richiesto da Aruba
$user = 'nome_utente_db';    // Nome utente MySQL
$password = 'password_db';   // Password del database
$database = 'nome_database'; // Nome del database

$conn = new mysqli($host, $user, $password, $database);

// Verifica la connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
