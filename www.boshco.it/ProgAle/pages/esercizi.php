<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/esercizio.php';
include_once '../models/sottoargomento.php';
include_once '../models/argomento.php';
include_once '../models/esame.php';
include_once '../models/esercizio_correlato.php';
include_once '../models/comments.php';
include_once 'components/comments/comments.php';

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Inizializza modelli
$esercizio = new Esercizio($db);
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$esame = new Esame($db);

// Parametri GET
$sottoargomento_id = isset($_GET['sottoargomento_id']) ? $_GET['sottoargomento_id'] : null;

// Carica informazioni del sottoargomento se specificato
$sottoargomento_info = null;
if ($sottoargomento_id) {
    $sottoargomento->id = $sottoargomento_id;
    $sottoargomento_info = $sottoargomento->readOne();
    
    if ($sottoargomento_info) {
        // Carica informazioni sull'argomento
        $argomento->id = $sottoargomento_info['argomento_id'];
        $argomento_info = $argomento->readOne();
        
        // Carica informazioni sull'esame
        $esame->id = $argomento_info['esame_id'];
        $esame_info = $esame->readOne();
    }
}

// Includi handler per le operazioni CRUD
include_once 'handlers/esercizio_handler.php';

// Includi breadcrumb
include_once 'components/shared/breadcrumb.php';

// Genera il breadcrumb
if ($sottoargomento_id && $sottoargomento_info) {
    $breadcrumb_items = [
        ['text' => 'Home', 'link' => 'index.php'],
        ['text' => $esame_info['nome'], 'link' => 'view_esame.php?id=' . $esame_info['id']],
        ['text' => $argomento_info['titolo'], 'link' => 'view_argomento.php?id=' . $argomento_info['id']],
        ['text' => $sottoargomento_info['titolo']]
    ];
    generaBreadcrumb($breadcrumb_items);
}

// Mostra messaggio
if (!empty($message)) {
    echo "<div class='message {$message_class}'>{$message}</div>";
}

// Intestazione pagina
echo "<div class='header-with-button'>";
if ($sottoargomento_id && $sottoargomento_info) {
    echo "<h2>Esercizi di: " . htmlspecialchars($sottoargomento_info['titolo']) . "</h2>";
} else {
    echo "<h2>Tutti gli Esercizi</h2>";
}

// Pulsante di aggiunta
if (isset($_SESSION['user_id'])) {
    echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Esercizio</button>";
}
echo "</div>";

// Recupera esercizi
$stmt = $sottoargomento_id ? $esercizio->readBySottoArgomento($sottoargomento_id) : $esercizio->readAll();
$num = $stmt->rowCount();

// Visualizzazione esercizi
if ($num > 0) {
    echo "<ul class='item-list'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        
        // Mostra sottoargomento solo se stiamo visualizzando tutti gli esercizi
        $sottoargomento_info_display = isset($sottoargomento_titolo) ? 
            "<div class='item-meta'>Sottoargomento: " . htmlspecialchars($sottoargomento_titolo) . "</div>" : "";
        
        // Determina la classe CSS in base alla difficoltà
        $difficolta_class = "difficulty-$difficolta";
        $difficolta_text = ($difficolta == 1) ? "Facile" : (($difficolta == 2) ? "Media" : "Difficile");
        
        echo "<li class='$difficolta_class'>
                <div class='item-title'>" . htmlspecialchars($titolo) . "</div>
                $sottoargomento_info_display
                <div class='item-meta'>Difficoltà: $difficolta_text</div>
                <div class='item-description'>
                    <strong>Testo:</strong><br>
                    " . nl2br(htmlspecialchars(substr($testo, 0, 200))) . (strlen($testo) > 200 ? "..." : "") . "
                </div>
                <div class='item-actions'>
                    <a href='view_pages/view_esercizio.php?id=$id'>Visualizza</a> | 
                    <a href='requisiti.php?esercizio_id=$id'>Requisiti</a>";
        
        // Azioni di modifica/eliminazione
        if (isset($_SESSION['user_id'])) {
            echo " | <a href='?edit=$id" . ($sottoargomento_id ? "&sottoargomento_id=$sottoargomento_id" : "") . "'>Modifica</a>";
            echo " | <a href='?delete=$id" . ($sottoargomento_id ? "&sottoargomento_id=$sottoargomento_id" : "") . "' onclick='return confirm(\"Sei sicuro di voler eliminare questo esercizio?\");'>Elimina</a>";
        }
        
        echo "</div></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nessun esercizio trovato." . ($sottoargomento_id ? " Aggiungi un esercizio a questo sottoargomento." : "") . "</p>";
}

// Includi i form
if (isset($_GET['edit'])) {
    include_once 'components/forms/edit_esercizio.php';
} else {
    include_once 'components/forms/create_esercizio.php';
}

include_once '../ui/includes/footer.php';
?>