<?php
// File: pages/handlers/sottoargomento_handler.php

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// --- Gestione del form per creare un nuovo sottoargomento ---
if (isset($_POST['create'])) {
    $sottoargomento->argomento_id = $_POST['argomento_id'];
    $sottoargomento->titolo = $_POST['titolo'];
    $sottoargomento->descrizione = $_POST['descrizione'];
    $sottoargomento->livello_profondita = $_POST['livello_profondita'];

    if ($sottoargomento->create()) {
        // Ottieni l'ID del nuovo sottoargomento
        $nuovo_id = $db->lastInsertId();
        
        // Gestione dei prerequisiti
        // Argomenti
        if (isset($_POST['argomenti_prereq']) && !empty($_POST['argomenti_prereq'])) {
            $argomenti_ids = json_decode($_POST['argomenti_prereq']);
            if (is_array($argomenti_ids)) {
                foreach ($argomenti_ids as $arg_id) {
                    $sottoargomento->addArgomentoPrerequisito($nuovo_id, $arg_id);
                }
            }
        }
        
        // Sottoargomenti
        if (isset($_POST['sottoargomenti_prereq']) && !empty($_POST['sottoargomenti_prereq'])) {
            $sottoargomenti_ids = json_decode($_POST['sottoargomenti_prereq']);
            if (is_array($sottoargomenti_ids)) {
                foreach ($sottoargomenti_ids as $sa_id) {
                    $sottoargomento->addSottoargomentoPrerequisito($nuovo_id, $sa_id);
                }
            }
        }
        
        $message = "Sottoargomento creato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile creare il sottoargomento.";
        $message_class = "error";
    }
}

// --- Gestione della modifica di un sottoargomento ---
if (isset($_POST['update'])) {
    $sottoargomento->id = $_POST['id'];
    $sottoargomento->argomento_id = $_POST['argomento_id'];
    $sottoargomento->titolo = $_POST['titolo'];
    $sottoargomento->descrizione = $_POST['descrizione'];
    $sottoargomento->livello_profondita = $_POST['livello_profondita'];

    if ($sottoargomento->update()) {
        // Rimuovi tutti i prerequisiti esistenti
        $sottoargomento->removeAllArgomentiPrerequisiti($sottoargomento->id);
        $sottoargomento->removeAllSottoargomentiPrerequisiti($sottoargomento->id);
        
        // Aggiungi i nuovi prerequisiti
        // Argomenti
        if (isset($_POST['argomenti_prereq']) && !empty($_POST['argomenti_prereq'])) {
            $argomenti_ids = json_decode($_POST['argomenti_prereq']);
            if (is_array($argomenti_ids)) {
                foreach ($argomenti_ids as $arg_id) {
                    $sottoargomento->addArgomentoPrerequisito($sottoargomento->id, $arg_id);
                }
            }
        }
        
        // Sottoargomenti
        if (isset($_POST['sottoargomenti_prereq']) && !empty($_POST['sottoargomenti_prereq'])) {
            $sottoargomenti_ids = json_decode($_POST['sottoargomenti_prereq']);
            if (is_array($sottoargomenti_ids)) {
                foreach ($sottoargomenti_ids as $sa_id) {
                    $sottoargomento->addSottoargomentoPrerequisito($sottoargomento->id, $sa_id);
                }
            }
        }
        
        $message = "Sottoargomento aggiornato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile aggiornare il sottoargomento.";
        $message_class = "error";
    }
}

// --- Gestione della cancellazione di un sottoargomento ---
if (isset($_GET['delete'])) {
    $sottoargomento->id = $_GET['delete'];
    
    // La rimozione dei prerequisiti avverrà automaticamente grazie ai vincoli ON DELETE CASCADE
    if ($sottoargomento->delete()) {
        $message = "Sottoargomento eliminato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile eliminare il sottoargomento.";
        $message_class = "error";
    }
}
?>