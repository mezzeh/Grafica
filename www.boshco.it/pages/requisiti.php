<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/requisito.php';
include_once '../models/esercizio.php';
include_once '../models/argomento.php'; // Aggiunto per la nuova funzionalità

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $message = "Problema di connessione al database.";
    $message_class = "error";
} else {
    // Istanza dei modelli
    $requisito = new Requisito($db);
    $esercizio = new Esercizio($db);
    $argomento = new Argomento($db); // Aggiunto per la nuova funzionalità
    
    // Se è stato selezionato un esercizio, mostra solo i requisiti di quell'esercizio
    $esercizio_id = isset($_GET['esercizio_id']) ? $_GET['esercizio_id'] : null;
    
    if ($esercizio_id) {
        $esercizio->id = $esercizio_id;
        $esercizio_info = $esercizio->readOne();
        if (!empty($esercizio_info)) {
            // Genera breadcrumb o informazioni sul contesto
            // ...
        }
    }

    // Includi handler per le operazioni CRUD
    include_once 'handlers/requisito_handler.php';

    // Mostra il messaggio se presente
    if (!empty($message)) {
        echo "<div class='message $message_class'>$message</div>";
    }
    
    // --- PRIMA MOSTRA LA LISTA DEI REQUISITI ESISTENTI ---
    echo "<div class='header-with-button'>";
    if ($esercizio_id) {
        echo "<h2>Requisiti dell'Esercizio: " . $esercizio_info['titolo'] . "</h2>";
    } else {
        echo "<h2>Tutti i Requisiti</h2>";
    }
    echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Requisito</button>";
    echo "</div>";
    
    // Leggi tutti i requisiti o i requisiti di un esercizio specifico
    if ($esercizio_id) {
        $stmt = $requisito->readByEsercizio($esercizio_id);
    } else {
        $stmt = $requisito->readAll();
    }
    
    // Conta i requisiti
    $num = $stmt->rowCount();
    
    if ($num > 0) {
        echo "<ul class='item-list'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            // Mostra esercizio solo se stiamo visualizzando tutti i requisiti
            $esercizio_info_display = isset($esercizio_titolo) ? "<div class='item-meta'>Esercizio: $esercizio_titolo</div>" : "";
            
            // Ottieni gli argomenti associati
            $argomenti_associati = $requisito->getAssociatedArgomenti($id);
            $argomenti_html = "";
            
            if ($argomenti_associati && $argomenti_associati->rowCount() > 0) {
                $argomenti_html = "<div class='item-argomenti'><strong>Argomenti correlati:</strong> ";
                $argomenti_list = array();
                
                while ($argomento_row = $argomenti_associati->fetch(PDO::FETCH_ASSOC)) {
                    $argomenti_list[] = "<a href='view_pages/view_argomento.php?id=" . $argomento_row['id'] . "'>" . 
                                       htmlspecialchars($argomento_row['titolo']) . "</a>";
                }
                
                $argomenti_html .= implode(", ", $argomenti_list);
                $argomenti_html .= "</div>";
            }
            
            echo "<li>
                    <div class='item-description'>" . htmlspecialchars($descrizione) . "</div>
                    $esercizio_info_display
                    $argomenti_html
                    <div class='item-actions'>
                        <a href='?edit=$id" . ($esercizio_id ? "&esercizio_id=$esercizio_id" : "") . "'>Modifica</a> | 
                        <a href='?delete=$id" . ($esercizio_id ? "&esercizio_id=$esercizio_id" : "") . "' onclick='return confirm(\"Sei sicuro di voler eliminare questo requisito?\");'>Elimina</a>
                    </div>
                </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nessun requisito trovato." . ($esercizio_id ? " Aggiungi requisiti per questo esercizio." : "") . "</p>";
    }
    
    // Includi i form per modificare/creare requisiti
    if (isset($_GET['edit'])) {
        include_once 'components/forms/edit_requisito.php';
    } else {
        include_once 'components/forms/create_requisito.php';
    }
}

// Includi footer
include_once '../ui/includes/footer.php';
?>