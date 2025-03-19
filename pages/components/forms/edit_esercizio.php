<?php
// File: pages/components/forms/edit_esercizio.php

// Form per modificare un esercizio
if (isset($_GET['edit']) && isset($_SESSION['user_id'])) {
    $esercizio->id = $_GET['edit'];
    if ($esercizio->readOne()) {
?>
<div id='editFormContainer'>
    <h2>Modifica Esercizio</h2>
    <form action='' method='POST'>
        <input type='hidden' name='id' value='<?php echo $esercizio->id; ?>'>
        
        <?php if (!$sottoargomento_id): ?>
            <?php $stmt_sottoargomenti = $sottoargomento->readAll(); ?>
            
            <label for='sottoargomento_id'>Sottoargomento</label>
            <select name='sottoargomento_id' required>
                <?php while ($row_sottoargomento = $stmt_sottoargomenti->fetch(PDO::FETCH_ASSOC)): ?>
                    <?php $selected = ($esercizio->sottoargomento_id == $row_sottoargomento['id']) ? "selected" : ""; ?>
                    <option value='<?php echo $row_sottoargomento['id']; ?>' <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($row_sottoargomento['titolo']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        <?php else: ?>
            <input type='hidden' name='sottoargomento_id' value='<?php echo $sottoargomento_id; ?>'>
            <div class='form-group'>
                <label>Sottoargomento</label>
                <div class='form-control-static'><?php echo htmlspecialchars($sottoargomento_info['titolo']); ?></div>
            </div>
        <?php endif; ?>
        
        <label for='titolo'>Titolo Esercizio</label>
        <input type='text' name='titolo' value='<?php echo htmlspecialchars($esercizio->titolo); ?>' required>
        
        <label for='testo'>Testo dell'Esercizio</label>
        <textarea name='testo' rows='6'><?php echo htmlspecialchars($esercizio->testo); ?></textarea>
        
        <label for='soluzione'>Soluzione</label>
        <textarea name='soluzione' rows='6'><?php echo htmlspecialchars($esercizio->soluzione); ?></textarea>
        
        <label for='difficolta'>Livello di Difficoltà</label>
        <select name='difficolta'>
            <option value='1' <?php echo ($esercizio->difficolta == 1) ? "selected" : ""; ?>>Facile</option>
            <option value='2' <?php echo ($esercizio->difficolta == 2) ? "selected" : ""; ?>>Media</option>
            <option value='3' <?php echo ($esercizio->difficolta == 3) ? "selected" : ""; ?>>Difficile</option>
        </select>
        
        <button type='submit' name='update'>Aggiorna Esercizio</button>
        <a href='esercizi.php<?php echo ($sottoargomento_id ? "?sottoargomento_id=$sottoargomento_id" : ""); ?>' class='btn-secondary'>Annulla</a>
    </form>
</div>
<?php
    }
}
?>