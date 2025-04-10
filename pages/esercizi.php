<?php
// Attiva output buffering per prevenire errori di header già inviati
ob_start();

// Include path utilities
require_once dirname(__DIR__) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/esercizio.php');
include_once getAbsolutePath('models/sottoargomento.php');
include_once getAbsolutePath('models/argomento.php');
include_once getAbsolutePath('models/esame.php');
include_once getAbsolutePath('models/formula.php'); // Aggiunto per il collegamento alle formule

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once getAbsolutePath('ui/includes/footer.php');
    exit;
}

// Inizializza modelli
$esercizio = new Esercizio($db);
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$esame = new Esame($db);
$formula = new Formula($db); // Aggiunto per il collegamento alle formule

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
include_once getAbsolutePath('pages/handlers/esercizio_handler.php');

// Includi breadcrumb
include_once getAbsolutePath('pages/components/shared/breadcrumb.php');

// Genera il breadcrumb
if ($sottoargomento_id && $sottoargomento_info) {
    $breadcrumb_items = [
        ['text' => 'Home', 'link' => getUrlPath('pages/index.php')],
        ['text' => $esame_info['nome'], 'link' => getUrlPath('pages/view_pages/view_esame.php?id=' . $esame_info['id'])],
        ['text' => $argomento_info['titolo'], 'link' => getUrlPath('pages/view_pages/view_argomento.php?id=' . $argomento_info['id'])],
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
        
        // Ottieni le formule associate
        $formule_associate = $esercizio->getAssociatedFormule($id);
        $formule_html = "";
        
        if ($formule_associate && $formule_associate->rowCount() > 0) {
            $formule_html = "<div class='item-formule'><strong>Formule associate:</strong> ";
            $formule_list = array();
            
            while ($formula_row = $formule_associate->fetch(PDO::FETCH_ASSOC)) {
                $formule_list[] = "<a href='" . getUrlPath('pages/view_pages/view_formula.php?id=' . $formula_row['id']) . "'>" . 
                                 htmlspecialchars($formula_row['nome']) . "</a>";
            }
            
            $formule_html .= implode(", ", $formule_list);
            $formule_html .= "</div>";
        }
        
        echo "<li class='$difficolta_class'>
                <div class='item-title'>" . htmlspecialchars($titolo) . "</div>
                $sottoargomento_info_display
                <div class='item-meta'>Difficoltà: $difficolta_text</div>
                <div class='item-description'>
                    <strong>Testo:</strong><br>
                    " . nl2br(htmlspecialchars(substr($testo, 0, 200))) . (strlen($testo) > 200 ? "..." : "") . "
                </div>
                $formule_html
                <div class='item-actions'>
                    <a href='" . getUrlPath('pages/view_pages/view_esercizio.php?id=' . $id) . "'>Visualizza</a> | 
                    <a href='" . getUrlPath('pages/requisiti.php?esercizio_id=' . $id) . "'>Requisiti</a> |
                    <a href='" . getUrlPath('pages/esercizio_formule.php?esercizio_id=' . $id) . "'>Gestisci Formule</a>";
        
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
    include_once getAbsolutePath('pages/components/forms/edit_esercizio.php');
} else {
    include_once getAbsolutePath('pages/components/forms/create_esercizio.php');
}

include_once getAbsolutePath('ui/includes/footer.php');
ob_end_flush();
?>