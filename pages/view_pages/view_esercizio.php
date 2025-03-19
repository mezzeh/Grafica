<?php
// File: pages/view_pages/view_esercizio.php
ob_start();

// Include path utilities
require_once dirname(dirname(__DIR__)) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header_view.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/esercizio.php');
include_once getAbsolutePath('models/sottoargomento.php');
include_once getAbsolutePath('models/argomento.php');
include_once getAbsolutePath('models/esame.php');
include_once getAbsolutePath('models/esercizio_correlato.php');
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
$esercizio = new Esercizio($db);
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$esame = new Esame($db);
$esercizioCorrelato = new EsercizioCorrelato($db);

// Parametri GET
$esercizio_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$esercizio_id) {
    echo "<div class='message error'>Nessun esercizio specificato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica i dettagli dell'esercizio
$esercizio->id = $esercizio_id;
$esercizio_info = $esercizio->readOne();

if (!$esercizio_info) {
    echo "<div class='message error'>Esercizio non trovato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica le informazioni sul sottoargomento
$sottoargomento->id = $esercizio_info['sottoargomento_id'];
$sottoargomento_info = $sottoargomento->readOne();

// Carica le informazioni sull'argomento
$argomento->id = $sottoargomento_info['argomento_id'];
$argomento_info = $argomento->readOne();

// Carica le informazioni sull'esame
$esame->id = $argomento_info['esame_id'];
$esame_info = $esame->readOne();

// Includi breadcrumb
include_once getAbsolutePath('pages/components/shared/breadcrumb.php');

// Genera il breadcrumb
$breadcrumb_items = [
    ['text' => 'Home', 'link' => getUrlPath('pages/index.php')],
    ['text' => $esame_info['nome'], 'link' => getUrlPath('pages/view_pages/view_esame.php?id=' . $esame_info['id'])],
    ['text' => $argomento_info['titolo'], 'link' => getUrlPath('pages/view_pages/view_argomento.php?id=' . $argomento_info['id'])],
    ['text' => $sottoargomento_info['titolo'], 'link' => getUrlPath('pages/view_pages/view_sottoargomento.php?id=' . $sottoargomento_info['id'])],
    ['text' => $esercizio_info['titolo']]
];
generaBreadcrumb($breadcrumb_items);
?>

<div class="esercizio-view">
    <h2><?php echo htmlspecialchars($esercizio_info['titolo']); ?></h2>
    
    <div class="esercizio-meta">
        <p>Difficoltà: 
            <?php 
            switch($esercizio_info['difficolta']) {
                case 1: echo "Facile"; break;
                case 2: echo "Media"; break;
                case 3: echo "Difficile"; break;
                default: echo "Non specificata";
            }
            ?>
        </p>
    </div>
    
    <div class="esercizio-content">
        <h3>Testo dell'Esercizio</h3>
        <div class="esercizio-text">
            <?php echo nl2br(htmlspecialchars($esercizio_info['testo'])); ?>
        </div>
        
        <div class="solution-toggle">
            <button id="show-solution" class="btn-primary">Mostra Soluzione</button>
        </div>
        
        <div id="solution-content" class="esercizio-solution" style="display: none;">
            <h3>Soluzione</h3>
            <?php echo nl2br(htmlspecialchars($esercizio_info['soluzione'])); ?>
        </div>
    </div>
    
    <!-- Sezione per gli esercizi correlati -->
    <div class="esercizi-correlati">
        <h3>Esercizi Correlati</h3>
        <?php
        // Carica gli esercizi correlati
        $correlati = $esercizioCorrelato->readByEsercizio($esercizio_id);
        $correlati_count = $correlati->rowCount();
        
        if ($correlati_count > 0):
        ?>
        <ul class="correlati-list">
            <?php while ($row = $correlati->fetch(PDO::FETCH_ASSOC)): ?>
                <li>
                    <a href="<?php echo getUrlPath('pages/view_pages/view_esercizio.php?id=' . $row['esercizio_correlato_id']); ?>">
                        <?php echo htmlspecialchars($row['esercizio_correlato_titolo']); ?>
                    </a>
                    <span class="relazione-tipo">(<?php echo ucfirst($row['tipo_relazione']); ?>)</span>
                </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>Nessun esercizio correlato per questo esercizio.</p>
        <?php endif; ?>
    </div>
    
    <div class="esercizio-actions">
        <a href="<?php echo getUrlPath('pages/requisiti.php?esercizio_id=' . $esercizio_info['id']); ?>" class="btn-primary">Requisiti</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo getUrlPath('pages/esercizi_correlati.php?esercizio_id=' . $esercizio_info['id']); ?>" class="btn-primary">Gestisci Esercizi Correlati</a>
            <a href="<?php echo getUrlPath('pages/esercizi.php?edit=' . $esercizio_info['id'] . '&sottoargomento_id=' . $sottoargomento_info['id']); ?>" class="btn-secondary">Modifica Esercizio</a>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showSolutionBtn = document.getElementById('show-solution');
    const solutionContent = document.getElementById('solution-content');
    
    if (showSolutionBtn && solutionContent) {
        showSolutionBtn.addEventListener('click', function() {
            if (solutionContent.style.display === 'none') {
                solutionContent.style.display = 'block';
                showSolutionBtn.textContent = 'Nascondi Soluzione';
            } else {
                solutionContent.style.display = 'none';
                showSolutionBtn.textContent = 'Mostra Soluzione';
            }
        });
    }
});
</script>

<?php
// Gestione e rendering dei commenti
$risultato_commenti = gestioneCommentiEsercizi($db, $esercizio_id);

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
renderCommentiEsercizi($db, $esercizio_id);

ob_end_flush();

include_once getAbsolutePath('ui/includes/footer_view.php');
?>