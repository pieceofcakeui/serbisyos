document.addEventListener('DOMContentLoaded', function () {
    const searchBox = document.querySelector('.search-inbox');
    const searchInput = document.getElementById('conversation-search');

    if (!searchBox || !searchInput) {
        console.error('Required elements not found. Make sure the search box exists.');
        return;
    }

    const dropdown = document.createElement('div');
    dropdown.className = 'search-dropdown';
    dropdown.id = 'shop-search-dropdown';
    searchBox.appendChild(dropdown);

    const style = document.createElement('style');
    style.textContent = `.search-dropdown {
            position: absolute;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            z-index: 1000;
            display: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-right: -50px;
        }
        .search-result-item {
            padding: 10px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .search-result-item:hover {
            background-color: #f5f5f5;
        }
        .search-result-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        .no-results {
            padding: 10px;
            text-align: center;
            color: #666;
        }`;
    document.head.appendChild(style);

    let searchTimeout = null;

    function searchShops(query) {
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }

        if (!query || query.length < 1) {
            dropdown.innerHTML = '';
            dropdown.style.display = 'none';
            return;
        }

        dropdown.innerHTML = '<div class="no-results">Searching...</div>';
        dropdown.style.display = 'block';

        searchTimeout = setTimeout(function () {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `../account/backend/search_shops.php?query=${encodeURIComponent(query)}`, true);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        console.log('Search response:', data);

                        if (data.error) {
                            dropdown.innerHTML = `<div class="no-results">${data.error}</div>`;
                            dropdown.style.display = 'block';
                            return;
                        }

                        dropdown.innerHTML = '';

                        if (data.length > 0) {
                            data.forEach(function (shop) {
                                const item = document.createElement('div');
                                item.className = 'search-result-item';
                                item.setAttribute('data-user-id', shop.id);

                                const imgSrc = shop.logo;
                                const imgOnError = "this.onerror=null;this.src='../account/uploads/shop_logo/logo.jpg';";

                                item.innerHTML = `<img src="${imgSrc}" alt="${shop.name}" 
                                         class="search-result-avatar" onerror="${imgOnError}">
                                    <div class="search-result-name">${shop.name}</div>`;

                                item.addEventListener('click', function () {
                                    const userId = this.getAttribute('data-user-id');
                                    window.location.href = `?user_id=${userId}`;
                                });

                                dropdown.appendChild(item);
                            });

                            dropdown.style.display = 'block';
                        } else {
                            const noResults = document.createElement('div');
                            noResults.className = 'no-results';
                            noResults.textContent = 'No shops found';
                            dropdown.appendChild(noResults);
                            dropdown.style.display = 'block';
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e, xhr.responseText);
                        dropdown.innerHTML = '<div class="no-results">Error processing search results</div>';
                        dropdown.style.display = 'block';
                    }
                } else {
                    dropdown.innerHTML = '<div class="no-results">Error searching for shops</div>';
                    dropdown.style.display = 'block';
                }
            };

            xhr.onerror = function () {
                console.error('Network error occurred');
                dropdown.innerHTML = '<div class="no-results">Network error</div>';
                dropdown.style.display = 'block';
            };

            xhr.send();
        }, 150);
    }

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();
        searchShops(query);
    });

    searchInput.addEventListener('focus', function () {
        const query = this.value.trim();
        if (query.length >= 1) {
            searchShops(query);
        }
    });

    document.addEventListener('click', function (event) {
        if (!searchBox.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    console.log('Shop search functionality initialized');
});