/**
 * Sistema di ricerca con suggerimenti in tempo reale
 */
document.addEventListener('DOMContentLoaded', function () {
    // Elementi della ricerca
    const searchInput = document.querySelector('.search-form input[type="text"]');
    const searchForm = document.querySelector('.search-form');
    const searchResults = document.getElementById('search-results');

    if (!searchInput || !searchResults) {
        console.error('Elementi di ricerca non trovati nella pagina');
        return;
    }

    // Determina il percorso base per l'API di ricerca
    let apiPath = '../api/search.php';

    // Adatta il percorso in base al nome del file corrente
    const currentPage = window.location.pathname.split('/').pop();
    if (currentPage.startsWith('view_')) {
        apiPath = '../api/search.php'; // Manteniamo lo stesso percorso per le pagine view_
    }

    console.log('Sistema di ricerca inizializzato, API path:', apiPath);

    // Imposta un timeout per il debouncing della ricerca
    let searchTimeout = null;

    // Icone per i tipi di risultati
    const typeIcons = {
        'piano': '📚',
        'esame': '📝',
        'argomento': '📌'
    };

    // Etichette per i tipi di risultati
    const typeLabels = {
        'piano': 'Piano di Studio',
        'esame': 'Esame',
        'argomento': 'Argomento'
    };

    // Gestione dell'input di ricerca
    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        // Se la query è vuota, nascondi i risultati
        if (query === '') {
            searchResults.style.display = 'none';
            return;
        }

        // Annulla la precedente ricerca in attesa
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        // Imposta un nuovo timeout per evitare troppe richieste
        searchTimeout = setTimeout(function () {
            fetch(apiPath + '?q=' + encodeURIComponent(query))
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Errore di rete: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    // Mostra i risultati
                    if (data.length > 0) {
                        searchResults.innerHTML = '';

                        data.forEach(item => {
                            const resultItem = document.createElement('div');
                            resultItem.className = 'search-result-item';
                            resultItem.innerHTML = `
                                <div class="result-icon">${typeIcons[item.type] || '🔍'}</div>
                                <div class="result-content">
                                    <div class="result-title">${highlightMatch(item.name, query)}</div>
                                    <div class="result-type">${typeLabels[item.type] || 'Risultato'}</div>
                                    <div class="result-description">${highlightMatch(item.description, query)}</div>
                                </div>
                            `;

                            // Rendi cliccabile l'intero elemento di risultato
                            resultItem.addEventListener('click', function () {
                                window.location.href = item.url;
                            });

                            searchResults.appendChild(resultItem);
                        });

                        searchResults.style.display = 'block';
                    } else {
                        searchResults.innerHTML = '<div class="no-results">Nessun risultato trovato</div>';
                        searchResults.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Errore nella ricerca:', error);
                    searchResults.innerHTML = '<div class="no-results">Errore nella ricerca: ' + error.message + '</div>';
                    searchResults.style.display = 'block';
                });
        }, 300); // Attesa di 300ms dopo l'ultimo input
    });

    // Gestione del click fuori dall'area di ricerca
    document.addEventListener('click', function (event) {
        if (!searchForm.contains(event.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Gestione della pressione dei tasti
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            searchResults.style.display = 'none';
        }
    });

    // Evita l'invio del form quando si preme Invio su un risultato
    searchResults.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    });

    // Previeni l'invio del form se non c'è testo di ricerca
    searchForm.addEventListener('submit', function (event) {
        if (searchInput.value.trim() === '') {
            event.preventDefault();
        }
    });

    // Funzione per evidenziare le parti corrispondenti
    function highlightMatch(text, query) {
        if (!text) return '';

        const index = text.toLowerCase().indexOf(query.toLowerCase());
        if (index >= 0) {
            return text.substring(0, index) +
                '<strong class="highlight">' + text.substring(index, index + query.length) + '</strong>' +
                text.substring(index + query.length);
        }
        return text;
    }
});