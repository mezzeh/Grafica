<?php
// Attiva output buffering per prevenire errori di header già inviati
ob_start();

// Include path utilities
require_once dirname(__DIR__) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/esercizio.php');
include_once getAbsolutePath('models/sottoargomento.php');
include_once getAbsolutePath('models/argomento.php');
include_once getAbsolutePath('models/formula.php');

// Inizializza variabili per messaggi
$message = "";
$message_class = "";

// Connessione al database
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "<div class='message error'>Problema di connessione al database.</div>";
    include_once getAbsolutePath('ui/includes/footer.php');
    exit;
}

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    echo "<div class='message error'>Devi essere loggato per gestire le formule associate.</div>";
    include_once getAbsolutePath('ui/includes/footer.php');
    exit;
}

// Inizializza modelli
$esercizio = new Esercizio($db);
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$formula = new Formula($db);

// Parametri GET
$esercizio_id = isset($_GET['esercizio_id']) ? $_GET['esercizio_id'] : null;

if (!$esercizio_id) {
    echo "<div class='message error'>Nessun esercizio specificato.</div>";
    include_once getAbsolutePath('ui/includes/footer.php');
    exit;
}

// Carica informazioni sull'esercizio
$esercizio->id = $esercizio_id;
$esercizio_info = $esercizio->readOne();

if (!$esercizio_info) {
    echo "<div class='message error'>Esercizio non trovato.</div>";
    include_once getAbsolutePath('ui/includes/footer.php');
    exit;
}

// Carica informazioni sul sottoargomento associato
$sottoargomento->id = $esercizio_info['sottoargomento_id'];
$sottoargomento_info = $sottoargomento->readOne();

// Carica informazioni sull'argomento associato
$argomento->id = $sottoargomento_info['argomento_id'];
$argomento_info = $argomento->readOne();

// Funzione per il reindirizzamento sicuro
function safeRedirect($url) {
    if (headers_sent()) {
        echo "<script>window.location.href='" . $url . "';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=" . $url . "'></noscript>";
        exit;
    } else {
        header("Location: " . $url);
        exit;
    }
}

// --- Gestione del form per aggiungere una nuova formula associata ---
if (isset($_POST['add_formula'])) {
    if (isset($_POST['formula_id']) && !empty($_POST['formula_id'])) {
        $formula_data = explode('|', $_POST['formula_id']);
        
        if (count($formula_data) === 2 && $formula_data[0] === 'formula') {
            $formula_id = $formula_data[1];
            
            // Verifica che non sia già stata aggiunta questa formula
            if (!$esercizio->isFormulaAssociated($esercizio_id, $formula_id)) {
                if ($esercizio->addFormula($esercizio_id, $formula_id)) {
                    $message = "Formula associata con successo!";
                    $message_class = "success";
                    
                    // Reindirizza per evitare ripresentazione del form
                    $redirect_url = getUrlPath("pages/esercizio_formule.php?esercizio_id={$esercizio_id}&success=created");
                    safeRedirect($redirect_url);
                } else {
                    $message = "Impossibile associare la formula.";
                    $message_class = "error";
                }
            } else {
                $message = "Questa formula è già associata all'esercizio.";
                $message_class = "error";
            }
        } else {
            $message = "Dati della formula non validi.";
            $message_class = "error";
        }
    } else {
        $message = "Nessuna formula selezionata.";
        $message_class = "error";
    }
}

// --- Gestione dell'eliminazione di una formula associata ---
if (isset($_GET['delete'])) {
    $formula_id = $_GET['delete'];
    
    if ($esercizio->removeFormula($esercizio_id, $formula_id)) {
        $message = "Formula rimossa con successo!";
        $message_class = "success";
    } else {
        $message = "Impossibile rimuovere la formula.";
        $message_class = "error";
    }
}

// Gestione dei messaggi di successo da reindirizzamento
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'created':
            $message = "Formula associata con successo!";
            $message_class = "success";
            break;
    }
}

// Includi breadcrumb
include_once getAbsolutePath('pages/components/shared/breadcrumb.php');

// Genera il breadcrumb
$breadcrumb_items = [
    ['text' => 'Home', 'link' => getUrlPath('pages/index.php')],
    ['text' => 'Esercizi', 'link' => getUrlPath('pages/esercizi.php')],
    ['text' => $esercizio_info['titolo'], 'link' => getUrlPath('pages/view_pages/view_esercizio.php?id=' . $esercizio_id)],
    ['text' => 'Gestione Formule']
];
generaBreadcrumb($breadcrumb_items);

// Mostra messaggio se presente
if (!empty($message)) {
    echo "<div class='message $message_class'>$message</div>";
}
?>

<div class="header-with-button">
    <h2>Gestione Formule per l'Esercizio: <?php echo htmlspecialchars($esercizio_info['titolo']); ?></h2>
</div>

<div class="formule-container">
    <div class="current-formule">
        <h3>Formule Attuali</h3>
        
        <?php
        // Carica le formule esistenti
        $formule = $esercizio->getAssociatedFormule($esercizio_id);
        $formule_count = $formule->rowCount();
        
        if ($formule_count > 0):
        ?>
        <ul class="item-list">
            <?php while ($row = $formule->fetch(PDO::FETCH_ASSOC)): ?>
            <li>
                <div class="item-title">
                    <?php echo htmlspecialchars($row['nome']); ?>
                </div>
                <div class="item-description">
                    <div class="formula-expression"><?php echo htmlspecialchars($row['espressione']); ?></div>
                    <?php if (!empty($row['descrizione'])): ?>
                        <p><?php echo htmlspecialchars($row['descrizione']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="item-actions">
                    <a href="<?php echo getUrlPath('pages/view_pages/view_formula.php?id=' . $row['id']); ?>">
                        Visualizza Formula
                    </a> | 
                    <a href="?esercizio_id=<?php echo $esercizio_id; ?>&delete=<?php echo $row['id']; ?>" 
                       onclick="return confirm('Sei sicuro di voler rimuovere questa formula?');">
                        Rimuovi
                    </a>
                </div>
            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>Nessuna formula associata a questo esercizio.</p>
        <?php endif; ?>
    </div>
    
    <div class="add-formula-form">
        <h3>Aggiungi Nuova Formula</h3>
        <form action="" method="POST">
            <div class="form-group">
                <label for="formula_search">Cerca Formula</label>
                <div class="search-container">
                    <input type="text" 
                           id="formula_search" 
                           class="requisito-search-input" 
                           placeholder="Inizia a digitare per cercare una formula..." 
                           data-type="formula"
                           data-target="formula_id">
                    <input type="hidden" name="formula_id" id="formula_id">
                </div>
            </div>
            
            <button type="submit" name="add_formula" class="btn-primary">Associa Formula</button>
        </form>
    </div>
</div>

<div class="form-actions" style="margin-top: 20px;">
    <a href="<?php echo getUrlPath('pages/view_pages/view_esercizio.php?id=' . $esercizio_id); ?>" class="btn-secondary">Torna all'Esercizio</a>
</div>

<!-- Aggiungi JavaScript per l'autocompletamento -->
<script src="<?php echo getUrlPath('ui/js/autocomplete_requisiti.js'); ?>"></script>

<?php 
include_once getAbsolutePath('ui/includes/footer.php');
ob_end_flush();
?>