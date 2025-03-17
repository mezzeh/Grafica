<?php
ob_start();

// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/argomento.php';
include_once '../models/esame.php';

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
    $argomento = new Argomento($db);
    $esame = new Esame($db);
    
    if (empty($esame_info)) {
        echo "<div class='message error'>Esame non trovato.</div>";
        include_once '../ui/includes/footer.php';
        exit;
    }
}
// Includi handler per le operazioni CRUD
include_once 'handlers/argomento_handler.php';

// Includi breadcrumb
include_once 'components/shared/breadcrumb.php';

// Mostra messaggio
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
        
        echo "<li class='importance-{$livello_importanza}'>";
        echo "<div class='item-title'>" . htmlspecialchars($titolo) . "</div>";
        echo "<div class='item-meta'>Importanza: {$livello_importanza}</div>";
        echo "<div class='item-description'>" . htmlspecialchars($descrizione) . "</div>";
        
       echo "<div class='item-actions'>";
echo "<a href='view_pages/view_argomento.php?id={$id}'>Visualizza</a> | "; // Aggiungi questo link
echo "<a href='sottoargomenti.php?argomento_id={$id}'>Sottoargomenti</a>";

        // Azioni di modifica/eliminazione condizionali
        if ($esame_id && verificaPermessiPiano($db, $esame_id)) {
            echo " | <a href='?edit={$id}&esame_id={$esame_id}'>Modifica</a>";
            echo " | <a href='?delete={$id}&esame_id={$esame_id}' onclick='return confirm(\"Sei sicuro di voler eliminare questo argomento?\");'>Elimina</a>";
        }
    }

    // --- Gestione del form per creare un nuovo argomento ---
    if (isset($_POST['create'])) {
        $argomento->esame_id = $_POST['esame_id'];
        $argomento->titolo = $_POST['titolo'];
        $argomento->descrizione = $_POST['descrizione'];
        $argomento->livello_importanza = $_POST['livello_importanza'];

        if ($argomento->create()) {
            $message = "Argomento creato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile creare l'argomento.";
            $message_class = "error";
        }
    }

    // --- Gestione della modifica di un argomento ---
    if (isset($_POST['update'])) {
        $argomento->id = $_POST['id'];
        $argomento->esame_id = $_POST['esame_id'];
        $argomento->titolo = $_POST['titolo'];
        $argomento->descrizione = $_POST['descrizione'];
        $argomento->livello_importanza = $_POST['livello_importanza'];

        if ($argomento->update()) {
            $message = "Argomento aggiornato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile aggiornare l'argomento.";
            $message_class = "error";
        }
    }

    // --- Gestione della cancellazione di un argomento ---
    if (isset($_GET['delete'])) {
        $argomento->id = $_GET['delete'];
        if ($argomento->delete()) {
            $message = "Argomento eliminato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile eliminare l'argomento.";
            $message_class = "error";
        }
    }

    // Mostra il messaggio se presente
    if (!empty($message)) {
        echo "<div class='message $message_class'>$message</div>";
    }
    
    // --- PRIMA MOSTRA LA LISTA DEGLI ARGOMENTI ESISTENTI ---
    echo "<div class='header-with-button'>";
    if ($esame_id) {
        echo "<h2>Argomenti dell'Esame: " . $esame_info['nome'] . "</h2>";
    } else {
        echo "<h2>Tutti gli Argomenti</h2>";
    }
    echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Argomento</button>";
    echo "</div>";
    
    // Leggi tutti gli argomenti o gli argomenti di un esame specifico
    if ($esame_id) {
        $stmt = $argomento->readByEsame($esame_id);
    } else {
        $stmt = $argomento->readAll();
    }
    
    // Conta gli argomenti
    $num = $stmt->rowCount();
    
    if ($num > 0) {
        echo "<ul class='item-list'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            // Mostra esame solo se stiamo visualizzando tutti gli argomenti
            $esame_info_display = isset($esame_nome) ? "<div class='item-meta'>Esame: $esame_nome</div>" : "";
            
            echo "<li class='importance-$livello_importanza'>
                    <div class='item-title'>$titolo</div>
                    $esame_info_display
                    <div class='item-meta'>Importanza: $livello_importanza</div>
                    <div class='item-description'>$descrizione</div>
                    <div class='item-actions'>
                        <a href='sottoargomenti.php?argomento_id=$id'>Sottoargomenti</a> | 
                        <a href='?edit=$id" . ($esame_id ? "&esame_id=$esame_id" : "") . "'>Modifica</a> | 
                        <a href='?delete=$id" . ($esame_id ? "&esame_id=$esame_id" : "") . "' onclick='return confirm(\"Sei sicuro di voler eliminare questo argomento?\");'>Elimina</a>
                    </div>
                </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nessun argomento trovato." . ($esame_id ? " Aggiungi un argomento a questo esame." : "") . "</p>";
    }
    
    // --- POI MOSTRA I FORM DI MODIFICA/CREAZIONE ---
    
    // Form per modificare un argomento
    if (isset($_GET['edit'])) {
        $argomento->id = $_GET['edit'];
        if ($argomento->readOne()) {
            echo "<div id='editFormContainer'>";
            echo "<h2>Modifica Argomento</h2>";
            echo "<form action='' method='POST'>";
            echo "<input type='hidden' name='id' value='" . $argomento->id . "'>";
            
            // Carica tutti gli esami per il menu a tendina se non siamo in un contesto di esame specifico
            if (!$esame_id) {
                $stmt_esami = $esame->readAll();
                
                echo "<label for='esame_id'>Esame</label>";
                echo "<select name='esame_id' required>";
                
                while ($row_esame = $stmt_esami->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($argomento->esame_id == $row_esame['id']) ? "selected" : "";
                    echo "<option value='" . $row_esame['id'] . "' $selected>" . $row_esame['nome'] . "</option>";
                }
                
                echo "</select>";
            } else {
                echo "<input type='hidden' name='esame_id' value='$esame_id'>";
                echo "<div class='form-group'>";
                echo "<label>Esame</label>";
                echo "<div class='form-control-static'>" . $esame_info['nome'] . "</div>";
                echo "</div>";
            }
            
            echo "<label for='titolo'>Titolo Argomento</label>";
            echo "<input type='text' name='titolo' value='" . $argomento->titolo . "' required>";
            
            echo "<label for='descrizione'>Descrizione</label>";
            echo "<textarea name='descrizione'>" . $argomento->descrizione . "</textarea>";
            
            echo "<label for='livello_importanza'>Livello di Importanza (1-5)</label>";
            echo "<select name='livello_importanza'>";
            for ($i = 1; $i <= 5; $i++) {
                $selected = ($argomento->livello_importanza == $i) ? "selected" : "";
                $label = "";
                switch ($i) {
                    case 1: $label = "Molto importante"; break;
                    case 2: $label = "Importante"; break;
                    case 3: $label = "Media importanza"; break;
                    case 4: $label = "Poco importante"; break;
                    case 5: $label = "Marginale"; break;
                }
                echo "<option value='$i' $selected>$i - $label</option>";
            }
            echo "</select>";
            
            echo "<button type='submit' name='update'>Aggiorna Argomento</button>";
            echo "<a href='argomenti.php" . ($esame_id ? "?esame_id=$esame_id" : "") . "' class='btn-secondary'>Annulla</a>";
            echo "</form>";
            echo "</div>";
        }
    }
    
    // Form per creare un nuovo argomento (inizialmente nascosto)
    echo "<div id='createFormContainer' style='display: none;'>";
    echo "<h2>Crea Nuovo Argomento</h2>";
    echo "<form action='' method='POST'>";
    
    // Carica tutti gli esami per il menu a tendina
    if ($esame_id) {
        // Se siamo in una pagina di esame specifico, usa quell'esame
        echo "<input type='hidden' name='esame_id' value='$esame_id'>";
        echo "<div class='form-group'>";
        echo "<label>Esame</label>";
        echo "<div class='form-control-static'>" . $esame_info['nome'] . "</div>";
        echo "</div>";
    } else {
        // Altrimenti mostra il menu a tendina
        $stmt_esami = $esame->readAll();
        
        echo "<label for='esame_id'>Esame</label>";
        echo "<select name='esame_id' required>";
        
        while ($row_esame = $stmt_esami->fetch(PDO::FETCH_ASSOC)) {
            echo "<option value='" . $row_esame['id'] . "'>" . $row_esame['nome'] . "</option>";
        }
        
        echo "</select>";
    }
    
    echo "<label for='titolo'>Titolo Argomento</label>";
    echo "<input type='text' name='titolo' required>";
    
    echo "<label for='descrizione'>Descrizione</label>";
    echo "<textarea name='descrizione'></textarea>";
    
    echo "<label for='livello_importanza'>Livello di Importanza (1-5)</label>";
    echo "<select name='livello_importanza'>";
    for ($i = 1; $i <= 5; $i++) {
        $selected = ($i == 3) ? "selected" : ""; // Default: media importanza
        $label = "";
        switch ($i) {
            case 1: $label = "Molto importante"; break;
            case 2: $label = "Importante"; break;
            case 3: $label = "Media importanza"; break;
            case 4: $label = "Poco importante"; break;
            case 5: $label = "Marginale"; break;
        }
        echo "<option value='$i' $selected>$i - $label</option>";
    }
    echo "</select>";
    
    echo "<button type='submit' name='create'>Crea Argomento</button>";
    echo "<button type='button' id='cancelCreateBtn' class='btn-secondary'>Annulla</button>";
    echo "</form>";
    echo "</div>";
    
    // JavaScript per mostrare/nascondere il form di creazione
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const showCreateFormBtn = document.getElementById('showCreateFormBtn');
            const createFormContainer = document.getElementById('createFormContainer');
            const cancelCreateBtn = document.getElementById('cancelCreateBtn');
            
            if (showCreateFormBtn && createFormContainer) {
                showCreateFormBtn.addEventListener('click', function() {
                    createFormContainer.style.display = 'block';
                    showCreateFormBtn.style.display = 'none';
                });
            }
            
            if (cancelCreateBtn && createFormContainer && showCreateFormBtn) {
                cancelCreateBtn.addEventListener('click', function() {
                    createFormContainer.style.display = 'none';
                    showCreateFormBtn.style.display = 'inline-block';
                });
            }
        });
    </script>";
}

// Includi footer
include_once '../ui/includes/footer.php';
?>