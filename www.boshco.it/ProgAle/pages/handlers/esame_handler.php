<?php
// File: pages/handlers/esame_handler.php

// Funzione per verificare i permessi
function verificaPermessiPiano($db, $piano_id) {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    
    $piano = new PianoDiStudio($db);
    $piano->id = $piano_id;
    $piano_info = $piano->readOne();
    
    return ($piano_info && 
        (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] || 
         $piano_info['user_id'] == $_SESSION['user_id']));
}

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// --- Gestione del form per creare un nuovo esame ---
if (isset($_POST['create']) && isset($_SESSION['user_id'])) {
    $esame->piano_id = $_POST['piano_id'];
    $esame->nome = $_POST['nome'];
    $esame->codice = $_POST['codice'];
    $esame->crediti = $_POST['crediti'];
    $esame->descrizione = $_POST['descrizione'];

    // Verifica che l'utente sia il proprietario del piano o admin
    if (verificaPermessiPiano($db, $esame->piano_id)) {
        if ($esame->create()) {
            $message = "Esame creato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile creare l'esame.";
            $message_class = "error";
        }
    } else {
        $message = "Non hai i permessi per creare un esame in questo piano di studio.";
        $message_class = "error";
    }
}

// --- Gestione della modifica di un esame ---
if (isset($_POST['update']) && isset($_SESSION['user_id'])) {
    $esame->id = $_POST['id'];
    $esame->piano_id = $_POST['piano_id'];
    $esame->nome = $_POST['nome'];
    $esame->codice = $_POST['codice'];
    $esame->crediti = $_POST['crediti'];
    $esame->descrizione = $_POST['descrizione'];

    // Verifica che l'utente sia il proprietario del piano o admin
    $piano = new PianoDiStudio($db);
    $piano->id = $esame->piano_id;
    $piano_details = $piano->readOne();
    
    if ($piano_details && ($piano_details['user_id'] == $_SESSION['user_id'] || (isset($_SESSION['is_admin']) && $_SESSION['is_admin']))) {
        if ($esame->update()) {
            $message = "Esame aggiornato con successo!";
            $message_class = "success";
        } else {
            $message = "Impossibile aggiornare l'esame.";
            $message_class = "error";
        }
    } else {
        $message = "Non hai i permessi per modificare questo esame.";
        $message_class = "error";
    }
}

// --- Gestione della cancellazione di un esame ---
if (isset($_GET['delete']) && isset($_SESSION['user_id'])) {
    $esame->id = $_GET['delete'];
    $temp_info = $esame->readOne();
    
    if ($temp_info) {
        // Verifica che l'utente sia il proprietario del piano o admin
        $piano->id = $temp_info['piano_id'];
        $piano_details = $piano->readOne();
        
        if ($piano_details && ($piano_details['user_id'] == $_SESSION['user_id'] || (isset($_SESSION['is_admin']) && $_SESSION['is_admin']))) {
            if ($esame->delete()) {
                $message = "Esame eliminato con successo!";
                $message_class = "success";
            } else {
                $message = "Impossibile eliminare l'esame.";
                $message_class = "error";
            }
        } else {
            $message = "Non hai i permessi per eliminare questo esame.";
            $message_class = "error";
        }
    } else {
        $message = "Esame non trovato.";
        $message_class = "error";
    }
}
?>