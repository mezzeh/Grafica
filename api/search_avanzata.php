<?php
/**
 * API per la ricerca avanzata
 * Restituisce risultati in formato JSON per l'autocompletamento
 */

// Includi file di configurazione con percorsi corretti
include_once '../config/database.php';
include_once '../models/argomento.php';
include_once '../models/sottoargomento.php';
include_once '../models/esercizio.php';

// Abilita CORS per le chiamate AJAX
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Verifica se è stata inviata una query di ricerca e un tipo
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? trim($_GET['type']) : 'all';

// Se la query è vuota, restituisci un array vuoto
if (empty($query)) {
    echo json_encode([]);
    exit;
}

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(["error" => "Problema di connessione al database"]);
    exit;
}

// Array per i risultati della ricerca
$results = [];

// Ricerca in base al tipo richiesto
switch ($type) {
    case 'argomento':
        // Ricerca solo in argomenti
        $argomento = new Argomento($db);
        $stmt = $argomento->search($query);
        
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'id' => $row['id'],
                    'type' => 'argomento',
                    'name' => isset($row['titolo']) ? $row['titolo'] : $row['nome'],
                    'description' => substr($row['descrizione'], 0, 100) . '...'
                ];
            }
        }
        break;
        
    case 'sottoargomento':
        // Ricerca solo in sottoargomenti
        $sottoargomento = new SottoArgomento($db);
        $stmt = $sottoargomento->search($query);
        
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'id' => $row['id'],
                    'type' => 'sottoargomento',
                    'name' => isset($row['titolo']) ? $row['titolo'] : $row['nome'],
                    'description' => substr($row['descrizione'], 0, 100) . '...'
                ];
            }
        }
        break;
        
    case 'esercizio':
        // Ricerca solo in esercizi
        $esercizio = new Esercizio($db);
        $stmt = $esercizio->search($query);
        
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'id' => $row['id'],
                    'type' => 'esercizio',
                    'name' => $row['titolo'],
                    'description' => substr($row['testo'], 0, 100) . '...'
                ];
            }
        }
        break;
        
    default:
        // Ricerca in tutti i tipi
        
        // Ricerca in argomenti
        $argomento = new Argomento($db);
        $stmt = $argomento->search($query);
        
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'id' => $row['id'],
                    'type' => 'argomento',
                    'name' => isset($row['titolo']) ? $row['titolo'] : $row['nome'],
                    'description' => substr($row['descrizione'], 0, 100) . '...'
                ];
            }
        }
        
        // Ricerca in sottoargomenti
        $sottoargomento = new SottoArgomento($db);
        $stmt = $sottoargomento->search($query);
        
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'id' => $row['id'],
                    'type' => 'sottoargomento',
                    'name' => isset($row['titolo']) ? $row['titolo'] : $row['nome'],
                    'description' => substr($row['descrizione'], 0, 100) . '...'
                ];
            }
        }
        
        // Ricerca in esercizi
        $esercizio = new Esercizio($db);
        $stmt = $esercizio->search($query);
        
        if ($stmt && $stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = [
                    'id' => $row['id'],
                    'type' => 'esercizio',
                    'name' => $row['titolo'],
                    'description' => substr($row['testo'], 0, 100) . '...'
                ];
            }
        }
}

// Limita il numero di risultati a 10
$results = array_slice($results, 0, 10);

// Restituisci i risultati in formato JSON
echo json_encode($results);
?>