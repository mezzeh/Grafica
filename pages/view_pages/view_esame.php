<?php
// File: pages/view_pages/view_esame.php

ob_start();

// Includi header
include_once '../../ui/includes/header_view.php'; // Aggiornato il percorso

// Includi file di configurazione e modelli
include_once '../../config/database.php'; // Aggiornato il percorso
include_once '../../models/esame.php'; // Aggiornato il percorso
include_once '../../models/piano_di_studio.php'; // Aggiornato il percorso
include_once '../../models/comments.php'; // Aggiornato il percorso
include_once '../components/comments/comments.php'; // Aggiornato il percorso

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once '../../ui/includes/footer_view.php'; // Aggiornato il percorso
    exit;
}

// Inizializza modelli
$esame = new Esame($db);
$piano = new PianoDiStudio($db);

// Parametri GET
$esame_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$esame_id) {
    echo "<div class='message error'>Nessun esame specificato.</div>";
    include_once '../../ui/includes/footer_view.php'; // Aggiornato il percorso
    exit;
}

// Carica i dettagli dell'esame
$esame->id = $esame_id;
$esame_info = $esame->readOne();

if (!$esame_info) {
    echo "<div class='message error'>Esame non trovato.</div>";
    include_once '../../ui/includes/footer_view.php'; // Aggiornato il percorso
    exit;
}

// Carica le informazioni sul piano di studio
$piano->id = $esame_info['piano_id'];
$piano_info = $piano->readOne();

// Creare il breadcrumb
echo "<div class='breadcrumb'>";
echo "<ul>";
echo "<li><a href='../index.php'>Piani di Studio</a></li>"; // Aggiornato il percorso
echo "<li><a href='../esami.php?piano_id=" . $esame_info['piano_id'] . "'>" . htmlspecialchars($piano_info['nome']) . "</a></li>"; // Aggiornato il percorso
echo "<li>" . htmlspecialchars($esame_info['nome']) . "</li>";
echo "</ul>";
echo "</div>";

// Visualizza i dettagli dell'esame
echo "<div class='esame-details'>";
echo "<h2>" . htmlspecialchars($esame_info['nome']) . "</h2>";
echo "<div class='item-meta'>Codice: " . htmlspecialchars($esame_info['codice']) . " | Crediti: " . $esame_info['crediti'] . "</div>";
echo "<div class='item-description'><h3>Descrizione</h3>" . htmlspecialchars($esame_info['descrizione']) . "</div>";

echo "<div class='item-actions'>";
echo "<a href='../argomenti.php?esame_id=" . $esame_info['id'] . "' class='btn-primary'>Visualizza Argomenti</a>"; // Aggiornato il percorso

// Verifica i permessi per mostrare il pulsante di modifica
if (isset($_SESSION['user_id']) && (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] || 
    $piano_info && $piano_info['user_id'] == $_SESSION['user_id'])) {
echo "<a href='../esami.php?edit=" . $esame_info['id'] . "&piano_id=" . $esame_info['piano_id'] . "' class='btn-secondary'>Modifica Esame</a>";}
echo "</div>";
echo "</div>";

// Gestione dei commenti
$risultato_commenti = gestioneCommentiEsami($db, $esame_id);

// Se c'è un risultato con redirect, esegui il redirect
/* if ($risultato_commenti && isset($risultato_commenti['redirect'])) {
    header("Location: " . $risultato_commenti['redirect']);
    exit;
}
 */
// Mostra eventuali messaggi
if ($risultato_commenti && !empty($risultato_commenti['message'])) {
    echo "<div class='message {$risultato_commenti['message_class']}'>{$risultato_commenti['message']}</div>";
}

// Rendering dei commenti
renderCommentiEsami($db, $esame_id);

ob_end_flush();

include_once '../../ui/includes/footer_view.php'; // Aggiornato il percorso
?>