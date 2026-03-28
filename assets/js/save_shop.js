document.addEventListener('DOMContentLoaded', function() {
        const saveContainers = document.querySelectorAll('.view-shop-save-shop-container');
        saveContainers.forEach(container => {
            const isSaved = container.getAttribute('data-saved') === 'true';
            const iconWrapper = container.querySelector('.view-shop-save-icon-wrapper');
            const icon = iconWrapper.querySelector('.bi.bi-bookmark');

            if (icon) {
                if (isSaved) {
                    iconWrapper.classList.add('saved');
                    icon.style.color = '#ffd700';
                } else {
                    iconWrapper.classList.remove('saved');
                    icon.style.color = '#666';
                }
            }
        });
    });

    function toggleSaveShop(shopId) {
        const saveContainer = document.querySelector(`[onclick="toggleSaveShop(${shopId})"]`);
        const isLoggedIn = saveContainer.getAttribute('data-logged-in') === 'true';

        if (!isLoggedIn) {
            showCustomToast('Please login to save shops', 'warning');
            return;
        }

        fetch(`${BASE_URL}/account/backend/save_shop.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `shop_id=${shopId}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const iconWrapper = saveContainer.querySelector('.view-shop-save-icon-wrapper');
                const icon = iconWrapper.querySelector('.bi.bi-bookmark');

                if (!icon) {
                    console.error("Save icon not found!");
                    return;
                }

                if (data.error) {
                    showCustomToast(data.error, 'error');
                    return;
                }

                if (data.save) {
                    iconWrapper.classList.add('saved');
                    icon.style.color = '#ffd700';
                    saveContainer.setAttribute('data-saved', 'true');
                    showCustomToast(data.message || "Shop saved successfully!", 'success');
                } else {
                    iconWrapper.classList.remove('saved');
                    icon.style.color = '#666';
                    saveContainer.setAttribute('data-saved', 'false');
                    showCustomToast(data.message || "Shop removed successfully!", 'info');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomToast("Failed to save shop. Please try again.", 'error');
            });
    }

    function showCustomToast(message, type) {
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = `custom-toast custom-toast-${type}`;
        toast.innerHTML = `
        <span>${message}</span>
        <button class="toast-close-button">&times;</button>
    `;

        const closeButton = toast.querySelector('.toast-close-button');
        closeButton.addEventListener('click', () => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        });

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '1';
        }, 10);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }

    const bookmarkStyles = document.createElement('style');
    bookmarkStyles.textContent = `
    .view-shop-save-icon-wrapper .bi.bi-bookmark { /* FIXED SELECTOR */
        color: #666 !important;
        transition: color 0.3s ease;
    }
    
    .view-shop-save-icon-wrapper.saved .bi.bi-bookmark { /* FIXED SELECTOR */
        color: #ffd700 !important;
    }
`;
    document.head.appendChild(bookmarkStyles);

    const toastStyles = document.createElement('style');
    toastStyles.textContent = `
    .custom-toast {
        background-color: #388e3c;
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        padding: 12px 16px;
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        opacity: 0;
        text-align: center;
        min-width: 250px;
        max-width: 90%;
        margin: 0 auto;
        transition: opacity 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .custom-toast-success {
        background-color: #388e3c !important;
    }
    
    .custom-toast-error {
        background-color: #d32f2f !important;
    }
    
    .custom-toast-warning {
        background-color: #ffa000 !important;
    }
    
    .custom-toast-info {
        background-color: #1976d2 !important;
    }
    
    .toast-close-button {
        color: white !important;
        text-shadow: none;
        opacity: 1;
        font-weight: bold;
        font-size: 18px;
        margin-left: 15px;
        cursor: pointer;
        background: transparent;
        border: none;
        padding: 0 5px;
    }
`;
    document.head.appendChild(toastStyles);
