<?php
// File: pages/components/comments/comments.php

// Includi i componenti necessari
include_once 'comment_list.php';

/**
 * Base function for comment management
 * 
 * @param PDO $db Database connection
 * @param string $tipo_elemento Element type (e.g., 'esame', 'piano', 'argomento')
 * @param int $elemento_id ID of the element
 * @param string $redirect_base Base URL for redirects
 * @return array|null Operation result or null
 */
function gestioneCommenti($db, $tipo_elemento, $elemento_id, $redirect_base) {
    // Check if the user is logged in
    if (!isset($_SESSION['user_id'])) {
        return ['message' => 'Accesso negato', 'message_class' => 'error'];
    }

    $commento = new Commento($db);

    // Add comment
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
        } else {
            return [
                'message' => 'Errore nell\'aggiunta del commento', 
                'message_class' => 'error'
            ];
        }
    }

    // Update comment
    if (isset($_POST['update_comment'])) {
        $commento->id = $_POST['comment_id'];
        
        // Prima verifica che il commento esista
        $commento_esistente = $commento->readOne();
        
        if (!$commento_esistente) {
            return [
                'message' => 'Commento non trovato', 
                'message_class' => 'error'
            ];
        }
        
        // Verifica i permessi (solo il proprietario o admin possono modificare)
        if ($commento_esistente['user_id'] != $_SESSION['user_id'] && 
            !(isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
            return [
                'message' => 'Non hai i permessi per modificare questo commento', 
                'message_class' => 'error'
            ];
        }
        
        // Aggiorna i dati del commento
        $commento->user_id = $commento_esistente['user_id']; // Mantiene l'utente originale
        $commento->testo = $_POST['comment_text'];
        
        if ($commento->update()) {
            return [
                'message' => 'Commento aggiornato con successo', 
                'message_class' => 'success',
                'redirect' => "{$redirect_base}&comment_updated=1"
            ];
        } else {
            return [
                'message' => 'Errore nell\'aggiornamento del commento', 
                'message_class' => 'error'
            ];
        }
    }

    // Delete comment
    if (isset($_GET['delete_comment'])) {
        $commento->id = $_GET['delete_comment'];
        $commento_esistente = $commento->readOne();
        
        if (!$commento_esistente) {
            return [
                'message' => 'Commento non trovato', 
                'message_class' => 'error'
            ];
        }
        
        // Verifica i permessi (solo il proprietario o admin possono eliminare)
        if ($commento_esistente['user_id'] == $_SESSION['user_id'] || 
            (isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
            
            if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                // Se è un admin, usa il metodo dedicato
                if ($commento->deleteByAdmin()) {
                    return [
                        'message' => 'Commento eliminato con successo', 
                        'message_class' => 'success',
                        'redirect' => "{$redirect_base}&comment_deleted=1"
                    ];
                }
            } else {
                // Altrimenti usa il metodo standard che verifica anche l'user_id
                $commento->user_id = $_SESSION['user_id'];
                if ($commento->delete()) {
                    return [
                        'message' => 'Commento eliminato con successo', 
                        'message_class' => 'success',
                        'redirect' => "{$redirect_base}&comment_deleted=1"
                    ];
                }
            }
            
            return [
                'message' => 'Errore nell\'eliminazione del commento', 
                'message_class' => 'error'
            ];
        } else {
            return [
                'message' => 'Non hai i permessi per eliminare questo commento', 
                'message_class' => 'error'
            ];
        }
    }

    return null;
}

/**
 * Funzione per gestire i commenti dei piani di studio
 */
function gestioneCommentiPiani($db, $piano_id) {
    $redirect_base = "view_piano.php?id={$piano_id}";
    return gestioneCommenti($db, 'piano', $piano_id, $redirect_base);
}

/**
 * Funzione per renderizzare i commenti dei piani di studio
 */
function renderCommentiPiani($db, $piano_id) {
    $redirect_param = "id={$piano_id}";
    renderCommenti($db, 'piano', $piano_id, $redirect_param);
}

/**
 * Funzione per gestire i commenti degli esami
 */
function gestioneCommentiEsami($db, $esame_id) {
    $redirect_base = "view_esame.php?id={$esame_id}";
    return gestioneCommenti($db, 'esame', $esame_id, $redirect_base);
}

/**
 * Funzione per renderizzare i commenti degli esami
 */
function renderCommentiEsami($db, $esame_id) {
    $redirect_param = "id={$esame_id}";
    renderCommenti($db, 'esame', $esame_id, $redirect_param);
}

/**
 * Funzione di compatibilità per gli argomenti
 */
function gestioneCommentiArgomenti($db, $esame_id, $argomento_id) {
    // Modifica il reindirizzamento per puntare alla pagina di dettaglio
    $redirect_base = "view_argomento.php?id={$argomento_id}";
    return gestioneCommenti($db, 'argomento', $argomento_id, $redirect_base);
}

/**
 * Funzione per renderizzare i commenti degli argomenti
 */
function renderCommentiArgomenti($db, $esame_id, $argomento_id) {
    // Aggiorna il parametro di reindirizzamento per riflettere la nuova struttura URL
    $redirect_param = "id={$argomento_id}";
    renderCommenti($db, 'argomento', $argomento_id, $redirect_param);
}

/**
 * Funzione per gestire i commenti degli esercizi
 */
function gestioneCommentiEsercizi($db, $esercizio_id) {
    $redirect_base = "view_esercizio.php?id={$esercizio_id}";
    return gestioneCommenti($db, 'esercizio', $esercizio_id, $redirect_base);
}

/**
 * Funzione per renderizzare i commenti degli esercizi
 */
function renderCommentiEsercizi($db, $esercizio_id) {
    $redirect_param = "id={$esercizio_id}";
    renderCommenti($db, 'esercizio', $esercizio_id, $redirect_param);
}

/**
 * Funzione per gestire i commenti dei sottoargomenti
 */
function gestioneCommentiSottoargomenti($db, $sottoargomento_id) {
    $redirect_base = "view_sottoargomento.php?id={$sottoargomento_id}";
    return gestioneCommenti($db, 'sottoargomento', $sottoargomento_id, $redirect_base);
}

/**
 * Funzione per renderizzare i commenti dei sottoargomenti
 */
function renderCommentiSottoargomenti($db, $sottoargomento_id) {
    $redirect_param = "id={$sottoargomento_id}";
    renderCommenti($db, 'sottoargomento', $sottoargomento_id, $redirect_param);
}

/**
 * Funzione per gestire i commenti delle formule
 */
function gestioneCommentiFormule($db, $formula_id) {
    $redirect_base = "view_formula.php?id={$formula_id}";
    return gestioneCommenti($db, 'formula', $formula_id, $redirect_base);
}

/**
 * Funzione per renderizzare i commenti delle formule
 */
function renderCommentiFormule($db, $formula_id) {
    $redirect_param = "id={$formula_id}";
    renderCommenti($db, 'formula', $formula_id, $redirect_param);
}