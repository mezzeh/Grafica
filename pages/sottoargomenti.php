<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/sottoargomento.php';
include_once '../models/argomento.php';
include_once '../models/esame.php';

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Inizializza modelli
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$esame = new Esame($db);

// Parametri GET
$argomento_id = isset($_GET['argomento_id']) ? $_GET['argomento_id'] : null;

// Carica informazioni sull'argomento se specificato
$argomento_info = null;
if ($argomento_id) {
    $argomento->id = $argomento_id;
    $argomento_info = $argomento->readOne();
    
    if ($argomento_info) {
        // Carica informazioni sull'esame
        $esame->id = $argomento_info['esame_id'];
        $esame_info = $esame->readOne();
    }
}

// Includi handler per le operazioni CRUD
include_once 'handlers/sottoargomento_handler.php';

// Includi breadcrumb
include_once 'components/shared/breadcrumb.php';

// Genera il breadcrumb
if ($argomento_id && $argomento_info) {
    $breadcrumb_items = [
        ['text' => 'Piani di Studio', 'link' => 'index.php'],
        ['text' => 'Esami', 'link' => 'esami.php?piano_id=' . $esame_info['piano_id']],
        ['text' => $esame_info['nome'], 'link' => 'argomenti.php?esame_id=' . $esame_info['id']],
        ['text' => $argomento_info['titolo']]
    ];
    generaBreadcrumb($breadcrumb_items);
}

// Mostra messaggio
if (!empty($message)) {
    echo "<div class='message {$message_class}'>{$message}</div>";
}

// Intestazione pagina
echo "<div class='header-with-button'>";
if ($argomento_id && $argomento_info) {
    echo "<h2>Sottoargomenti di: " . htmlspecialchars($argomento_info['titolo']) . "</h2>";
} else {
    echo "<h2>Tutti i Sottoargomenti</h2>";
}

// Pulsante di aggiunta
if (isset($_SESSION['user_id'])) {
    echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Sottoargomento</button>";
}
echo "</div>";

// Recupera sottoargomenti
$stmt = $argomento_id ? $sottoargomento->readByArgomento($argomento_id) : $sottoargomento->readAll();
$num = $stmt->rowCount();

// Visualizzazione sottoargomenti
if ($num > 0) {
    echo "<ul class='item-list'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // Mostra argomento solo se stiamo visualizzando tutti i sottoargomenti
        $argomento_info_display = isset($argomento_titolo) ? 
            "<div class='item-meta'>Argomento: " . htmlspecialchars($argomento_titolo) . "</div>" : "";
        
        echo "<li class='depth-{$livello_profondita}'>
                <div class='item-title'>" . htmlspecialchars($titolo) . "</div>
                {$argomento_info_display}
                <div class='item-meta'>Livello di profondità: {$livello_profondita}</div>
                <div class='item-description'>" . htmlspecialchars($descrizione) . "</div>
                <div class='item-actions'>
                    <a href='view_pages/view_sottoargomento.php?id={$id}'>Visualizza</a> | 
                    <a href='esercizi.php?sottoargomento_id={$id}'>Esercizi</a>";
        
        // Azioni di modifica/eliminazione
        if (isset($_SESSION['user_id'])) {
            echo " | <a href='?edit={$id}" . ($argomento_id ? "&argomento_id={$argomento_id}" : "") . "'>Modifica</a>";
            echo " | <a href='?delete={$id}" . ($argomento_id ? "&argomento_id={$argomento_id}" : "") . "' onclick='return confirm(\"Sei sicuro di voler eliminare questo sottoargomento?\");'>Elimina</a>";
        }
        
        echo "</div></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nessun sottoargomento trovato." . ($argomento_id ? " Aggiungi un sottoargomento a questo argomento." : "") . "</p>";
}

// Includi i form
if (isset($_GET['edit'])) {
    include_once 'components/forms/edit_sottoargomento.php';
} else {
    include_once 'components/forms/create_sottoargomento.php';
}

include_once '../ui/includes/footer.php';
?>