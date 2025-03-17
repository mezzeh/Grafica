<?php
// File: pages/handlers/requisito_handler.php

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// --- Gestione del form per creare un nuovo requisito ---
if (isset($_POST['create'])) {
    $requisito->esercizio_id = $_POST['esercizio_id'];
    $requisito->descrizione = $_POST['descrizione'];

    $insert_id = $requisito->create();
    if ($insert_id) {
        // Gestione degli argomenti associati (opzionale)
        if (isset($_POST['argomenti']) && is_array($_POST['argomenti'])) {
            foreach ($_POST['argomenti'] as $argomento_id) {
                $requisito->addArgomento($insert_id, $argomento_id);
            }
        }
        
        $message = "Requisito creato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile creare il requisito.";
        $message_class = "error";
    }
}

// --- Gestione della modifica di un requisito ---
if (isset($_POST['update'])) {
    $requisito->id = $_POST['id'];
    $requisito->esercizio_id = $_POST['esercizio_id'];
    $requisito->descrizione = $_POST['descrizione'];

    if ($requisito->update()) {
        // Rimuovi tutte le associazioni esistenti
        $requisito->removeAllArgomenti($requisito->id);
        
        // Aggiungi le nuove associazioni
        if (isset($_POST['argomenti']) && is_array($_POST['argomenti'])) {
            foreach ($_POST['argomenti'] as $argomento_id) {
                $requisito->addArgomento($requisito->id, $argomento_id);
            }
        }
        
        $message = "Requisito aggiornato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile aggiornare il requisito.";
        $message_class = "error";
    }
}

// --- Gestione della cancellazione di un requisito ---
if (isset($_GET['delete'])) {
    $requisito->id = $_GET['delete'];
    if ($requisito->delete()) {
        $message = "Requisito eliminato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile eliminare il requisito.";
        $message_class = "error";
    }
}
?>