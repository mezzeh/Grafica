<?php
// File: pages/components/forms/create_esercizio.php

// Mostra il form solo se l'utente ha i permessi
if (isset($_SESSION['user_id'])) {
?>
<div id='createFormContainer' style='display: none;'>
    <h2>Crea Nuovo Esercizio</h2>
    <form action='' method='POST'>
        <?php if ($sottoargomento_id): ?>
            <input type='hidden' name='sottoargomento_id' value='<?php echo $sottoargomento_id; ?>'>
            <div class='form-group'>
                <label>Sottoargomento</label>
                <div class='form-control-static'><?php echo htmlspecialchars($sottoargomento_info['titolo']); ?></div>
            </div>
        <?php else: ?>
            <?php $stmt_sottoargomenti = $sottoargomento->readAll(); ?>
            
            <label for='sottoargomento_id'>Sottoargomento</label>
            <select name='sottoargomento_id' required>
                <?php while ($row_sottoargomento = $stmt_sottoargomenti->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value='<?php echo $row_sottoargomento['id']; ?>'>
                        <?php echo htmlspecialchars($row_sottoargomento['titolo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>
        
        <label for='titolo'>Titolo Esercizio</label>
        <input type='text' name='titolo' required>
        
        <label for='testo'>Testo dell'Esercizio</label>
        <textarea name='testo' rows='6'></textarea>
        
        <label for='soluzione'>Soluzione</label>
        <textarea name='soluzione' rows='6'></textarea>
        
        <label for='difficolta'>Livello di Difficoltà</label>
        <select name='difficolta'>
            <option value='1'>Facile</option>
            <option value='2' selected>Media</option>
            <option value='3'>Difficile</option>
        </select>
        
        <button type='submit' name='create'>Crea Esercizio</button>
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