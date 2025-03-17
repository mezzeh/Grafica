<?php
// File: pages/components/forms/create_requisito.php

// Mostra il form di creazione solo se l'utente ha i permessi
if ($esercizio_id && isset($_SESSION['user_id'])) {
?>
<div id='createFormContainer' style='display: none;'>
    <h2>Crea Nuovo Requisito</h2>
    <form action='' method='POST'>
        <input type='hidden' name='esercizio_id' value='<?php echo $esercizio_id; ?>'>
        
        <label for='descrizione'>Descrizione del Requisito</label>
        <textarea name='descrizione' rows='4' required></textarea>
        
        <!-- Selezione degli argomenti correlati (opzionale) -->
        <label for='argomenti'>Argomenti Correlati (opzionale)</label>
        <div class='checkbox-group'>
            <?php 
            // Ottieni tutti gli argomenti disponibili
            $argomento = new Argomento($db);
            $stmt_argomenti = $argomento->readAll();
            
            while ($row_argomento = $stmt_argomenti->fetch(PDO::FETCH_ASSOC)): 
            ?>
                <label class='checkbox-label'>
                    <input type='checkbox' name='argomenti[]' value='<?php echo $row_argomento['id']; ?>'>
                    <?php echo htmlspecialchars($row_argomento['titolo']); ?>
                </label>
            <?php endwhile; ?>
        </div>
        <small class="form-text text-muted">Seleziona gli argomenti correlati a questo requisito (facoltativo)</small>
        
        <button type='submit' name='create'>Crea Requisito</button>
        <button type='button' id='cancelCreateBtn' class='btn-secondary'>Annulla</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const showCreateFormBtn = document.getElementById('showCreateFormBtn');
    const createFormContainer = document.getElementById('createFormContainer');
    const cancelCreateBtn = document.getElementById('cancelCreateBtn');
    
    if (showCreateFormBtn && createFormContainer) {
        showCreateFormBtn.addEventListener('click', function() {
            createFormContainer.style.display = 'block';
            showCreateFormBtn.style.display = 'none';
        });
    }
    
    if (cancelCreateBtn && createFormContainer && showCreateFormBtn) {
        cancelCreateBtn.addEventListener('click', function() {
            createFormContainer.style.display = 'none';
            showCreateFormBtn.style.display = 'inline-block';
        });
    }
});
</script>
<?php
}
?>