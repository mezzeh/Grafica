<?php
ob_start();

// Include path utilities
require_once dirname(__DIR__) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/argomento.php');
include_once getAbsolutePath('models/esame.php');
include_once getAbsolutePath('models/piano_di_studio.php');

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Parametri GET
$esame_id = isset($_GET['esame_id']) ? $_GET['esame_id'] : null;
$edit_id = isset($_GET['edit']) ? $_GET['edit'] : null;

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $message = "Problema di connessione al database.";
    $message_class = "error";
    echo "<div class='message {$message_class}'>{$message}</div>";
    include_once getAbsolutePath('ui/includes/footer.php');
    exit;
}

// Inizializza modelli
$argomento = new Argomento($db);
$esame = new Esame($db);
$piano = new PianoDiStudio($db);

// Carica le informazioni sull'esame se specificato
$esame_info = null;
if ($esame_id) {
    $esame->id = $esame_id;
    $esame_info = $esame->readOne();
    
    if (empty($esame_info)) {
        echo "<div class='message error'>Esame non trovato.</div>";
        include_once getAbsolutePath('ui/includes/footer.php');
        exit;
    }
    
    // Carica informazioni sul piano per verificare permessi
    $piano->id = $esame_info['piano_id'];
    $piano_info = $piano->readOne();
}

// Includi handler per le operazioni CRUD
include_once getAbsolutePath('pages/handlers/argomento_handler.php');

// Includi breadcrumb
include_once getAbsolutePath('pages/components/shared/breadcrumb.php');

// Genera il breadcrumb se siamo in un contesto di esame
if ($esame_id && $esame_info) {
    $breadcrumb_items = [
        ['text' => 'Home', 'link' => getUrlPath('pages/index.php')],
        ['text' => 'Esami', 'link' => getUrlPath('pages/esami.php?piano_id=' . $esame_info['piano_id'])],
        ['text' => $esame_info['nome']]
    ];
    generaBreadcrumb($breadcrumb_items);
}

// Mostra messaggio se presente
if (!empty($message)) {
    echo "<div class='message {$message_class}'>{$message}</div>";
}

// Intestazione pagina
echo "<div class='header-with-button'>";
echo "<h2>Argomenti" . ($esame_id ? " dell'Esame: " . htmlspecialchars($esame_info['nome']) : "") . "</h2>";

// Pulsante di aggiunta condizionale
if ($esame_id && verificaPermessiPiano($db, $esame_id)) {
    echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Argomento</button>";
}
echo "</div>";

// Recupera argomenti
$stmt = $esame_id ? $argomento->readByEsame($esame_id) : $argomento->readAll();
$num = $stmt->rowCount();

// Visualizzazione argomenti
if ($num > 0) {
    echo "<ul class='item-list'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // Mostra esame solo se stiamo visualizzando tutti gli argomenti
        $esame_info_display = isset($esame_nome) ? 
            "<div class='item-meta'>Esame: " . htmlspecialchars($esame_nome) . "</div>" : "";
        
        echo "<li class='importance-{$livello_importanza}'>";
        echo "<div class='item-title'>" . htmlspecialchars($titolo) . "</div>";
        echo $esame_info_display;
        echo "<div class='item-meta'>Importanza: {$livello_importanza}</div>";
        echo "<div class='item-description'>" . htmlspecialchars($descrizione) . "</div>";
        
        echo "<div class='item-actions'>";
        echo "<a href='" . getUrlPath('pages/view_pages/view_argomento.php?id=' . $id) . "'>Visualizza</a> | ";
        echo "<a href='" . getUrlPath('pages/sottoargomenti.php?argomento_id=' . $id) . "'>Sottoargomenti</a>";

        // Azioni di modifica/eliminazione condizionali
        if ($esame_id && verificaPermessiPiano($db, $esame_id)) {
            echo " | <a href='?edit={$id}&esame_id={$esame_id}'>Modifica</a>";
            echo " | <a href='?delete={$id}&esame_id={$esame_id}' onclick='return confirm(\"Sei sicuro di voler eliminare questo argomento?\");'>Elimina</a>";
        }
        
        echo "</div></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nessun argomento trovato." . ($esame_id ? " Aggiungi un argomento a questo esame." : "") . "</p>";
}

// Includi i form
if (isset($_GET['edit'])) {
    include_once getAbsolutePath('pages/components/forms/edit_argomento.php');
} else {
    include_once getAbsolutePath('pages/components/forms/create_argomento.php');
}

// Includi footer
include_once getAbsolutePath('ui/includes/footer.php');
?>