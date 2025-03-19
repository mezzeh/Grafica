<?php
// File: pages/components/forms/edit_argomento.php

// Form di modifica (se richiesto)
if ($edit_id && verificaPermessiPiano($db, $esame_id)): 
    $argomento->id = $edit_id;
    $edit_argomento = $argomento->readOne();
?>
<div id='editFormContainer'>
    <h2>Modifica Argomento</h2>
    <form action="?edit=<?php echo $edit_id; ?>&esame_id=<?php echo $esame_id; ?>" method="POST">
        <label for="titolo">Titolo Argomento</label>
        <input type="text" name="titolo" value="<?php echo htmlspecialchars($edit_argomento['titolo']); ?>" required>
        
        <label for="descrizione">Descrizione</label>
        <textarea name="descrizione"><?php echo htmlspecialchars($edit_argomento['descrizione']); ?></textarea>
        
        <label for="livello_importanza">Livello di Importanza</label>
        <select name="livello_importanza">
            <option value="1" <?php echo $edit_argomento['livello_importanza'] == 1 ? 'selected' : ''; ?>>Molto importante</option>
            <option value="2" <?php echo $edit_argomento['livello_importanza'] == 2 ? 'selected' : ''; ?>>Importante</option>
            <option value="3" <?php echo $edit_argomento['livello_importanza'] == 3 ? 'selected' : ''; ?>>Media importanza</option>
            <option value="4" <?php echo $edit_argomento['livello_importanza'] == 4 ? 'selected' : ''; ?>>Poco importante</option>
            <option value="5" <?php echo $edit_argomento['livello_importanza'] == 5 ? 'selected' : ''; ?>>Marginale</option>
        </select>
        
        <button type="submit" name="update">Aggiorna Argomento</button>
        <a href="argomenti.php?esame_id=<?php echo $esame_id; ?>" class="btn-secondary">Annulla</a>
    </form>
</div>
<?php endif; ?>