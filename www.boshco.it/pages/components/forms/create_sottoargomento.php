<?php
// File: pages/components/forms/create_sottoargomento.php

// Mostra il form solo se l'utente ha i permessi
if ($argomento_id && isset($_SESSION['user_id'])) {
    // Verifica permessi (puoi adattare questa funzione alle tue esigenze)
    $can_create = true; // Aggiungi qui la tua logica di verifica dei permessi
    
    if ($can_create) {
?>
<div id='createFormContainer' style='display: none;'>
    <h2>Crea Nuovo Sottoargomento</h2>
    <form action='' method='POST'>
        <input type='hidden' name='argomento_id' value='<?php echo $argomento_id; ?>'>
        
        <label for='titolo'>Titolo Sottoargomento</label>
        <input type='text' name='titolo' required>
        
        <label for='descrizione'>Descrizione</label>
        <textarea name='descrizione'></textarea>
        
        <label for='livello_profondita'>Livello di Profondità (1-5)</label>
        <select name='livello_profondita'>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value='<?php echo $i; ?>' <?php echo ($i == 1) ? "selected" : ""; ?>>Livello <?php echo $i; ?></option>
            <?php endfor; ?>
        </select>
        
        
        
        <button type='submit' name='create'>Crea Sottoargomento</button>
        <button type='button' id='cancelCreateBtn' class='btn-secondary'>Annulla</button>
    </form>
</div>

<!-- Inizializza i dati per l'autocompletamento -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inizializza gli array vuoti per l'autocompletamento
        window.preselectedArgomenti = [];
        window.preselectedSottoargomenti = [];
    });
</script>

<!-- Includi lo script per l'autocompletamento -->
<script src="../ui/js/prerequisiti-autocomplete.js"></script>

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
}
?>