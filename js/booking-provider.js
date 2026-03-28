let currentShopId = null;
let currentShopName = null;
let currentAction = null;

function handleActionClick(action, encryptedShopId, shopName = null) {
    const isLoggedIn = document.querySelector('[data-logged-in]').getAttribute('data-logged-in') === 'true';

    currentAction = action;
    currentShopId = encryptedShopId; 
    currentShopName = shopName;

    if (isLoggedIn) {
        performAction(currentAction, currentShopId, currentShopName);
    } else {
        showLoginModal();
    }
}


function performAction(action, id, name) {
    switch (action) {
        case 'book':
            console.log(`User want to book with ID: ${id}`);
            break;
        default:
            console.error('Unknown action:', action);
    }
}

function showLoginModal() {
    const modal = document.getElementById('loginRequiredModal');
    if (modal) {
        const titleElement = document.querySelector('.accountRequired-modal-title');
        const bodyElement = document.querySelector('.accountRequired-modal-body');
        const actionText = getActionText(currentAction);

        if (titleElement) {
            titleElement.textContent = 'Account Required';
        }

        if (bodyElement) {
            if (currentAction === 'book') {
                bodyElement.innerHTML = `Log in or sign up to ${actionText}.`;
            }
        }

        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

        const redirectUrl = `account/view_details.php?shop_id=${currentShopId}&shop=${currentShopName}`;

        document.getElementById('loginBtn').onclick = function () {
            window.location.href = `login.php?redirect=${encodeURIComponent(redirectUrl)}`;
        };

        document.getElementById('signupBtn').onclick = function () {
            window.location.href = `signup.php?redirect=${encodeURIComponent(redirectUrl)}`;
        };
    }
}


function getActionText(action) {
    switch (action) {
        case 'book':
            return 'book an appointment with this shop';
        default:
            return 'continue with your action';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const closeModalBtn = document.querySelector('.accountRequired-close-modal');
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    window.addEventListener('click', function (event) {
        const modal = document.getElementById('loginRequiredModal');
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        const modal = document.getElementById('loginRequiredModal');
        if (event.key === 'Escape' && modal && modal.style.display === 'block') {
            closeModal();
        }
    });
});

function closeModal() {
    const modal = document.getElementById('loginRequiredModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';

        const loginBtn = document.getElementById('loginBtn');
        const signupBtn = document.getElementById('signupBtn');
        if (loginBtn) loginBtn.onclick = null;
        if (signupBtn) signupBtn.onclick = null;
    }
}