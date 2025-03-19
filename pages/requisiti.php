<?php
// Include path utilities
require_once dirname(__DIR__) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/requisito.php');
include_once getAbsolutePath('models/esercizio.php');
include_once getAbsolutePath('models/argomento.php'); // Aggiunto per la nuova funzionalità

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
    include_once getAbsolutePath('pages/handlers/requisito_handler.php');

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
                    $argomenti_list[] = "<a href='" . getUrlPath('pages/view_pages/view_argomento.php?id=' . $argomento_row['id']) . "'>" . 
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
    
    // --- Aggiunta del nuovo form di creazione con autocompletamento ---
    if (!isset($_GET['edit'])) {
?>
<div id='createFormContainer' style='display: none;'>
    <h2>Crea Nuovo Requisito</h2>
    <form action='' method='POST'>
        <input type='hidden' name='esercizio_id' value='<?php echo $esercizio_id; ?>'>
        
        <label for='descrizione'>Descrizione del Requisito</label>
        <textarea name='descrizione' rows='4' required></textarea>
        
        <!-- Selezione degli argomenti correlati (implementata con autocompletamento) -->
        <label for="argomenti_search">Cerca Argomenti da Collegare</label>
        <div class="search-container">
            <input type="text" 
                   id="argomenti_search" 
                   class="requisito-search-input" 
                   placeholder="Inizia a digitare per cercare un argomento..." 
                   data-type="argomento"
                   data-target="argomento_selezionato">
            <input type="hidden" name="argomento_selezionato" id="argomento_selezionato">
        </div>
        
        <div class="selected-prerequisites-container">
            <label>Argomenti Selezionati</label>
            <div id="argomenti_selezionati" class="selected-tags">
                <!-- Gli argomenti selezionati appariranno qui -->
            </div>
            <input type="hidden" name="argomenti_ids" id="argomenti_ids" value="[]">
        </div>
        
        <button type='submit' name='create'>Crea Requisito</button>
        <button type='button' id='cancelCreateBtn' class='btn-secondary'>Annulla</button>
    </form>
</div>

<!-- Script per gestire la selezione multipla di argomenti -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Array per tenere traccia degli argomenti selezionati
    let argomentiSelezionati = [];
    
    // Elementi del DOM
    const argomentiSearch = document.getElementById('argomenti_search');
    const argomentiSelezionatiContainer = document.getElementById('argomenti_selezionati');
    const argomentiIdsInput = document.getElementById('argomenti_ids');
    const argomentoSelezionatoInput = document.getElementById('argomento_selezionato');
    
    // Funzione per aggiungere un argomento selezionato
    function aggiungiArgomento(id, nome, tipo) {
        // Verifica se l'argomento è già stato selezionato
        if (!argomentiSelezionati.some(arg => arg.id === id)) {
            // Aggiungi l'argomento all'array
            argomentiSelezionati.push({id: id, nome: nome, tipo: tipo});
            
            // Aggiorna l'input nascosto con gli ID
            argomentiIdsInput.value = JSON.stringify(argomentiSelezionati.map(arg => arg.id));
            
            // Crea un elemento tag per l'argomento
            const tagElement = document.createElement('div');
            tagElement.className = 'selected-tag';
            tagElement.dataset.id = id;
            tagElement.innerHTML = `
                <span>${nome}</span>
                <span class="tag-type">(${tipo})</span>
                <button type="button" class="remove-tag" data-id="${id}">×</button>
            `;
            
            // Aggiungi il tag al container
            argomentiSelezionatiContainer.appendChild(tagElement);
            
            // Pulisci il campo di ricerca
            argomentiSearch.value = '';
            argomentoSelezionatoInput.value = '';
        }
    }
    
    // Ascoltatore per l'input nascosto che viene aggiornato dall'autocompletamento
    if (argomentoSelezionatoInput) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    const valore = argomentoSelezionatoInput.value;
                    if (valore) {
                        const parti = valore.split('|');
                        if (parti.length === 2) {
                            const tipo = parti[0];
                            const id = parti[1];
                            const nome = argomentiSearch.value;
                            
                            aggiungiArgomento(id, nome, tipo);
                        }
                    }
                }
            });
        });
        
        observer.observe(argomentoSelezionatoInput, { attributes: true });
    }
    
    // Ascoltatore per rimuovere i tag
    argomentiSelezionatiContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            const id = e.target.dataset.id;
            
            // Rimuovi il tag dal DOM
            const tagElement = e.target.closest('.selected-tag');
            if (tagElement) {
                tagElement.remove();
            }
            
            // Rimuovi l'argomento dall'array
            argomentiSelezionati = argomentiSelezionati.filter(arg => arg.id !== id);
            
            // Aggiorna l'input nascosto
            argomentiIdsInput.value = JSON.stringify(argomentiSelezionati.map(arg => arg.id));
        }
    });
});
</script>

<?php
    } else {
        // Se siamo in modalità modifica, includi il form di modifica esistente
        include_once getAbsolutePath('pages/components/forms/edit_requisito.php');
    }
}

// Aggiungi il riferimento al JavaScript per l'autocompletamento
echo '<script src="' . getUrlPath('ui/js/autocomplete_requisiti.js') . '"></script>';

// Includi footer
include_once getAbsolutePath('ui/includes/footer.php');
?>