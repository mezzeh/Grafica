

<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/piano_di_studio.php';

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    $message = "Problema di connessione al database.";
    $message_class = "error";
} else {
    // Istanza del modello PianoDiStudio
    $piano = new PianoDiStudio($db);

    // Includi handler per le operazioni CRUD
    include_once 'handlers/piano_handler.php';

    // Mostra il messaggio se presente
    if (!empty($message)) {
        echo "<div class='message $message_class'>$message</div>";
    }
    
    // --- PRIMA MOSTRA I PIANI DI STUDIO PUBBLICI ---
    echo "<div class='header-with-button'>";
    echo "<h2>Piani di Studio Pubblici</h2>";
    if(isset($_SESSION['user_id'])) {
        echo "<button id='showCreateFormBtn' class='btn-primary'>Aggiungi Nuovo Piano</button>";
    } else {
        echo "<p>Per creare il tuo piano di studio, <a href='login.php'>accedi</a> o <a href='register.php'>registrati</a>.</p>";
    }
    echo "</div>";
    
    // Leggi tutti i piani di studio pubblici
    $stmt = $piano->readPublic();
    $num = $stmt->rowCount();
    
    if ($num > 0) {
        echo "<ul class='item-list'>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            
            // Ottieni il nome dell'utente che ha creato il piano
            $user_query = "SELECT username FROM users WHERE id = :user_id LIMIT 1";
            $user_stmt = $db->prepare($user_query);
            $user_stmt->bindParam(":user_id", $user_id);
            $user_stmt->execute();
            $user_row = $user_stmt->fetch(PDO::FETCH_ASSOC);
            $creator = $user_row ? $user_row['username'] : "Utente sconosciuto";
            
            echo "<li>
                    <div class='item-title'>" . htmlspecialchars($nome) . "</div>
                    <div class='item-meta'>Creato da: " . htmlspecialchars($creator) . "</div>
                    <div class='item-description'>" . htmlspecialchars($descrizione) . "</div>
                    <div class='item-actions'>
                        <a href='view_pages/view_piano.php?id=$id'>Visualizza</a>";
            
            // Mostra i pulsanti di modifica solo se l'utente è loggato ed è il proprietario o admin
            if(isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $user_id || isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
                echo " | <a href='?edit=$id'>Modifica</a>";
                echo " | <a href='?delete=$id' onclick='return confirm(\"Sei sicuro di voler eliminare questo piano?\");'>Elimina</a>";
            }
            
            echo "</div>
                </li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nessun piano di studio pubblico trovato.</p>";
    }
    
    // Se l'utente è loggato, mostra un link ai suoi piani
    if(isset($_SESSION['user_id'])) {
        echo '<div class="header-with-button">';
        echo '<h2><a href="my_piani.php" class="btn-primary">I miei piani di studio</a></h2>';
        echo '</div>';
    }
    
    // Includi il form appropriato in base alla richiesta
    if (isset($_GET['edit'])) {
        include_once 'components/forms/edit_piano.php';
    } else {
        include_once 'components/forms/create_piano.php';
    }
}

// Includi footer
include_once '../ui/includes/footer.php';
?>