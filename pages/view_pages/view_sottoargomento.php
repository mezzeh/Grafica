<?php
// File: pages/view_pages/view_sottoargomento.php
ob_start();

// Include path utilities
require_once dirname(dirname(__DIR__)) . '/config/paths.php';

// Includi header
include_once getAbsolutePath('ui/includes/header_view.php');

// Includi file di configurazione e modelli
include_once getAbsolutePath('config/database.php');
include_once getAbsolutePath('models/sottoargomento.php');
include_once getAbsolutePath('models/argomento.php');
include_once getAbsolutePath('models/esame.php');
include_once getAbsolutePath('models/esercizio.php');
include_once getAbsolutePath('models/sottoargomento_requisito.php');
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
$sottoargomento = new SottoArgomento($db);
$argomento = new Argomento($db);
$esame = new Esame($db);
$esercizio = new Esercizio($db);
$sottoargomentoRequisito = new SottoargomentoRequisito($db);

// Parametri GET
$sottoargomento_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$sottoargomento_id) {
    echo "<div class='message error'>Nessun sottoargomento specificato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica i dettagli del sottoargomento
$sottoargomento->id = $sottoargomento_id;
$sottoargomento_info = $sottoargomento->readOne();

if (!$sottoargomento_info) {
    echo "<div class='message error'>Sottoargomento non trovato.</div>";
    include_once getAbsolutePath('ui/includes/footer_view.php');
    exit;
}

// Carica le informazioni sull'argomento
$argomento->id = $sottoargomento_info['argomento_id'];
$argomento_info = $argomento->readOne();

// Carica le informazioni sull'esame
$esame->id = $argomento_info['esame_id'];
$esame_info = $esame->readOne();

// Genera il breadcrumb
echo "<div class='breadcrumb'>";
echo "<ul>";
echo "<li><a href='" . getUrlPath('pages/index.php') . "'>Home</a></li>";
echo "<li><a href='" . getUrlPath('pages/esami.php?piano_id=' . $esame_info['piano_id']) . "'>" . htmlspecialchars($esame_info['nome']) . "</a></li>";
echo "<li><a href='" . getUrlPath('pages/argomenti.php?esame_id=' . $argomento_info['esame_id']) . "'>" . htmlspecialchars($argomento_info['titolo']) . "</a></li>";
echo "<li>" . htmlspecialchars($sottoargomento_info['titolo']) . "</li>";
echo "</ul>";
echo "</div>";
?>

<div class="sottoargomento-view">
    <h2><?php echo htmlspecialchars($sottoargomento_info['titolo']); ?></h2>
    
    <div class="sottoargomento-meta">
        <p>Livello di profondità: <?php echo $sottoargomento_info['livello_profondita']; ?></p>
    </div>
    
    <div class="sottoargomento-description">
        <h3>Descrizione</h3>
        <div class="description-content">
            <?php echo nl2br(htmlspecialchars($sottoargomento_info['descrizione'])); ?>
        </div>
    </div>
    
    <!-- Sezione per i requisiti -->
    <div class="sottoargomento-requisiti">
        <h3>Requisiti Preliminari</h3>
        <?php
        // Carica i requisiti del sottoargomento
        $requisiti = $sottoargomentoRequisito->readBySottoargomento($sottoargomento_id);
        $requisiti_count = $requisiti->rowCount();
        
        if ($requisiti_count > 0):
        ?>
        <ul class="requisiti-list">
            <?php while ($row = $requisiti->fetch(PDO::FETCH_ASSOC)): ?>
                <li>
                    <?php 
                    // Crea il link appropriato in base al tipo di requisito
                    $link_text = htmlspecialchars($row['requisito_nome']);
                    $link_url = '';
                    
                    if ($row['requisito_tipo'] === 'argomento') {
                        $link_url = getUrlPath('pages/view_pages/view_argomento.php?id=' . $row['requisito_id']);
                    } else if ($row['requisito_tipo'] === 'sottoargomento') {
                        $link_url = getUrlPath('pages/view_pages/view_sottoargomento.php?id=' . $row['requisito_id']);
                    }
                    
                    if (!empty($link_url)) {
                        echo '<a href="' . $link_url . '">' . $link_text . '</a>';
                    } else {
                        echo $link_text;
                    }
                    
                    echo ' <span class="requisito-tipo">(' . ucfirst($row['requisito_tipo']) . ')</span>';
                    ?>
                </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p>Nessun requisito preliminare specificato per questo sottoargomento.</p>
        <?php endif; ?>
    </div>
    
    <div class="related-exercises">
        <h3>Esercizi relativi a questo sottoargomento</h3>
        
        <?php
        // Carica gli esercizi associati al sottoargomento
        $stmt = $esercizio->readBySottoArgomento($sottoargomento_id);
        
        if ($stmt->rowCount() > 0) {
            echo "<ul class='item-list'>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);
                
                // Determina la classe CSS in base alla difficoltà
                $difficolta_class = "difficulty-$difficolta";
                $difficolta_text = ($difficolta == 1) ? "Facile" : (($difficolta == 2) ? "Media" : "Difficile");
                
                echo "<li class='$difficolta_class'>
                        <div class='item-title'>" . htmlspecialchars($titolo) . "</div>
                        <div class='item-meta'>Difficoltà: $difficolta_text</div>
                        <div class='item-description'>" . nl2br(htmlspecialchars(substr($testo, 0, 150))) . (strlen($testo) > 150 ? "..." : "") . "</div>
                        <div class='item-actions'>
                            <a href='" . getUrlPath('pages/view_pages/view_esercizio.php?id=' . $id) . "'>Visualizza Esercizio</a>
                        </div>
                    </li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Nessun esercizio trovato per questo sottoargomento.</p>";
            if (isset($_SESSION['user_id'])) {
                echo "<a href='" . getUrlPath('pages/esercizi.php?sottoargomento_id=' . $sottoargomento_id) . "' class='btn-primary'>Aggiungi Esercizi</a>";
            }
        }
        ?>
    </div>
    
    <div class="sottoargomento-actions">
        <a href="<?php echo getUrlPath('pages/esercizi.php?sottoargomento_id=' . $sottoargomento_id); ?>" class="btn-primary">Gestisci Esercizi</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?php echo getUrlPath('pages/requisiti_sottoargomento.php?sottoargomento_id=' . $sottoargomento_id); ?>" class="btn-primary">Gestisci Requisiti</a>
            <a href="<?php echo getUrlPath('pages/sottoargomenti.php?edit=' . $sottoargomento_id . '&argomento_id=' . $argomento_info['id']); ?>" class="btn-secondary">Modifica Sottoargomento</a>
        <?php endif; ?>
    </div>
</div>

<?php
// Gestione dei commenti
$risultato_commenti = gestioneCommentiSottoargomenti($db, $sottoargomento_id);

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
renderCommentiSottoargomenti($db, $sottoargomento_id);

ob_end_flush();

include_once getAbsolutePath('ui/includes/footer_view.php');
?>