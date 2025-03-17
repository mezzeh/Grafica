<?php
// File: pages/components/forms/edit_piano.php

// Form per modificare un piano di studio
if (isset($_GET['edit']) && isset($_SESSION['user_id'])) {
    $piano->id = $_GET['edit'];
    $edit_piano_info = $piano->readOne();
    
    // Verifica se l'utente è il proprietario o admin
    if ($edit_piano_info && ($edit_piano_info['user_id'] == $_SESSION['user_id'] || isset($_SESSION['is_admin']) && $_SESSION['is_admin'])) {
?>
<div id='editFormContainer'>
    <h2>Modifica Piano di Studio</h2>
    <form action='' method='POST'>
        <input type='hidden' name='id' value='<?php echo $piano->id; ?>'>
        
        <label for='nome'>Nome</label>
        <input type='text' name='nome' value='<?php echo htmlspecialchars($edit_piano_info['nome']); ?>' required>
        
        <label for='descrizione'>Descrizione</label>
        <textarea name='descrizione' required><?php echo htmlspecialchars($edit_piano_info['descrizione']); ?></textarea>
        
        <label for='visibility'>Visibilità</label>
        <select name='visibility'>
            <option value='private' <?php echo ($edit_piano_info['visibility'] == 'private') ? 'selected' : ''; ?>>Privato</option>
            <option value='public' <?php echo ($edit_piano_info['visibility'] == 'public') ? 'selected' : ''; ?>>Pubblico</option>
        </select>
        
        <button type='submit' name='update'>Aggiorna Piano</button>
        <a href='<?php echo isset($_GET['from']) && $_GET['from'] == 'my' ? 'my_piani.php' : 'index.php'; ?>' class='btn-secondary'>Annulla</a>
    </form>
</div>
<?php
    } else {
        echo "<div class='message error'>Non hai i permessi per modificare questo piano di studio.</div>";
    }
}
?>