<?php
// File: pages/handlers/comment_handler.php

/**
 * Gestisce le operazioni CRUD per i commenti
 * 
 * @param PDO $db Connessione al database
 * @param string $tipo_elemento Tipo di elemento (es. 'argomento', 'esame', 'piano')
 * @param int $elemento_id ID dell'elemento a cui è associato il commento
 * @param string $redirect_base URL base per i redirect
 * @return array|null Risultato dell'operazione o null
 */
if (!class_exists('Commento')) {
    include_once '../models/commento.php';
}

function gestioneCommenti($db, $tipo_elemento, $elemento_id, $redirect_base) {
    // Verifica se l'utente è loggato
    if (!isset($_SESSION['user_id'])) {
        return ['message' => 'Accesso negato', 'message_class' => 'error'];
    }

    $commento = new Commento($db);

    // Aggiunta commento
    if (isset($_POST['add_comment'])) {
        $nuovo_commento = new Commento($db);
        $nuovo_commento->user_id = $_SESSION['user_id'];
        $nuovo_commento->tipo_elemento = $tipo_elemento;
        $nuovo_commento->elemento_id = $elemento_id;
        $nuovo_commento->testo = $_POST['comment_text'];
        
        if ($nuovo_commento->create()) {
            return [
                'message' => 'Commento aggiunto con successo', 
                'message_class' => 'success',
                'redirect' => "{$redirect_base}&comment_added=1"
            ];
        }
    }

    // Aggiornamento commento
    if (isset($_POST['update_comment'])) {
        $commento->id = $_POST['comment_id'];
        $commento->user_id = $_SESSION['user_id'];
        $commento->testo = $_POST['comment_text'];
        
        // Verifica se è l'autore o admin
        $commento_esistente = $commento->readOne();
        if ($commento_esistente['user_id'] == $_SESSION['user_id'] || 
            (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
            
            if ($commento->update()) {
                return [
                    'message' => 'Commento aggiornato con successo', 
                    'message_class' => 'success',
                    'redirect' => "{$redirect_base}&comment_updated=1"
                ];
            }
        }
    }

    // Eliminazione commento
    if (isset($_GET['delete_comment'])) {
        $commento->id = $_GET['delete_comment'];
        $commento_esistente = $commento->readOne();
        
        // Verifica se è l'autore o admin
        if ($commento_esistente['user_id'] == $_SESSION['user_id'] || 
            (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
            
            if ($commento->delete()) {
                return [
                    'message' => 'Commento eliminato con successo', 
                    'message_class' => 'success',
                    'redirect' => "{$redirect_base}&comment_deleted=1"
                ];
            }
        }
    }

    return null;
}
?>