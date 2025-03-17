<?php
// Includi header
include_once '../ui/includes/header.php';

// Includi file di configurazione e modelli
include_once '../config/database.php';
include_once '../models/sottoargomento.php';
include_once '../models/argomento.php';
include_once '../models/sottoargomento_requisito.php';

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
    echo "<div class='message error'>Devi essere loggato per gestire i requisiti.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Inizializza modelli
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$sottoargomentoRequisito = new SottoargomentoRequisito($db);

// Parametri GET
$sottoargomento_id = isset($_GET['sottoargomento_id']) ? $_GET['sottoargomento_id'] : null;

if (!$sottoargomento_id) {
    echo "<div class='message error'>Nessun sottoargomento specificato.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Carica informazioni sul sottoargomento
$sottoargomento->id = $sottoargomento_id;
$sottoargomento_info = $sottoargomento->readOne();

if (!$sottoargomento_info) {
    echo "<div class='message error'>Sottoargomento non trovato.</div>";
    include_once '../ui/includes/footer.php';
    exit;
}

// Carica informazioni sull'argomento associato
$argomento->id = $sottoargomento_info['argomento_id'];
$argomento_info = $argomento->readOne();

// --- Gestione del form per aggiungere un nuovo requisito ---
if (isset($_POST['add_requisito'])) {
    $requisito_data = explode('|', $_POST['requisito_data']);
    
    if (count($requisito_data) === 2) {
        $sottoargomentoRequisito->sottoargomento_id = $sottoargomento_id;
        $sottoargomentoRequisito->requisito_tipo = $requisito_data[0];
        $sottoargomentoRequisito->requisito_id = $requisito_data[1];
        
        // Verifica che non sia già stato aggiunto questo requisito
        if (!$sottoargomentoRequisito->exists()) {
            if ($sottoargomentoRequisito->create()) {
                $message = "Requisito aggiunto con successo!";
                $message_class = "success";
            } else {
                $message = "Impossibile aggiungere il requisito.";
                $message_class = "error";
            }
        } else {
            $message = "Questo requisito è già stato aggiunto.";
            $message_class = "error";
        }
    } else {
        $message = "Dati del requisito non validi.";
        $message_class = "error";
    }
}

// --- Gestione dell'eliminazione di un requisito ---
if (isset($_GET['delete'])) {
    $sottoargomentoRequisito->id = $_GET['delete'];
    
    if ($sottoargomentoRequisito->delete()) {
        $message = "Requisito eliminato con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile eliminare il requisito.";
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
    ['text' => $sottoargomento_info['titolo'], 'link' => 'view_pages/view_sottoargomento.php?id=' . $sottoargomento_id],
    ['text' => 'Gestione Requisiti']
];
generaBreadcrumb($breadcrumb_items);

// Mostra messaggio se presente
if (!empty($message)) {
    echo "<div class='message $message_class'>$message</div>";
}
?>

<div class="header-with-button">
    <h2>Gestione Requisiti per: <?php echo htmlspecialchars($sottoargomento_info['titolo']); ?></h2>
</div>

<div class="requisiti-container">
    <div class="current-requisiti">
        <h3>Requisiti Attuali</h3>
        
        <?php
        // Carica i requisiti esistenti
        $requisiti = $sottoargomentoRequisito->readBySottoargomento($sottoargomento_id);
        $requisiti_count = $requisiti->rowCount();
        
        if ($requisiti_count > 0):
        ?>
        <ul class="item-list">
            <?php while ($row = $requisiti->fetch(PDO::FETCH_ASSOC)): ?>
            <li>
                <div class="item-title">
                    <?php echo htmlspecialchars($row['requisito_nome']); ?>
                </div>
                <div class="item-meta">
                    Tipo: <?php echo ucfirst($row['requisito_tipo']); ?>
                </div>
                <div class="item-actions">
                    <a href="?sottoargomento_id=<?php echo $sottoargomento_id; ?>&delete=<?php echo $row['id']; ?>" 
                       onclick="return confirm('Sei sicuro di voler rimuovere questo requisito?');">
                        Rimuovi
                    </a>
                </div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>Nessun requisito specificato per questo sottoargomento.</p>
        <?php endif; ?>
    </div>
    
    <div class="add-requisito-form">
        <h3>Aggiungi Nuovo Requisito</h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="requisito_search">Cerca Argomento o Sottoargomento</label>
                <div class="search-container">
                    <input type="text" 
                           id="requisito_search" 
                           class="requisito-search-input" 
                           placeholder="Inizia a digitare per cercare..." 
                           data-target="requisito_data"
                           data-type-label="requisito_tipo_label">
                    <input type="hidden" name="requisito_data" id="requisito_data">
                </div>
            </div>
            
            <div class="form-group">
                <label>Tipo di Requisito: <span id="requisito_tipo_label">-</span></label>
            </div>
            
            <button type="submit" name="add_requisito" class="btn-primary">Aggiungi Requisito</button>
        </form>
    </div>
</div>

<div class="form-actions" style="margin-top: 20px;">
    <a href="view_pages/view_sottoargomento.php?id=<?php echo $sottoargomento_id; ?>" class="btn-secondary">Torna al Sottoargomento</a>
</div>

<!-- Aggiungi JavaScript per l'autocompletamento -->
<script src="../ui/js/autocomplete_requisiti.js"></script>

<!-- I CSS sono inclusi nel file style.css -->

<?php include_once '../ui/includes/footer.php'; ?>