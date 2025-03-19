<?php
// File: pages/components/forms/edit_requisito.php

// Form per modificare un requisito
if (isset($_GET['edit']) && isset($_SESSION['user_id'])) {
    $requisito->id = $_GET['edit'];
    if ($requisito->readOne()) {
        // Ottieni gli argomenti associati
        $stmt_associati = $requisito->getAssociatedArgomenti($requisito->id);
        $argomenti_associati = [];
        
        while ($row = $stmt_associati->fetch(PDO::FETCH_ASSOC)) {
            $argomenti_associati[] = [
                'id' => $row['id'],
                'nome' => $row['titolo'],
                'tipo' => 'argomento'
            ];
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
        
        <!-- Ricerca avanzata di argomenti -->
        <label for="argomenti_search">Cerca Argomenti da Collegare</label>
        <div class="search-container">
            <input type="text" 
                   id="argomenti_search" 
                   class="requisito-search-input" 
                   placeholder="Inizia a digitare per cercare un argomento..." 
                   data-type="argomento"
                   data-target="argomento_selezionato">
            <input type="hidden" name="argomento_selezionato" id="argomento_selezionato">
        </div>
        
        <div class="selected-prerequisites-container">
            <label>Argomenti Selezionati</label>
            <div id="argomenti_selezionati" class="selected-tags">
                <?php foreach ($argomenti_associati as $arg): ?>
                <div class="selected-tag" data-id="<?php echo $arg['id']; ?>">
                    <span><?php echo htmlspecialchars($arg['nome']); ?></span>
                    <span class="tag-type">(<?php echo ucfirst($arg['tipo']); ?>)</span>
                    <button type="button" class="remove-tag" data-id="<?php echo $arg['id']; ?>">×</button>
                    <input type="hidden" name="argomenti[]" value="<?php echo $arg['id']; ?>">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <button type='submit' name='update'>Aggiorna Requisito</button>
        <a href='requisiti.php<?php echo ($esercizio_id ? "?esercizio_id=$esercizio_id" : ""); ?>' class='btn-secondary'>Annulla</a>
    </form>
</div>

<!-- Script per gestire la selezione multipla di argomenti -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementi del DOM
    const argomentiSearch = document.getElementById('argomenti_search');
    const argomentiSelezionatiContainer = document.getElementById('argomenti_selezionati');
    const argomentoSelezionatoInput = document.getElementById('argomento_selezionato');
    
    // Funzione per aggiungere un argomento selezionato
    function aggiungiArgomento(id, nome, tipo) {
        // Verifica se l'argomento è già stato selezionato
        if (!document.querySelector(`.selected-tag[data-id="${id}"]`)) {
            // Crea un elemento tag per l'argomento
            const tagElement = document.createElement('div');
            tagElement.className = 'selected-tag';
            tagElement.dataset.id = id;
            tagElement.innerHTML = `
                <span>${nome}</span>
                <span class="tag-type">(${tipo})</span>
                <button type="button" class="remove-tag" data-id="${id}">×</button>
                <input type="hidden" name="argomenti[]" value="${id}">
            `;
            
            // Aggiungi il tag al container
            argomentiSelezionatiContainer.appendChild(tagElement);
            
            // Pulisci il campo di ricerca
            argomentiSearch.value = '';
            argomentoSelezionatoInput.value = '';
        }
    }
    
    // Ascoltatore per l'input nascosto che viene aggiornato dall'autocompletamento
    if (argomentoSelezionatoInput) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    const valore = argomentoSelezionatoInput.value;
                    if (valore) {
                        const parti = valore.split('|');
                        if (parti.length === 2) {
                            const tipo = parti[0];
                            const id = parti[1];
                            const nome = argomentiSearch.value;
                            
                            aggiungiArgomento(id, nome, tipo);
                        }
                    }
                }
            });
        });
        
        observer.observe(argomentoSelezionatoInput, { attributes: true });
    }
    
    // Ascoltatore per rimuovere i tag
    argomentiSelezionatiContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-tag')) {
            const id = e.target.dataset.id;
            
            // Rimuovi il tag dal DOM
            const tagElement = e.target.closest('.selected-tag');
            if (tagElement) {
                tagElement.remove();
            }
        }
    });
});
</script>
<?php
    }
}
?>