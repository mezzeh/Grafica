<?php
// File: pages/components/forms/edit_requisito.php

// Form per modificare un requisito
if (isset($_GET['edit']) && isset($_SESSION['user_id'])) {
    $requisito->id = $_GET['edit'];
    if ($requisito->readOne()) {
        // Ottieni gli argomenti associati
        $stmt_associati = $requisito->getAssociatedArgomenti($requisito->id);
        $argomenti_associati_ids = [];
        
        while ($row = $stmt_associati->fetch(PDO::FETCH_ASSOC)) {
            $argomenti_associati_ids[] = $row['id'];
        }
?>
<div id='editFormContainer'>
    <h2>Modifica Requisito</h2>
    <form action='' method='POST'>
        <input type='hidden' name='id' value='<?php echo $requisito->id; ?>'>
        
        <?php if (!$esercizio_id): ?>
            <?php $stmt_esercizi = $esercizio->readAll(); ?>
            
            <label for='esercizio_id'>Esercizio</label>
            <select name='esercizio_id' required>
                <?php while ($row_esercizio = $stmt_esercizi->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php $selected = ($requisito->esercizio_id == $row_esercizio['id']) ? "selected" : ""; ?>
                    <option value='<?php echo $row_esercizio['id']; ?>' <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($row_esercizio['titolo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php else: ?>
            <input type='hidden' name='esercizio_id' value='<?php echo $esercizio_id; ?>'>
            <div class='form-group'>
                <label>Esercizio</label>
                <div class='form-control-static'><?php echo htmlspecialchars($esercizio_info['titolo']); ?></div>
            </div>
        <?php endif; ?>
        
        <label for='descrizione'>Descrizione del Requisito</label>
        <textarea name='descrizione' rows='4' required><?php echo htmlspecialchars($requisito->descrizione); ?></textarea>
        
        <!-- Selezione degli argomenti correlati (opzionale) -->
        <label for='argomenti'>Argomenti Correlati (opzionale)</label>
        <div class='checkbox-group'>
            <?php 
            // Ottieni tutti gli argomenti disponibili
            $argomento = new Argomento($db);
            $stmt_argomenti = $argomento->readAll();
            
            while ($row_argomento = $stmt_argomenti->fetch(PDO::FETCH_ASSOC)): 
                $checked = in_array($row_argomento['id'], $argomenti_associati_ids) ? 'checked' : '';
            ?>
                <label class='checkbox-label'>
                    <input type='checkbox' name='argomenti[]' value='<?php echo $row_argomento['id']; ?>' <?php echo $checked; ?>>
                    <?php echo htmlspecialchars($row_argomento['titolo']); ?>
                </label>
            <?php endwhile; ?>
        </div>
        <small class="form-text text-muted">Seleziona gli argomenti correlati a questo requisito (facoltativo)</small>
        
        <button type='submit' name='update'>Aggiorna Requisito</button>
        <a href='requisiti.php<?php echo ($esercizio_id ? "?esercizio_id=$esercizio_id" : ""); ?>' class='btn-secondary'>Annulla</a>
    </form>
</div>
<?php
    }
}
?>