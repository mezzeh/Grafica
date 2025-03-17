<?php
// File: pages/handlers/piano_handler.php

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// --- Gestione del form per creare un nuovo piano di studio ---
if (isset($_POST['create']) && isset($_SESSION['user_id'])) {
    $piano->nome = $_POST['nome'];
    $piano->descrizione = $_POST['descrizione'];
    $piano->user_id = $_SESSION['user_id'];
    $piano->visibility = $_POST['visibility'];

    if ($piano->create()) {
        $message = "Piano di studio creato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile creare il piano di studio.";
        $message_class = "error";
    }
}
// --- Gestione della modifica di un piano di studio ---
if (isset($_POST['update']) && isset($_SESSION['user_id'])) {
    // Make sure piano object is properly initialized
    if (!isset($piano) || $piano === null) {
        $piano = new PianoDiStudio($db);
    }
    
    // Set the properties before using them
    $piano->id = $_POST['id'];
    $piano->nome = $_POST['nome'];
    $piano->descrizione = $_POST['descrizione'];
    $piano->visibility = isset($_POST['visibility']) ? $_POST['visibility'] : 'private';
    
    // Debug
    error_log("Updating piano: " . print_r($_POST, true));
    
    // Verify ownership before updating
    $temp_piano = new PianoDiStudio($db);
    $temp_piano->id = $_POST['id'];
    $owner_info = $temp_piano->readOne();
    
    if($owner_info && ($owner_info['user_id'] == $_SESSION['user_id'] || isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
        if ($piano->update()) {
            $message = "Piano di studio aggiornato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile aggiornare il piano di studio.";
            $message_class = "error";
        }
    } else {
        $message = "Non hai i permessi per modificare questo piano di studio.";
        $message_class = "error";
    }
}

// --- Gestione della cancellazione di un piano ---
if (isset($_GET['delete']) && isset($_SESSION['user_id'])) {
    $piano->id = $_GET['delete'];
    
    // Verifica se l'utente è il proprietario o admin
    $owner_info = $piano->readOne();
    if($owner_info && ($owner_info['user_id'] == $_SESSION['user_id'] || isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
        if ($piano->delete()) {
            $message = "Piano di studio eliminato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile eliminare il piano di studio.";
            $message_class = "error";
        }
    } else {
        $message = "Non hai i permessi per eliminare questo piano di studio.";
        $message_class = "error";
    }
}
?>