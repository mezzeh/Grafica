<?php
ob_start();

// Include path utilities
require_once dirname(dirname(__DIR__)) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header_view.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/argomento.php');
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
$argomento = new Argomento($db);
$esame = new Esame($db);
$piano = new PianoDiStudio($db);

// Parametri GET
$argomento_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$argomento_id) {
    echo "<div class='message error'>Nessun argomento specificato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica i dettagli dell'argomento
$argomento->id = $argomento_id;
$argomento_info = $argomento->readOne();

if (!$argomento_info) {
    echo "<div class='message error'>Argomento non trovato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Mostra i requisiti collegati a questo argomento
echo "<div class='related-requisiti'>";
echo "<h3>Requisiti correlati</h3>";

// Query per ottenere i requisiti associati a questo argomento
$query = "SELECT r.id, r.descrizione, r.esercizio_id, e.titolo as esercizio_titolo
          FROM requisiti r
          JOIN requisito_argomento ra ON r.id = ra.requisito_id
          LEFT JOIN esercizi e ON r.esercizio_id = e.id
          WHERE ra.argomento_id = :argomento_id
          ORDER BY e.titolo, r.id";

$stmt = $db->prepare($query);
$stmt->bindParam(':argomento_id', $argomento_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "<ul class='item-list'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>
                <div class='item-description'>" . htmlspecialchars($row['descrizione']) . "</div>";
        
        if (!empty($row['esercizio_titolo'])) {
            echo "<div class='item-meta'>Esercizio: <a href='" . getUrlPath('pages/view_pages/view_esercizio.php?id=' . $row['esercizio_id']) . "'>" . 
                 htmlspecialchars($row['esercizio_titolo']) . "</a></div>";
        }
        
        echo "<div class='item-actions'>
                <a href='" . getUrlPath('pages/requisiti.php?edit=' . $row['id']) . "'>Modifica</a>
              </div>
            </li>";
    }
    echo "</ul>";
} else {
    echo "<p>Nessun requisito collegato a questo argomento.</p>";
}

echo "</div>";

// Carica le informazioni sull'esame
$esame->id = $argomento_info['esame_id'];
$esame_info = $esame->readOne();

// Carica informazioni sul piano di studio
$piano->id = $esame_info['piano_id'];
$piano_info = $piano->readOne();

// Crea il breadcrumb
echo "<div class='breadcrumb'>";
echo "<ul>";
echo "<li><a href='" . getUrlPath('pages/index.php') . "'>Piani di Studio</a></li>";
echo "<li><a href='" . getUrlPath('pages/esami.php?piano_id=' . $esame_info['piano_id']) . "'>Esami</a></li>";
echo "<li><a href='" . getUrlPath('pages/argomenti.php?esame_id=' . $esame_info['id']) . "'>" . htmlspecialchars($esame_info['nome']) . "</a></li>";
echo "<li>" . htmlspecialchars($argomento_info['titolo']) . "</li>";
echo "</ul>";
echo "</div>";

// Visualizza i dettagli dell'argomento
echo "<div class='argomento-details'>";
echo "<h2>" . htmlspecialchars($argomento_info['titolo']) . "</h2>";
echo "<div class='item-meta'>Importanza: " . $argomento_info['livello_importanza'] . "</div>";
echo "<div class='item-description'>" . htmlspecialchars($argomento_info['descrizione']) . "</div>";

echo "<div class='item-actions'>";
echo "<a href='" . getUrlPath('pages/sottoargomenti.php?argomento_id=' . $argomento_info['id']) . "' class='btn-primary'>Visualizza Sottoargomenti</a>";

// Verifica i permessi per mostrare il pulsante di modifica
if (isset($_SESSION['user_id']) && (
    (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) || 
    ($piano_info && $piano_info['user_id'] == $_SESSION['user_id'])
)) {
    echo " <a href='" . getUrlPath('pages/argomenti.php?edit=' . $argomento_info['id'] . "&esame_id=" . $argomento_info['esame_id']) . "' class='btn-secondary'>Modifica Argomento</a>";
}
echo "</div>";
echo "</div>";

// Gestione dei commenti
$risultato_commenti = gestioneCommentiArgomenti($db, $argomento_info['esame_id'], $argomento_id);

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
renderCommentiArgomenti($db, $argomento_info['esame_id'], $argomento_id);

ob_end_flush();

include_once getAbsolutePath('ui/includes/footer_view.php');
?>