<?php
// File: pages/components/forms/edit_esame.php

// Controlla l'ID dell'esame da modificare
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : null;

// Verifica se l'utente ha i permessi
if (isset($_SESSION['user_id']) && $edit_id) {
    // Recupera i dati dell'esame
    $esame->id = $edit_id;
    $row = $esame->readOne();
    
    // Verifica se l'esame esiste
    if ($row === null) {
        echo "<div class='message error'>Esame non trovato.</div>";
        exit;
    }
    
    // Verifica dei permessi
    $can_edit = verificaPermessiPiano($db, $row['piano_id']) || 
                (isset($_SESSION['is_admin']) && $_SESSION['is_admin']);
    
    if (!$can_edit) {
        echo "<div class='message error'>Non hai i permessi per modificare questo esame.</div>";
        exit;
    }
?>
<div id='editFormContainer'>
    <h2>Modifica Esame</h2>
    <form action='' method='POST'>
        <!-- ID dell'esame da modificare -->
        <input type='hidden' name='id' value='<?php echo $edit_id; ?>'>
        
        <?php if ($piano_id): ?>
            <!-- Se siamo in una pagina di piano specifico -->
            <input type='hidden' name='piano_id' value='<?php echo $piano_id; ?>'>
            <div class='form-group'>
                <label>Piano di Studio</label><?php if (isset($row_piano) && $row_piano): ?>
    <option value='<?php echo $row_piano['id']; ?>'><?php echo htmlspecialchars($row_piano['nome']); ?></option>
<?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Mostra menu a tendina dei piani -->
            <?php $stmt_piani = $piano->readAll(); ?>
            <label for='piano_id'>Piano di Studio</label>
            <select name='piano_id' required>
                <?php while ($row_piano = $stmt_piani->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value='<?php echo $row_piano['id']; ?>'
                        <?php echo ($row_piano['id'] == $row['piano_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row_piano['nome']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php endif; ?>
        
        <label for='nome'>Nome Esame</label>
        <input type='text' name='nome' required value='<?php echo htmlspecialchars($row['nome']); ?>'>
        
        <label for='codice'>Codice Esame</label>
        <input type='text' name='codice' value='<?php echo htmlspecialchars($row['codice']); ?>'>
        
        <label for='crediti'>Crediti</label>
        <input type='number' name='crediti' min='1' max='30' 
               value='<?php echo intval($row['crediti']); ?>'>
        
        <label for='descrizione'>Descrizione</label>
        <textarea name='descrizione'><?php echo htmlspecialchars($row['descrizione']); ?></textarea>
        
        <button type='submit' name='update'>Aggiorna Esame</button>
        <button type='button' id='cancelEditBtn'>Annulla</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            // Torna alla pagina precedente
            window.location.href = window.location.pathname + 
                (<?php echo $piano_id ? "'?piano_id={$piano_id}'" : 'null'; ?> || '');
        });
    }
});
</script>
<?php
}
?>