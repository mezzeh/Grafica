<?php
// File: pages/view_pages/view_esame.php
ob_start();

// Include path utilities
require_once dirname(dirname(__DIR__)) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header_view.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/esame.php');
include_once getAbsolutePath('models/piano_di_studio.php');
include_once getAbsolutePath('models/comments.php');
include_once getAbsolutePath('pages/components/comments/comments.php');

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Inizializza modelli
$esame = new Esame($db);
$piano = new PianoDiStudio($db);

// Parametri GET
$esame_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$esame_id) {
    echo "<div class='message error'>Nessun esame specificato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica i dettagli dell'esame
$esame->id = $esame_id;
$esame_info = $esame->readOne();

if (!$esame_info) {
    echo "<div class='message error'>Esame non trovato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica le informazioni sul piano di studio
$piano->id = $esame_info['piano_id'];
$piano_info = $piano->readOne();

// Creare il breadcrumb
echo "<div class='breadcrumb'>";
echo "<ul>";
echo "<li><a href='" . getUrlPath('pages/index.php') . "'>Piani di Studio</a></li>";
echo "<li><a href='" . getUrlPath('pages/esami.php?piano_id=' . $esame_info['piano_id']) . "'>" . htmlspecialchars($piano_info['nome']) . "</a></li>";
echo "<li>" . htmlspecialchars($esame_info['nome']) . "</li>";
echo "</ul>";
echo "</div>";

// Visualizza i dettagli dell'esame
echo "<div class='esame-details'>";
echo "<h2>" . htmlspecialchars($esame_info['nome']) . "</h2>";
echo "<div class='item-meta'>Codice: " . htmlspecialchars($esame_info['codice']) . " | Crediti: " . $esame_info['crediti'] . "</div>";
echo "<div class='item-description'><h3>Descrizione</h3>" . htmlspecialchars($esame_info['descrizione']) . "</div>";

echo "<div class='item-actions'>";
echo "<a href='" . getUrlPath('pages/argomenti.php?esame_id=' . $esame_info['id']) . "' class='btn-primary'>Visualizza Argomenti</a>";

// Verifica i permessi per mostrare il pulsante di modifica
if (isset($_SESSION['user_id']) && (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] || 
    $piano_info && $piano_info['user_id'] == $_SESSION['user_id'])) {
echo "<a href='" . getUrlPath('pages/esami.php?edit=' . $esame_info['id'] . '&piano_id=' . $esame_info['piano_id']) . "' class='btn-secondary'>Modifica Esame</a>";}
echo "</div>";
echo "</div>";

// Gestione dei commenti
$risultato_commenti = gestioneCommentiEsami($db, $esame_id);

// Se c'è un risultato con redirect, esegui il redirect
if ($risultato_commenti && isset($risultato_commenti['redirect'])) {
    header("Location: " . $risultato_commenti['redirect']);
    exit;
}

// Mostra eventuali messaggi
if ($risultato_commenti && !empty($risultato_commenti['message'])) {
    echo "<div class='message {$risultato_commenti['message_class']}'>{$risultato_commenti['message']}</div>";
}

// Rendering dei commenti
renderCommentiEsami($db, $esame_id);

ob_end_flush();

include_once getAbsolutePath('ui/includes/footer_view.php');
?>