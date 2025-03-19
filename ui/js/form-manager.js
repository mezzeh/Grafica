/**
 * Script per la gestione dei form nell'applicazione
 */
document.addEventListener('DOMContentLoaded', function () {
    // Elementi per la gestione del form di creazione
    const showCreateFormBtn = document.getElementById('showCreateFormBtn');
    const createFormContainer = document.getElementById('createFormContainer');
    const cancelCreateBtn = document.getElementById('cancelCreateBtn');

    // Mostra il form di creazione
    if (showCreateFormBtn && createFormContainer) {
        showCreateFormBtn.addEventListener('click', function () {
            createFormContainer.style.display = 'block';
            this.style.display = 'none';
        });
    }

    // Nasconde il form di creazione
    if (cancelCreateBtn && createFormContainer && showCreateFormBtn) {
        cancelCreateBtn.addEventListener('click', function () {
            createFormContainer.style.display = 'none';
            showCreateFormBtn.style.display = 'inline-block';
        });
    }
});