<?php
// File: pages/components/forms/create_esame.php

// Mostra il form solo se l'utente ha i permessi necessari
if (isset($_SESSION['user_id'])) {
    $can_create = ($piano_id && verificaPermessiPiano($db, $piano_id)) || 
                 (!$piano_id && isset($_SESSION['is_admin']) && $_SESSION['is_admin']);
    
    if ($can_create) {
?>
<div id='createFormContainer' style='display: none;'>
    <h2>Crea Nuovo Esame</h2>
    <form action='' method='POST'>
        
        <?php if ($piano_id): ?>
            <!-- Se siamo in una pagina di piano specifico, usa quel piano -->
            <input type='hidden' name='piano_id' value='<?php echo $piano_id; ?>'>
            <div class='form-group'>
                <label>Piano di Studio</label>
                <div class='form-control-static'><?php echo htmlspecialchars($piano_info['nome']); ?></div>
            </div>
        <?php else: ?>
            <!-- Altrimenti mostra il menu a tendina -->
            <?php $stmt_piani = $piano->readAll(); ?>
            
            <label for='piano_id'>Piano di Studio</label>
            <select name='piano_id' required>
                
                <?php while ($row_piano = $stmt_piani->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value='<?php echo $row_piano['id']; ?>'><?php echo htmlspecialchars($row_piano['nome']); ?></option>
                <?php endwhile; ?>
                
            </select>
        <?php endif; ?>
        
        <label for='nome'>Nome Esame</label>
        <input type='text' name='nome' required>
        
        <label for='codice'>Codice Esame</label>
        <input type='text' name='codice'>
        
        <label for='crediti'>Crediti</label>
        <input type='number' name='crediti' min='1' max='30' value='6'>
        
        <label for='descrizione'>Descrizione</label>
        <textarea name='descrizione'></textarea>
        
        <button type='submit' name='create'>Crea Esame</button>
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
}
?>