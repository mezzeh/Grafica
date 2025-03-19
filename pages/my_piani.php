<?php
// Includi header
include_once '../ui/includes/header.php';

// Se l'utente non è loggato, reindirizza alla pagina di login
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/piano_di_studio.php';

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Istanza del modello PianoDiStudio
$piano = new PianoDiStudio($db);

// Includi handler per le operazioni CRUD
$_GET['from'] = 'my'; // Indica che siamo nella pagina dei miei piani
include_once 'handlers/piano_handler.php';

// Mostra il messaggio se presente
if (!empty($message)) {
    echo "<div class='message $message_class'>$message</div>";
}

// --- MOSTRA I PIANI DI STUDIO DELL'UTENTE ---
echo "<div class='header-with-button'>";
echo "<h2>I Miei Piani di Studio</h2>";
echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Piano</button>";
echo "</div>";

// Leggi tutti i piani di studio dell'utente
$stmt = $piano->readByUser($_SESSION['user_id']);
$num = $stmt->rowCount();

if ($num > 0) {
    echo "<ul class='item-list'>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $visibility_text = ($visibility == 'public') ? "Pubblico" : "Privato";
        
        echo "<li>
                <div class='item-title'>" . htmlspecialchars($nome) . "</div>
                <div class='item-meta'>Visibilità: $visibility_text</div>
                <div class='item-description'>" . htmlspecialchars($descrizione) . "</div>
                <div class='item-actions'>
                    <a href='view_pages/view_piano.php?id=$id'>Visualizza</a> | 
                    <a href='esami.php?piano_id=$id'>Esami</a> | 
                    <a href='?edit=$id&from=my'>Modifica</a> | 
                    <a href='?delete=$id&from=my' onclick='return confirm(\"Sei sicuro di voler eliminare questo piano?\");'>Elimina</a>
                </div>
            </li>";
    }
    echo "</ul>";
} else {
    echo "<p>Non hai ancora creato piani di studio.</p>";
}

// Includi il form appropriato in base alla richiesta
if (isset($_GET['edit'])) {
    include_once 'components/forms/edit_piano.php';
} else {
    include_once 'components/forms/create_piano.php';
}

include_once '../ui/includes/footer.php';
?>