/**
 * Script per la gestione dell'autocompletamento nella ricerca
 * di requisiti per sottoargomenti e esercizi correlati
 */
document.addEventListener('DOMContentLoaded', function () {
    // Seleziona tutti gli input di ricerca requisiti
    const requisitoSearchInputs = document.querySelectorAll('.requisito-search-input');

    // Per ogni input di ricerca
    requisitoSearchInputs.forEach(function (input) {
        // Crea un container per i risultati
        const resultsContainer = document.createElement('div');
        resultsContainer.className = 'search-results-dropdown';
        resultsContainer.style.display = 'none';
        input.parentNode.appendChild(resultsContainer);

        // Variabile per tenere traccia del timeout
        let searchTimeout;

        // Gestisci l'input di ricerca
        input.addEventListener('input', function () {
            const query = this.value.trim();
            const type = this.getAttribute('data-type') || 'all';

            // Pulisce il timeout precedente se presente
            clearTimeout(searchTimeout);

            // Se la query è vuota, nascondi i risultati
            if (query === '') {
                resultsContainer.style.display = 'none';
                return;
            }

            // Attendi 300ms prima di eseguire la ricerca (per evitare troppe richieste)
            searchTimeout = setTimeout(function () {
                // Percorso all'API di ricerca
                const apiUrl = '/api/search_avanzata.php';

                // Esegui la ricerca tramite AJAX
                fetch(`${apiUrl}?q=${encodeURIComponent(query)}&type=${type}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Errore nella risposta del server: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Svuota il container
                        resultsContainer.innerHTML = '';

                        // Se non ci sono risultati
                        if (data.length === 0) {
                            resultsContainer.innerHTML = '<div class="no-results">Nessun risultato trovato</div>';
                            resultsContainer.style.display = 'block';
                            return;
                        }

                        // Per ogni risultato
                        data.forEach(function (item) {
                            const resultItem = document.createElement('div');
                            resultItem.className = 'search-result-item';

                            // Icone per i diversi tipi
                            let icon = '';
                            switch (item.type) {
                                case 'argomento':
                                    icon = '📌';
                                    break;
                                case 'sottoargomento':
                                    icon = '📎';
                                    break;
                                case 'esercizio':
                                    icon = '📝';
                                    break;
                                case 'formula':
                                    icon = '➗'; // Icona per le formule
                                    break;
                            }

                            resultItem.innerHTML = `
                                <div class="result-icon">${icon}</div>
                                <div class="result-content">
                                    <div class="result-title">${item.name}</div>
                                    <div class="result-type">${capitalizeFirstLetter(item.type)}</div>
                                </div>
                            `;

                            // Al click sul risultato
                            resultItem.addEventListener('click', function () {
                                // Aggiorna il campo nascosto con l'ID del requisito
                                const hiddenField = document.getElementById(input.getAttribute('data-target'));
                                if (hiddenField) {
                                    hiddenField.value = `${item.type}|${item.id}`;

                                    // Trigger di un evento input per i listener
                                    const event = new Event('input', { bubbles: true });
                                    hiddenField.dispatchEvent(event);

                                    // Se il campo ha la proprietà value, forza una modifica di attributo
                                    // per gli observer di attributi
                                    if (hiddenField.hasAttribute('value')) {
                                        hiddenField.setAttribute('value', `${item.type}|${item.id}`);
                                    }
                                }

                                // Aggiorna il campo di input con il nome
                                input.value = item.name;

                                // Nascondi i risultati
                                resultsContainer.style.display = 'none';

                                // Aggiorna l'etichetta del tipo se presente
                                const typeLabel = document.getElementById(input.getAttribute('data-type-label'));
                                if (typeLabel) {
                                    let typeName = '';
                                    switch (item.type) {
                                        case 'argomento':
                                            typeName = 'Argomento';
                                            break;
                                        case 'sottoargomento':
                                            typeName = 'Sottoargomento';
                                            break;
                                        case 'esercizio':
                                            typeName = 'Esercizio';
                                            break;
                                        case 'formula':
                                            typeName = 'Formula';
                                            break;
                                    }
                                    typeLabel.textContent = typeName;
                                }
                            });

                            resultsContainer.appendChild(resultItem);
                        });

                        // Mostra i risultati
                        resultsContainer.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Errore nella ricerca:', error);
                        resultsContainer.innerHTML = '<div class="no-results">Errore durante la ricerca: ' + error.message + '</div>';
                        resultsContainer.style.display = 'block';
                    });
            }, 300);
        });

        // Chiudi i risultati quando si clicca fuori
        document.addEventListener('click', function (event) {
            if (!input.contains(event.target) && !resultsContainer.contains(event.target)) {
                resultsContainer.style.display = 'none';
            }
        });

        // Gestisci tasto Escape per chiudere i risultati
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                resultsContainer.style.display = 'none';
            }
        });
    });

    // Funzione per capitalizzare la prima lettera
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
});