<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/formula.php';
include_once '../models/argomento.php';

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
    $formula = new Formula($db);
    $argomento = new Argomento($db);

    // --- Gestione del form per creare una nuova formula ---
    if (isset($_POST['create'])) {
        $formula->nome = $_POST['nome'];
        $formula->espressione = $_POST['espressione'];
        $formula->descrizione = $_POST['descrizione'];
        
        // Gestione dell'immagine se caricata
        if(isset($_FILES['immagine']) && $_FILES['immagine']['size'] > 0) {
            $target_dir = "uploads/formule/";
            
            // Crea la directory se non esiste
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid() . "." . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
                $formula->immagine = $target_file;
            } else {
                $message = "Errore nel caricamento dell'immagine.";
                $message_class = "error";
            }
        }

        if ($formula->create()) {
            $message = "Formula creata con successo!";
            $message_class = "success";
            
            // Se sono stati selezionati degli argomenti, associali alla formula
            if(isset($_POST['argomenti']) && is_array($_POST['argomenti'])) {
                foreach($_POST['argomenti'] as $argomento_id) {
                    $formula->addArgomento($formula->id, $argomento_id);
                }
            }
        } else {
            $message = "Impossibile creare la formula.";
            $message_class = "error";
        }
    }

    // --- Gestione della modifica di una formula ---
    if (isset($_POST['update'])) {
        $formula->id = $_POST['id'];
        $formula->nome = $_POST['nome'];
        $formula->espressione = $_POST['espressione'];
        $formula->descrizione = $_POST['descrizione'];
        
        // Gestione dell'immagine se caricata
        if(isset($_FILES['immagine']) && $_FILES['immagine']['size'] > 0) {
            $target_dir = "uploads/formule/";
            
            // Crea la directory se non esiste
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $file_extension = pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION);
            $filename = uniqid() . "." . $file_extension;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
                // Elimina la vecchia immagine se esiste
                if(!empty($formula->immagine) && file_exists($formula->immagine)) {
                    unlink($formula->immagine);
                }
                $formula->immagine = $target_file;
            } else {
                $message = "Errore nel caricamento dell'immagine.";
                $message_class = "error";
            }
        }

        if ($formula->update()) {
            $message = "Formula aggiornata con successo!";
            $message_class = "success";
            
            // Aggiorna le associazioni con gli argomenti
            $formula->removeAllArgomenti($formula->id);
            if(isset($_POST['argomenti']) && is_array($_POST['argomenti'])) {
                foreach($_POST['argomenti'] as $argomento_id) {
                    $formula->addArgomento($formula->id, $argomento_id);
                }
            }
        } else {
            $message = "Impossibile aggiornare la formula.";
            $message_class = "error";
        }
    }

    // --- Gestione della cancellazione di una formula ---
    if (isset($_GET['delete'])) {
        $formula->id = $_GET['delete'];
        // Recupera il percorso dell'immagine per eliminarla
        $formula->readOne();
        
        if ($formula->delete()) {
            // Elimina l'immagine se esiste
            if(!empty($formula->immagine) && file_exists($formula->immagine)) {
                unlink($formula->immagine);
            }
            $message = "Formula eliminata con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile eliminare la formula.";
            $message_class = "error";
        }
    }

    // Mostra il messaggio se presente
    if (!empty($message)) {
        echo "<div class='message $message_class'>$message</div>";
    }
    
    // --- PRIMA MOSTRA LA LISTA DELLE FORMULE ESISTENTI ---
    echo "<div class='header-with-button'>";
    echo "<h2>Gestione Formule</h2>";
    echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuova Formula</button>";
    echo "</div>";
    
    // Leggi tutte le formule
    $stmt = $formula->readAll();
    
    // Conta le formule
    $num = $stmt->rowCount();
    
    if ($num > 0) {
        echo "<div class='formula-grid'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            echo "<div class='formula-box'>";
            echo "<div class='formula-name'>$nome</div>";
            echo "<div class='formula-expression'>$espressione</div>";
            
            if (!empty($descrizione)) {
                echo "<div class='formula-description'>$descrizione</div>";
            }
            
            if (!empty($immagine) && file_exists($immagine)) {
                echo "<div class='formula-image'>";
                echo "<img src='$immagine' alt='Immagine formula' style='max-width:100%;'>";
                echo "</div>";
            }
            
            // Ottieni gli argomenti associati a questa formula
            $argomenti_associati = $formula->getAssociatedArgomenti($id);
            if ($argomenti_associati && $argomenti_associati->rowCount() > 0) {
                echo "<div class='formula-argomenti'>";
                echo "<strong>Argomenti associati:</strong> ";
                $argomenti_list = array();
                while ($argomento_row = $argomenti_associati->fetch(PDO::FETCH_ASSOC)) {
                    $argomenti_list[] = $argomento_row['titolo'];
                }
                echo implode(", ", $argomenti_list);
                echo "</div>";
            }
            
            echo "<div class='item-actions'>";
            echo "<a href='?edit=$id'>Modifica</a> | ";
            echo "<a href='?delete=$id' onclick='return confirm(\"Sei sicuro di voler eliminare questa formula?\");'>Elimina</a>";
            echo "</div>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>Nessuna formula trovata.</p>";
    }
    
    // --- POI MOSTRA I FORM DI MODIFICA/CREAZIONE ---
    
    // Form per modificare una formula
    if (isset($_GET['edit'])) {
        $formula->id = $_GET['edit'];
        if ($formula->readOne()) {
            echo "<div id='editFormContainer'>";
            echo "<h2>Modifica Formula</h2>";
            echo "<form action='' method='POST' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='id' value='" . $formula->id . "'>";
            
            echo "<label for='nome'>Nome della Formula</label>";
            echo "<input type='text' name='nome' value='" . $formula->nome . "' required>";
            
            echo "<label for='espressione'>Espressione</label>";
            echo "<textarea name='espressione' rows='3' required>" . $formula->espressione . "</textarea>";
            
            echo "<label for='descrizione'>Descrizione</label>";
            echo "<textarea name='descrizione' rows='4'>" . $formula->descrizione . "</textarea>";
            
            echo "<label for='immagine'>Immagine (opzionale)</label>";
            if(!empty($formula->immagine) && file_exists($formula->immagine)) {
                echo "<div class='current-image'>";
                echo "<img src='" . $formula->immagine . "' alt='Immagine formula' style='max-width:300px;'><br>";
                echo "<small>Immagine attuale. Carica una nuova immagine per sostituirla.</small>";
                echo "</div>";
            }
            echo "<input type='file' name='immagine' accept='image/*'>";
            
            // Selezione multipla degli argomenti a cui associare la formula
            echo "<label for='argomenti'>Associa ad Argomenti (opzionale)</label>";
            $stmt_argomenti = $argomento->readAll();
            
            if ($stmt_argomenti->rowCount() > 0) {
                echo "<div class='checkbox-group'>";
                while ($row_argomento = $stmt_argomenti->fetch(PDO::FETCH_ASSOC)) {
                    $checked = "";
                    // Verifica se l'argomento è già associato alla formula
                    if ($formula->isArgomentoAssociated($formula->id, $row_argomento['id'])) {
                        $checked = "checked";
                    }
                    echo "<label class='checkbox-label'>";
                    echo "<input type='checkbox' name='argomenti[]' value='" . $row_argomento['id'] . "' $checked> ";
                    echo $row_argomento['titolo'];
                    echo "</label>";
                }
                echo "</div>";
            } else {
                echo "<p>Nessun argomento disponibile.</p>";
            }
            
            echo "<button type='submit' name='update'>Aggiorna Formula</button>";
            echo "<a href='formule.php' class='btn-secondary'>Annulla</a>";
            echo "</form>";
            echo "</div>";
        }
    }
    
    // Form per creare una nuova formula (inizialmente nascosto)
    echo "<div id='createFormContainer' style='display: none;'>";
    echo "<h2>Crea Nuova Formula</h2>";
    echo "<form action='' method='POST' enctype='multipart/form-data'>";
    
    echo "<label for='nome'>Nome della Formula</label>";
    echo "<input type='text' name='nome' required>";
    
    echo "<label for='espressione'>Espressione</label>";
    echo "<textarea name='espressione' rows='3' required></textarea>";
    
    echo "<label for='descrizione'>Descrizione</label>";
    echo "<textarea name='descrizione' rows='4'></textarea>";
    
    echo "<label for='immagine'>Immagine (opzionale)</label>";
    echo "<input type='file' name='immagine' accept='image/*'>";
    
    // Selezione multipla degli argomenti a cui associare la formula
    echo "<label for='argomenti'>Associa ad Argomenti (opzionale)</label>";
    $stmt_argomenti = $argomento->readAll();
    
    if ($stmt_argomenti->rowCount() > 0) {
        echo "<div class='checkbox-group'>";
        while ($row_argomento = $stmt_argomenti->fetch(PDO::FETCH_ASSOC)) {
            echo "<label class='checkbox-label'>";
            echo "<input type='checkbox' name='argomenti[]' value='" . $row_argomento['id'] . "'> ";
            echo $row_argomento['titolo'];
            echo "</label>";
        }
        echo "</div>";
    } else {
        echo "<p>Nessun argomento disponibile.</p>";
    }
    
    echo "<button type='submit' name='create'>Crea Formula</button>";
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