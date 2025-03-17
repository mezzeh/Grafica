/**
 * Common JavaScript functionality for Study Plan Management System
 */

/**
 * Live Search Implementation for Sistema Gestione Piani di Studio
 * 
 * This script adds real-time search functionality to the search form in the header.
 * As the user types, it sends AJAX requests to the search API and displays results
 * in a dropdown below the search input.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const searchForm = document.querySelector('.search-form');
    if (!searchForm) return;

    const searchInput = searchForm.querySelector('input[name="q"]');
    let searchResultsContainer = document.getElementById('search-results');

    // If the search results container doesn't exist, create it
    if (!searchResultsContainer) {
        const resultsDiv = document.createElement('div');
        resultsDiv.id = 'search-results';
        resultsDiv.className = 'search-results-dropdown';
        searchForm.appendChild(resultsDiv);
        searchResultsContainer = resultsDiv;
    }

    // Minimum number of characters before triggering search
    const MIN_CHARS = 2;

    // Debounce timer
    let debounceTimer;

    // Get the site base path from the form action
    const formAction = searchForm.getAttribute('action');
    const basePath = formAction.substring(0, formAction.lastIndexOf('/pages/')) || '';

    // Search function
    const performSearch = (query) => {
        // Clear previous timer
        clearTimeout(debounceTimer);

        // Set a new timer
        debounceTimer = setTimeout(() => {
            // Don't search if query is too short
            if (query.length < MIN_CHARS) {
                searchResultsContainer.style.display = 'none';
                return;
            }

            // Make the AJAX request to the API
            fetch(`${basePath}/api/search.php?q=${encodeURIComponent(query)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    displayResults(data, query);
                })
                .catch(error => {
                    console.error('Error in search:', error);
                });
        }, 300); // 300ms debounce
    };

    // Function to display search results
    const displayResults = (results, query) => {
        // Clear previous results
        searchResultsContainer.innerHTML = '';

        // Hide the container if no results
        if (results.length === 0) {
            searchResultsContainer.style.display = 'none';
            return;
        }

        // Create results HTML
        results.forEach(item => {
            const resultItem = document.createElement('div');
            resultItem.className = 'search-result-item';

            // Determine icon based on type
            let icon = '';
            if (item.type === 'piano') icon = '📚';
            else if (item.type === 'esame') icon = '📝';
            else if (item.type === 'argomento') icon = '📌';

            // Ensure URL is correct - prepend basePath if needed
            let url = item.url;
            if (url && !url.startsWith('http') && !url.startsWith('/')) {
                url = `${basePath}/pages/${url}`;
            }

            // Build the result item HTML
            resultItem.innerHTML = `
                <a href="${url}">
                    <div class="result-icon">${icon}</div>
                    <div class="result-content">
                        <div class="result-title">${highlightMatch(item.name, query)}</div>
                        <div class="result-type">${capitalizeFirstLetter(item.type)}</div>
                        ${item.description ? `<div class="result-description">${item.description}</div>` : ''}
                    </div>
                </a>
            `;

            searchResultsContainer.appendChild(resultItem);
        });

        // Show the results container
        searchResultsContainer.style.display = 'block';
    };

    // Helper function to highlight search term in results
    const highlightMatch = (text, query) => {
        if (!text) return '';

        const regex = new RegExp(`(${escapeRegExp(query)})`, 'gi');
        return text.replace(regex, '<strong>$1</strong>');
    };

    // Helper function to escape special characters in a string for regex
    const escapeRegExp = (string) => {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    };

    // Helper function to capitalize first letter
    const capitalizeFirstLetter = (string) => {
        return string.charAt(0).toUpperCase() + string.slice(1);
    };

    // Event listeners
    if (searchInput) {
        // Input event for real-time searching
        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            performSearch(query);
        });

        // Focus event to show results if there's a query
        searchInput.addEventListener('focus', function () {
            const query = this.value.trim();
            if (query.length >= MIN_CHARS) {
                performSearch(query);
            }
        });

        // Click outside to close results
        document.addEventListener('click', function (event) {
            if (!searchForm.contains(event.target)) {
                searchResultsContainer.style.display = 'none';
            }
        });

        // Prevent form submission when pressing Enter in the search field
        // Instead, we'll redirect to the full search page
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                window.location.href = formAction + '?q=' + encodeURIComponent(this.value.trim());
            }
        });
    }

    /**
     * Form Toggle Functionality for create/edit forms
     */
    // Elements for the form toggling
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