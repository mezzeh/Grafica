<?php
// File: pages/components/forms/create_argomento.php

// Mostra il form di creazione solo se l'utente ha i permessi
if ($esame_id && verificaPermessiPiano($db, $esame_id)) {
?>
    <div id='createFormContainer' style='display: none;'>
        <h2>Crea Nuovo Argomento</h2>
        <form action="" method="POST">
            <input type="hidden" name="esame_id" value="<?php echo $esame_id; ?>">
            
            <label for="titolo">Titolo Argomento</label>
            <input type="text" name="titolo" required>
            
            <label for="descrizione">Descrizione</label>
            <textarea name="descrizione"></textarea>
            
            <label for="livello_importanza">Livello di Importanza</label>
            <select name="livello_importanza">
                <option value="1">Molto importante</option>
                <option value="2">Importante</option>
                <option value="3" selected>Media importanza</option>
                <option value="4">Poco importante</option>
                <option value="5">Marginale</option>
            </select>
            
            <button type="submit" name="create">Crea Argomento</button>
            <button type="button" id="cancelCreateBtn" class="btn-secondary">Annulla</button>
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