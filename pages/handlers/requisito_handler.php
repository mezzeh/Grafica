<?php
// File: pages/handlers/requisito_handler.php

// Funzione per reindirizzamento sicuro
function safeRedirect($url) {
    if (headers_sent()) {
        echo "<script>window.location.href='" . $url . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=" . $url . "'></noscript>";
        exit;
    } else {
        header("Location: " . $url);
        exit;
    }
}

// Inizializza variabili di messaggio
$message = "";
$message_class = "";

// --- Gestione del form per creare un nuovo requisito ---
if (isset($_POST['create'])) {
    $requisito->esercizio_id = $_POST['esercizio_id'];
    $requisito->descrizione = $_POST['descrizione'];

    $insert_id = $requisito->create();
    if ($insert_id) {
        // Gestione degli argomenti associati
        if (isset($_POST['argomenti_ids']) && !empty($_POST['argomenti_ids'])) {
            $argomenti_ids = json_decode($_POST['argomenti_ids']);
            if (is_array($argomenti_ids)) {
                foreach ($argomenti_ids as $argomento_id) {
                    $requisito->addArgomento($insert_id, $argomento_id);
                }
            }
        }
        
        $message = "Requisito creato con successo!";
        $message_class = "success";
        
        // Reindirizza per evitare ripresentazione del form
        if ($esercizio_id) {
            // Usa safeRedirect invece di header diretto
            $redirect_url = getUrlPath("pages/requisiti.php?esercizio_id={$esercizio_id}&success=created");
            safeRedirect($redirect_url);
        }
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
        
        // Reindirizza per evitare ripresentazione del form
        if ($esercizio_id) {
            // Usa safeRedirect invece di header diretto
            $redirect_url = getUrlPath("pages/requisiti.php?esercizio_id={$esercizio_id}&success=updated");
            safeRedirect($redirect_url);
        }
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

// Gestione dei messaggi di successo da reindirizzamento
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $message = "Requisito creato con successo!";
            $message_class = "success";
            break;
        case 'updated':
            $message = "Requisito aggiornato con successo!";
            $message_class = "success";
            break;
    }
}
?>