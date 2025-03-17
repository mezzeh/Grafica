<?php
// File: pages/handlers/argomento_handler.php

// Funzione per verificare i permessi
function verificaPermessiPiano($db, $esame_id) {
    $esame = new Esame($db);
    $piano = new PianoDiStudio($db);
    
    $esame->id = $esame_id;
    $esame_info = $esame->readOne();
    
    if (!$esame_info) {
        return false;
    }
    
    $piano->id = $esame_info['piano_id'];
    $piano_details = $piano->readOne();
    
    return ($piano_details && 
        (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] || 
         $piano_details['user_id'] == $_SESSION['user_id']));
}

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// Gestione operazioni CRUD
if (isset($_SESSION['user_id'])) {
    // Creazione argomento
    if (isset($_POST['create']) && $esame_id) {
        if (verificaPermessiPiano($db, $esame_id)) {
            $argomento->esame_id = $esame_id;
            $argomento->titolo = $_POST['titolo'];
            $argomento->descrizione = $_POST['descrizione'];
            $argomento->livello_importanza = $_POST['livello_importanza'];

            if ($argomento->create()) {
                $message = "Argomento creato con successo!";
                $message_class = "success";
                header("Location: argomenti.php?esame_id=" . $esame_id);
                exit;
            } else {
                $message = "Impossibile creare l'argomento.";
                $message_class = "error";
            }
        } else {
            $message = "Non hai i permessi per creare un argomento in questo esame.";
            $message_class = "error";
        }
    }

    // Modifica argomento
    if (isset($_POST['update']) && $edit_id) {
        $argomento->id = $edit_id;
        $argomento_info = $argomento->readOne();
        
        if ($argomento_info) {
            $esame->id = $argomento_info['esame_id'];
            $esame_info = $esame->readOne();
            
            if (verificaPermessiPiano($db, $esame_info['id'])) {
                $argomento->titolo = $_POST['titolo'];
                $argomento->descrizione = $_POST['descrizione'];
                $argomento->livello_importanza = $_POST['livello_importanza'];

                if ($argomento->update()) {
                    $message = "Argomento aggiornato con successo!";
                    $message_class = "success";
                    header("Location: argomenti.php?esame_id=" . $esame_info['id']);
                    exit;
                } else {
                    $message = "Impossibile aggiornare l'argomento.";
                    $message_class = "error";
                }
            } else {
                $message = "Non hai i permessi per modificare questo argomento.";
                $message_class = "error";
            }
        }
    }

    // Eliminazione argomento
    if (isset($_GET['delete'])) {
        $argomento->id = $_GET['delete'];
        $argomento_info = $argomento->readOne();
        
        if ($argomento_info) {
            if (verificaPermessiPiano($db, $argomento_info['esame_id'])) {
                if ($argomento->delete()) {
                    $message = "Argomento eliminato con successo!";
                    $message_class = "success";
                    header("Location: argomenti.php?esame_id=" . $argomento_info['esame_id']);
                    exit;
                } else {
                    $message = "Impossibile eliminare l'argomento.";
                    $message_class = "error";
                }
            } else {
                $message = "Non hai i permessi per eliminare questo argomento.";
                $message_class = "error";
            }
        }
    }
}
?>