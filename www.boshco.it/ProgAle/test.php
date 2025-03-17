
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestione Piani di Studio</title>
    <!-- Collega il file CSS -->
    <link rel="stylesheet" href="ui/style.css">
</head>
<body>
<?php
include_once 'config/database.php';
include_once 'models/piano_di_studio.php';

// Test di connessione
$database = new Database();
$db = $database->getConnection();

if($db) {
    echo "<p>Connessione al database riuscita!</p>";

    // Istanza del modello PianoDiStudio
    $piano = new PianoDiStudio($db);

    // --- Gestione del form per creare un nuovo piano di studio ---
    if (isset($_POST['create'])) {
        $piano->nome = $_POST['nome'];
        $piano->descrizione = $_POST['descrizione'];

        if ($piano->create()) {
            echo "<p>Piano di studio creato con successo!</p>";
        } else {
            echo "<p>Impossibile creare il piano di studio.</p>";
        }
    }

    // --- Gestione della modifica di un piano di studio ---
    if (isset($_POST['update'])) {
        $piano->id = $_POST['id'];
        $piano->nome = $_POST['nome'];
        $piano->descrizione = $_POST['descrizione'];

        if ($piano->update()) {
            echo "<p>Piano di studio aggiornato con successo!</p>";
        } else {
            echo "<p>Impossibile aggiornare il piano di studio.</p>";
        }
    }

    // --- Gestione della cancellazione di un piano ---
    if (isset($_GET['delete'])) {
        $piano->id = $_GET['delete'];
        if ($piano->delete()) {
            echo "<p>Piano di studio eliminato con successo!</p>";
        } else {
            echo "<p>Impossibile eliminare il piano di studio.</p>";
        }
    }

    // --- Leggi tutti i piani di studio ---
    $stmt = $piano->readAll();
    echo "<h2>Piani di Studio</h2>";
    echo "<ul>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        echo "<li>$nome - $descrizione
            <a href='?edit=$id'>Modifica</a> | 
            <a href='?delete=$id' onclick='return confirm(\"Sei sicuro di voler eliminare questo piano?\");'>Elimina</a></li>";
    }
    echo "</ul>";

    // --- Modifica un piano di studio ---
    if (isset($_GET['edit'])) {
        $piano->id = $_GET['edit'];
        if ($piano->readOne()) {
            echo "<h2>Modifica Piano di Studio</h2>";
            echo "<form action='' method='POST'>
                    <input type='hidden' name='id' value='$piano->id'>
                    <label for='nome'>Nome</label>
                    <input type='text' name='nome' value='$piano->nome' required>
                    <label for='descrizione'>Descrizione</label>
                    <textarea name='descrizione' required>$piano->descrizione</textarea>
                    <button type='submit' name='update'>Aggiorna Piano</button>
                </form>";
        }
    }

    // --- Form per creare un nuovo piano di studio ---
    echo "<h2>Crea Nuovo Piano di Studio</h2>";
    echo "<form action='' method='POST'>
            <label for='nome'>Nome</label>
            <input type='text' name='nome' required>
            <label for='descrizione'>Descrizione</label>
            <textarea name='descrizione' required></textarea>
            <button type='submit' name='create'>Crea Piano</button>
        </form>";

} else {
    echo "<p>Problema di connessione al database.</p>";
}
?>

</body>
</html>