<?php
/**
 * API per la ricerca in tempo reale
 * Restituisce i risultati filtrati in base alla query inserita
 */

// Includi il file di configurazione dei percorsi
require_once __DIR__ . '/../config/paths.php';

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/piano_di_studio.php');
include_once getAbsolutePath('models/esame.php');
include_once getAbsolutePath('models/argomento.php');
include_once getAbsolutePath('models/sottoargomento.php');
include_once getAbsolutePath('models/esercizio.php');

// Abilita CORS per le chiamate AJAX
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Verifica se è stata inviata una query di ricerca
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

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

// Cerca nei piani di studio
$piano = new PianoDiStudio($db);
$stmt = $piano->search($query);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'id' => $row['id'],
            'type' => 'piano',
            'name' => $row['nome'],
            'url' => getUrlPath('pages/view_pages/view_piano.php?id=' . $row['id']),
            'description' => substr($row['descrizione'], 0, 100) . '...'
        ];
    }
}

// Cerca negli esami
$esame = new Esame($db);
$stmt = $esame->search($query);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'id' => $row['id'],
            'type' => 'esame',
            'name' => $row['nome'],
            'url' => getUrlPath('pages/view_pages/view_esame.php?id=' . $row['id']),
            'description' => 'Codice: ' . $row['codice'] . ', Crediti: ' . $row['crediti']
        ];
    }
}

// Cerca negli argomenti
$argomento = new Argomento($db);
$stmt = $argomento->search($query);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'id' => $row['id'],
            'type' => 'argomento',
            'name' => $row['titolo'],
            'url' => getUrlPath('pages/view_pages/view_argomento.php?id=' . $row['id']),
            'description' => substr($row['descrizione'], 0, 100) . '...'
        ];
    }
}

// Cerca nei sottoargomenti
$sottoargomento = new SottoArgomento($db);
$stmt = $sottoargomento->search($query);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'id' => $row['id'],
            'type' => 'sottoargomento',
            'name' => $row['titolo'],
            'url' => getUrlPath('pages/view_pages/view_sottoargomento.php?id=' . $row['id']),
            'description' => substr($row['descrizione'], 0, 100) . '...'
        ];
    }
}

// Cerca negli esercizi
$esercizio = new Esercizio($db);
$stmt = $esercizio->search($query);

if ($stmt && $stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $results[] = [
            'id' => $row['id'],
            'type' => 'esercizio',
            'name' => $row['titolo'],
            'url' => getUrlPath('pages/view_pages/view_esercizio.php?id=' . $row['id']),
            'description' => substr($row['testo'], 0, 100) . '...'
        ];
    }
}

// Limita il numero di risultati a 10
$results = array_slice($results, 0, 10);

// Restituisci i risultati in formato JSON
echo json_encode($results);
?>