{{-- Dashboard Search Component --}}
<div class="dashboard-search-wrapper">
    <form id="dashboard-search-form" action="{{ route('search') }}" method="GET" style="width: 100%;">
        <div style="position: relative; display: flex; align-items: center;">
            <label for="dashboard-search-input" class="visually-hidden">Search dashboard</label>
            <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input
                type="search"
                id="dashboard-search-input"
                name="q"
                class="form-control dashboard-search-input"
                placeholder="Search dashboard..."
                aria-label="Search dashboard"
                autocomplete="off"
                minlength="2"
                maxlength="100"
            >
            <button
                type="button"
                id="dashboard-search-clear"
                class="btn-search-clear"
                aria-label="Clear search"
                style="display: none;"
            >
                <svg class="search-clear-icon" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"></path>
                </svg>
            </button>
        </div>

        <div
            id="dashboard-search-results"
            class="search-results-dropdown"
            role="listbox"
            aria-label="Search results"
            style="display: none;"
        >
            <div id="dashboard-search-results-list" style="max-height: 300px; overflow-y: auto;"></div>
            <div id="dashboard-search-no-results" class="px-3 py-3 text-center text-muted" style="display: none;">
                <p class="small">No results found. Try different keywords.</p>
            </div>
            <div id="dashboard-search-loading" class="px-3 py-3 text-center text-muted" style="display: none;">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="small mt-2">Searching...</p>
            </div>
            <div id="dashboard-search-error" class="px-3 py-3 text-center text-danger" style="display: none;">
                <p class="small">Error searching. Please try again.</p>
            </div>
        </div>
    </form>
</div>

<style>
    .dashboard-search-wrapper {
        width: 100%;
        position: relative;
    }

    .dashboard-search-input {
        padding-left: 2.25rem;
        padding-right: 2.25rem;
        font-size: 0.875rem;
        border: 1px solid var(--border-color);
        background-color: var(--bg-primary);
        min-height: 38px;
    }

    .dashboard-search-input:focus {
        border-color: var(--navy-600);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(61, 106, 80, 0.1);
    }

    .dashboard-search-input::placeholder {
        color: var(--text-light);
    }

    .search-icon {
        position: absolute;
        left: 0.65rem;
        width: 18px;
        height: 18px;
        color: var(--text-light);
        pointer-events: none;
    }

    .btn-search-clear {
        position: absolute;
        right: 0.65rem;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0.35rem;
        color: var(--text-muted);
        transition: color var(--transition-speed);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-search-clear:hover {
        color: var(--text-main);
    }

    .btn-search-clear:focus {
        outline: 2px solid var(--navy-600);
        outline-offset: 2px;
    }

    .search-clear-icon {
        width: 18px;
        height: 18px;
    }

    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        margin-top: 0.5rem;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-elevated);
        z-index: 1050;
    }

    .search-result-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: background-color var(--transition-speed);
        border-bottom: 1px solid var(--border-color);
    }

    .search-result-item:last-child {
        border-bottom: none;
    }

    .search-result-item:hover,
    .search-result-item.selected {
        background-color: var(--bg-primary);
    }

    .search-result-item:focus-visible {
        outline: 2px solid var(--navy-600);
        outline-offset: -2px;
    }

    .search-result-icon {
        width: 20px;
        height: 20px;
        margin-right: 0.75rem;
        flex-shrink: 0;
        color: var(--text-muted);
    }

    .search-result-content {
        flex: 1;
        min-width: 0;
    }

    .search-result-title {
        font-weight: 500;
        color: var(--text-main);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 0.875rem;
    }

    .search-result-subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-top: 0.25rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('dashboard-search-input');
        const searchForm = document.getElementById('dashboard-search-form');
        const clearBtn = document.getElementById('dashboard-search-clear');
        const resultsContainer = document.getElementById('dashboard-search-results');
        const resultsList = document.getElementById('dashboard-search-results-list');
        const noResults = document.getElementById('dashboard-search-no-results');
        const loading = document.getElementById('dashboard-search-loading');
        const error = document.getElementById('dashboard-search-error');

        if (!searchInput || !searchForm || !clearBtn || !resultsContainer || !resultsList || !noResults || !loading || !error) {
            return;
        }

        let searchTimeout;
        let selectedIndex = -1;
        let results = [];

        function getIconSvg(type) {
            const icons = {
                user: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>',
                'user-graduate': '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 11c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>',
                book: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M18 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zM9 4h6v12H9V4z"/></svg>',
                building: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.48-.84-2.61-2.53-2.61-4.44 0-2.97 2.64-5.44 5.92-5.44s5.92 2.47 5.92 5.44c0 1.91-1.13 3.6-2.61 4.44.36 1.02.38 2.143 0 3.44-.76 1.88-2.38 3.81-3.44 5.46-1.41 2.26-3.42 3.76-3.42 3.76S7.48 15.46 6.07 13.21c-1.07-1.65-2.69-3.58-3.44-5.46-.357-1.297-.357-2.42 0-3.44zM12 6c-1.6 0-2.904 1.306-2.904 2.926 0 1.62 1.304 2.926 2.904 2.926s2.904-1.306 2.904-2.926C14.904 7.306 13.6 6 12 6z"/></svg>',
                calendar: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>',
                pencil: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25z"/></svg>',
                assignment: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H6v-2h10v2zm0-4H6v-2h10v2zm0-4H6V8h10v2z"/></svg>',
                'file-text': '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H6v-2h10v2zm0-4H6v-2h10v2zm0-4H6V8h10v2z"/></svg>',
                default: '<svg class="search-result-icon" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>'
            };
            return icons[type] || icons.default;
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text || '').replace(/[&<>"']/g, (m) => map[m]);
        }

        function hideResults() {
            resultsContainer.style.display = 'none';
            selectedIndex = -1;
            noResults.style.display = 'none';
            loading.style.display = 'none';
            error.style.display = 'none';
        }

        function updateSelected(items) {
            items.forEach((item, index) => {
                item.classList.toggle('selected', index === selectedIndex);
            });

            if (selectedIndex >= 0 && items[selectedIndex]) {
                items[selectedIndex].focus();
            }
        }

        clearBtn.addEventListener('click', function(event) {
            event.preventDefault();
            searchInput.value = '';
            clearBtn.style.display = 'none';
            resultsList.innerHTML = '';
            hideResults();
            searchInput.focus();
        });

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            clearBtn.style.display = query ? 'block' : 'none';
            clearTimeout(searchTimeout);

            if (query.length < 2) {
                resultsList.innerHTML = '';
                hideResults();
                return;
            }

            loading.style.display = 'block';
            noResults.style.display = 'none';
            error.style.display = 'none';
            resultsContainer.style.display = 'block';
            selectedIndex = -1;

            searchTimeout = setTimeout(() => {
                fetch(`${searchForm.action}?q=${encodeURIComponent(query)}`)
                    .then((response) => response.json())
                    .then((data) => {
                        loading.style.display = 'none';
                        results = data.results || [];

                        if (!results.length) {
                            resultsList.innerHTML = '';
                            noResults.style.display = 'block';
                            return;
                        }

                        noResults.style.display = 'none';
                        resultsList.innerHTML = results.map((result, index) => `
                            <div class="search-result-item" role="option" tabindex="0" data-index="${index}">
                                ${getIconSvg(result.type)}
                                <div class="search-result-content">
                                    <div class="search-result-title">${escapeHtml(result.title)}</div>
                                    <div class="search-result-subtitle">${escapeHtml(result.subtitle)}</div>
                                </div>
                            </div>
                        `).join('');

                        const items = resultsList.querySelectorAll('.search-result-item');
                        items.forEach((item, index) => {
                            item.addEventListener('click', () => {
                                const selected = results[index];
                                if (selected && selected.url) {
                                    window.location.href = selected.url;
                                }
                            });
                            item.addEventListener('keydown', (event) => {
                                if (event.key === 'Enter' || event.key === ' ') {
                                    event.preventDefault();
                                    item.click();
                                }
                            });
                        });
                    })
                    .catch(() => {
                        loading.style.display = 'none';
                        error.style.display = 'block';
                    });
            }, 300);
        });

        searchInput.addEventListener('keydown', function(event) {
            const items = resultsList.querySelectorAll('.search-result-item');
            if (!items.length) return;

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelected(items);
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, 0);
                updateSelected(items);
            } else if (event.key === 'Enter' && selectedIndex >= 0) {
                event.preventDefault();
                items[selectedIndex].click();
            } else if (event.key === 'Escape') {
                event.preventDefault();
                hideResults();
            }
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.dashboard-search-wrapper')) {
                hideResults();
            }
        });
    });
</script>

