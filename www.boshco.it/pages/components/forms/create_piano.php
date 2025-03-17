<?php
// File: pages/components/forms/create_piano.php

// Mostra il form solo se l'utente è loggato
if (isset($_SESSION['user_id'])) {
?>
<div id='createFormContainer' style='display: none;'>
    <h2>Crea Nuovo Piano di Studio</h2>
    <form action='' method='POST'>
        <label for='nome'>Nome</label>
        <input type='text' name='nome' required>
        
        <label for='descrizione'>Descrizione</label>
        <textarea name='descrizione' required></textarea>
        
        <label for='visibility'>Visibilità</label>
        <select name='visibility'>
            <option value='private'>Privato</option>
            <option value='public'>Pubblico</option>
        </select>
        
        <button type='submit' name='create'>Crea Piano</button>
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