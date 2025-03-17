<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/esercizio.php';
include_once '../models/sottoargomento.php';
include_once '../models/argomento.php';
include_once '../models/esercizio_correlato.php';

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    echo "<div class='message error'>Devi essere loggato per gestire gli esercizi correlati.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Inizializza modelli
$esercizio = new Esercizio($db);
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$esercizioCorrelato = new EsercizioCorrelato($db);

// Parametri GET
$esercizio_id = isset($_GET['esercizio_id']) ? $_GET['esercizio_id'] : null;

if (!$esercizio_id) {
    echo "<div class='message error'>Nessun esercizio specificato.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Carica informazioni sull'esercizio
$esercizio->id = $esercizio_id;
$esercizio_info = $esercizio->readOne();

if (!$esercizio_info) {
    echo "<div class='message error'>Esercizio non trovato.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Carica informazioni sul sottoargomento associato
$sottoargomento->id = $esercizio_info['sottoargomento_id'];
$sottoargomento_info = $sottoargomento->readOne();

// Carica informazioni sull'argomento associato
$argomento->id = $sottoargomento_info['argomento_id'];
$argomento_info = $argomento->readOne();

// --- Gestione del form per aggiungere un nuovo esercizio correlato ---
if (isset($_POST['add_correlato'])) {
    if (isset($_POST['esercizio_correlato_id']) && !empty($_POST['esercizio_correlato_id'])) {
        $esercizio_correlato_data = explode('|', $_POST['esercizio_correlato_id']);
        
        if (count($esercizio_correlato_data) === 2 && $esercizio_correlato_data[0] === 'esercizio') {
            $esercizioCorrelato->esercizio_id = $esercizio_id;
            $esercizioCorrelato->esercizio_correlato_id = $esercizio_correlato_data[1];
            $esercizioCorrelato->tipo_relazione = $_POST['tipo_relazione'];
            
            // Verifica che non sia lo stesso esercizio
            if ($esercizioCorrelato->esercizio_correlato_id == $esercizio_id) {
                $message = "Non puoi correlare un esercizio a se stesso.";
                $message_class = "error";
            } 
            // Verifica che non sia già stato aggiunto questo esercizio correlato
            else if (!$esercizioCorrelato->exists()) {
                if ($esercizioCorrelato->create()) {
                    $message = "Esercizio correlato aggiunto con successo!";
                    $message_class = "success";
                } else {
                    $message = "Impossibile aggiungere l'esercizio correlato.";
                    $message_class = "error";
                }
            } else {
                $message = "Questo esercizio è già stato correlato.";
                $message_class = "error";
            }
        } else {
            $message = "Dati dell'esercizio correlato non validi.";
            $message_class = "error";
        }
    } else {
        $message = "Nessun esercizio correlato selezionato.";
        $message_class = "error";
    }
}

// --- Gestione dell'eliminazione di un esercizio correlato ---
if (isset($_GET['delete'])) {
    $esercizioCorrelato->id = $_GET['delete'];
    
    if ($esercizioCorrelato->delete()) {
        $message = "Esercizio correlato rimosso con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile rimuovere l'esercizio correlato.";
        $message_class = "error";
    }
}

// Includi breadcrumb
include_once 'components/shared/breadcrumb.php';

// Genera il breadcrumb
$breadcrumb_items = [
    ['text' => 'Home', 'link' => 'index.php'],
    ['text' => 'Argomenti', 'link' => 'argomenti.php'],
    ['text' => $argomento_info['titolo'], 'link' => 'sottoargomenti.php?argomento_id=' . $argomento_info['id']],
    ['text' => $sottoargomento_info['titolo'], 'link' => 'esercizi.php?sottoargomento_id=' . $sottoargomento_info['id']],
    ['text' => $esercizio_info['titolo'], 'link' => 'view_esercizio.php?id=' . $esercizio_id],
    ['text' => 'Gestione Esercizi Correlati']
];
generaBreadcrumb($breadcrumb_items);

// Mostra messaggio se presente
if (!empty($message)) {
    echo "<div class='message $message_class'>$message</div>";
}
?>

<div class="header-with-button">
    <h2>Gestione Esercizi Correlati per: <?php echo htmlspecialchars($esercizio_info['titolo']); ?></h2>
</div>

<div class="correlati-container">
    <div class="current-correlati">
        <h3>Esercizi Correlati Attuali</h3>
        
        <?php
        // Carica gli esercizi correlati esistenti
        $correlati = $esercizioCorrelato->readByEsercizio($esercizio_id);
        $correlati_count = $correlati->rowCount();
        
        if ($correlati_count > 0):
        ?>
        <ul class="item-list">
            <?php while ($row = $correlati->fetch(PDO::FETCH_ASSOC)): ?>
            <li>
                <div class="item-title">
                    <?php echo htmlspecialchars($row['esercizio_correlato_titolo']); ?>
                </div>
                <div class="item-meta">
                    Tipo di relazione: <?php echo ucfirst($row['tipo_relazione']); ?>
                </div>
                <div class="item-actions">
                    <a href="view_pages/view_esercizio.php?id=<?php echo $row['esercizio_correlato_id']; ?>">
                        Visualizza Esercizio
                    </a> | 
                    <a href="?esercizio_id=<?php echo $esercizio_id; ?>&delete=<?php echo $row['id']; ?>" 
                       onclick="return confirm('Sei sicuro di voler rimuovere questo esercizio correlato?');">
                        Rimuovi
                    </a>
                </div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>Nessun esercizio correlato per questo esercizio.</p>
        <?php endif; ?>
    </div>
    
    <div class="add-correlato-form">
        <h3>Aggiungi Nuovo Esercizio Correlato</h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="esercizio_search">Cerca Esercizio</label>
                <div class="search-container">
                    <input type="text" 
                           id="esercizio_search" 
                           class="requisito-search-input" 
                           placeholder="Inizia a digitare per cercare..." 
                           data-type="esercizio"
                           data-target="esercizio_correlato_id">
                    <input type="hidden" name="esercizio_correlato_id" id="esercizio_correlato_id">
                </div>
            </div>
            
            <div class="form-group">
                <label for="tipo_relazione">Tipo di Relazione</label>
                <select name="tipo_relazione" id="tipo_relazione">
                    <option value="prerequisito">Prerequisito</option>
                    <option value="correlato">Correlato</option>
                    <option value="successivo">Successivo</option>
                    <option value="alternativo">Alternativo</option>
                </select>
            </div>
            
            <button type="submit" name="add_correlato" class="btn-primary">Aggiungi Esercizio Correlato</button>
        </form>
    </div>
</div>

<div class="form-actions" style="margin-top: 20px;">
    <a href="view_esercizio.php?id=<?php echo $esercizio_id; ?>" class="btn-secondary">Torna all'Esercizio</a>
</div>

<!-- Aggiungi JavaScript per l'autocompletamento -->
<script src="../ui/js/autocomplete_requisiti.js"></script>

<!-- I CSS sono inclusi nel file style.css -->

<?php include_once '../ui/includes/footer.php'; ?>