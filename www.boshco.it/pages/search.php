<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/piano_di_studio.php';
include_once '../models/esame.php';
include_once '../models/argomento.php';
include_once '../models/sottoargomento.php';
include_once '../models/esercizio.php';
include_once '../models/formula.php';

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Verifica se è stata fornita una query di ricerca
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($search_term)) {
    echo "<div class='message info'>Inserisci un termine di ricerca.</div>";
} else {
    // Connessione al database
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        echo "<div class='message error'>Problema di connessione al database.</div>";
    } else {
        // Istanza dei modelli
        $piano = new PianoDiStudio($db);
        $esame = new Esame($db);
        $argomento = new Argomento($db);
        $sottoargomento = new SottoArgomento($db);
        $esercizio = new Esercizio($db);
        
        // Verifica se la classe Formula esiste e inizializzala
        $formula_exists = class_exists('Formula');
        if ($formula_exists) {
            $formula = new Formula($db);
        }
        
        // Cerca in ogni modello
        $piani_results = $piano->search($search_term);
        $esami_results = $esame->search($search_term);
        $argomenti_results = $argomento->search($search_term);
        $sottoargomenti_results = $sottoargomento->search($search_term);
        $esercizi_results = $esercizio->search($search_term);
        
        // Inizializza contatore dei risultati
        $total_results = 
            $piani_results->rowCount() + 
            $esami_results->rowCount() + 
            $argomenti_results->rowCount() + 
            $sottoargomenti_results->rowCount() + 
            $esercizi_results->rowCount();
        
        // Cerca nelle formule se esistono
        if ($formula_exists) {
            $formule_results = $formula->search($search_term);
            $total_results += $formule_results->rowCount();
        }
        
        if ($total_results == 0) {
            echo "<div class='message info'>Nessun risultato trovato per: " . htmlspecialchars($search_term) . "</div>";
        } else {
            echo "<h2>Risultati della ricerca per: " . htmlspecialchars($search_term) . "</h2>";
            echo "<p>Trovati " . $total_results . " elementi</p>";
        }
        
        // Funzione per mostrare i risultati di un modello
        function displayResults($stmt, $title, $link_prefix, $id_field = 'id', $name_field = 'nome', $description_field = 'descrizione') {
            if ($stmt && $stmt->rowCount() > 0) {
                echo "<div class='search-section'>";
                echo "<h3>" . $title . " (" . $stmt->rowCount() . ")</h3>";
                echo "<ul class='item-list search-results'>";
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<li>";
                    echo "<div class='item-title'>" . htmlspecialchars($row[$name_field]) . "</div>";
                    
                    // Mostra descrizione se disponibile
                    if (isset($row[$description_field]) && !empty($row[$description_field])) {
                        $description = $row[$description_field];
                        echo "<div class='item-description'>" . htmlspecialchars(substr($description, 0, 150)) . (strlen($description) > 150 ? "..." : "") . "</div>";
                    }
                    
                    // Mostra azioni specifiche per ogni tipo di elemento
                    echo "<div class='item-actions'>";
                    if ($title == "Piani di Studio") {
                        echo "<a href='esami.php?piano_id=" . $row[$id_field] . "' class='btn-primary'>Visualizza Esami</a> ";
                        echo "<a href='index.php?edit=" . $row[$id_field] . "' class='btn-secondary'>Modifica</a>";
                    } elseif ($title == "Esami") {
                        echo "<a href='argomenti.php?esame_id=" . $row[$id_field] . "' class='btn-primary'>Visualizza Argomenti</a> ";
                        echo "<a href='esami.php?edit=" . $row[$id_field] . "' class='btn-secondary'>Modifica</a>";
                    } elseif ($title == "Argomenti") {
                        echo "<a href='sottoargomenti.php?argomento_id=" . $row[$id_field] . "' class='btn-primary'>Visualizza Sottoargomenti</a> ";
                        echo "<a href='argomenti.php?edit=" . $row[$id_field] . "' class='btn-secondary'>Modifica</a>";
                    } elseif ($title == "Sottoargomenti") {
                        echo "<a href='esercizi.php?sottoargomento_id=" . $row[$id_field] . "' class='btn-primary'>Visualizza Esercizi</a> ";
                        echo "<a href='sottoargomenti.php?edit=" . $row[$id_field] . "' class='btn-secondary'>Modifica</a>";
                    } elseif ($title == "Esercizi") {
                        echo "<a href='requisiti.php?esercizio_id=" . $row[$id_field] . "' class='btn-primary'>Visualizza Requisiti</a> ";
                        echo "<a href='esercizi.php?edit=" . $row[$id_field] . "' class='btn-secondary'>Modifica</a>";
                    } elseif ($title == "Formule") {
                        echo "<a href='formule.php?edit=" . $row[$id_field] . "' class='btn-secondary'>Modifica</a>";
                    }
                    echo "</div>";
                    
                    echo "</li>";
                }
                
                echo "</ul>";
                echo "</div>";
            }
        }
        
        // Visualizza i risultati di ogni modello
        displayResults($piani_results, "Piani di Studio", "index.php?edit=");
        displayResults($esami_results, "Esami", "esami.php?edit=");
        displayResults($argomenti_results, "Argomenti", "argomenti.php?edit=", 'id', 'titolo');
        displayResults($sottoargomenti_results, "Sottoargomenti", "sottoargomenti.php?edit=", 'id', 'titolo');
        displayResults($esercizi_results, "Esercizi", "esercizi.php?edit=", 'id', 'titolo', 'testo');
        
        // Visualizza i risultati delle formule se esistono
        if ($formula_exists && isset($formule_results)) {
            displayResults($formule_results, "Formule", "formule.php?edit=");
        }
    }
}

// Includi footer
include_once '../ui/includes/footer.php';
?>