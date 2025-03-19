<?php
// File: pages/handlers/esercizio_handler.php

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// --- Gestione del form per creare un nuovo esercizio ---
if (isset($_POST['create'])) {
    $esercizio->sottoargomento_id = $_POST['sottoargomento_id'];
    $esercizio->titolo = $_POST['titolo'];
    $esercizio->testo = $_POST['testo'];
    $esercizio->soluzione = $_POST['soluzione'];
    $esercizio->difficolta = $_POST['difficolta'];

    if ($esercizio->create()) {
        $message = "Esercizio creato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile creare l'esercizio.";
        $message_class = "error";
    }
}

// --- Gestione della modifica di un esercizio ---
if (isset($_POST['update'])) {
    $esercizio->id = $_POST['id'];
    $esercizio->sottoargomento_id = $_POST['sottoargomento_id'];
    $esercizio->titolo = $_POST['titolo'];
    $esercizio->testo = $_POST['testo'];
    $esercizio->soluzione = $_POST['soluzione'];
    $esercizio->difficolta = $_POST['difficolta'];

    if ($esercizio->update()) {
        $message = "Esercizio aggiornato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile aggiornare l'esercizio.";
        $message_class = "error";
    }
}

// --- Gestione della cancellazione di un esercizio ---
if (isset($_GET['delete'])) {
    $esercizio->id = $_GET['delete'];
    if ($esercizio->delete()) {
        $message = "Esercizio eliminato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile eliminare l'esercizio.";
        $message_class = "error";
    }
}
?>