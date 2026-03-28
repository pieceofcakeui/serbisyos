let currentShopIdentifier = null;
let currentShopName = null;
let currentAction = null;
let currentShopPhone = null;

function handleActionClick(action, shopIdentifier, shopName, shopPhone = null) {
    const loggedInAttr = document.querySelector('[data-logged-in]');
    const isLoggedIn = loggedInAttr ? loggedInAttr.getAttribute('data-logged-in') === 'true' : false;

    currentAction = action;
    currentShopIdentifier = shopIdentifier;
    currentShopName = shopName;
    currentShopPhone = shopPhone;

    if (isLoggedIn) {
        performAction(currentAction, currentShopIdentifier, currentShopName);
    } else {
        showLoginModal();
    }
}


function performAction(action, id, name) {
    switch (action) {
        case 'message':
            window.location.href = `account/view_details?shop=${id}&shop=${name}`;
            break;
        case 'favorites':
            window.location.href = `account/view_details?shop=${id}&shop=${name}`;
            console.log(`User wants to save shop: ${decodeURIComponent(name)}`);
            break;
        case 'report':
            console.log(`User wants to report shop: ${decodeURIComponent(name)}`);
            break;
        case 'review':
            console.log(`User wants to write a review for: ${decodeURIComponent(name)}`);
            const reviewModal = new bootstrap.Modal(document.getElementById('writeReview'));
            reviewModal.show();
            break;
        case 'like':
            console.log(`User liked review with slug: ${id}`);
            break;
        case 'book':
            console.log(`User want to book with slug: ${id}`);
            window.location.href = `account/view_details?shop=${id}`;
            break;
        case 'emergency':
            console.log(`User want to request an emergency with slug: ${id}`);
            window.location.href = `account/view_details?shop=${id}`;
            break;
        case 'emergency-provider':
            console.log(`User want to request an emergency with slug: ${id}`);
            window.location.href = `account/view_details?shop=${id}`;
            break
        case 'emergency-help':
            console.log(`User want to request an emergency with slug: ${id}`);
            window.location.href = `account/view_details?shop=${id}`;
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
            if (currentAction === 'emergency' || currentAction === 'emergency-provider') {
                bodyElement.innerHTML = `
                    You need an account to ${actionText}.<br><br>
                    <strong>For immediate help, call the shop directly.</strong><br>
                    <hr>
                    <p>Or log in to continue:</p>
                `;
            } else {
                bodyElement.innerHTML = `Log in or sign up to ${actionText}.`;
            }
        }

        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';

        let redirectUrl;

        if (currentShopIdentifier) {
            redirectUrl = `account/view_details?shop=${currentShopIdentifier}`;
        } else {
            redirectUrl = window.location.href; 
        }

        document.getElementById('loginBtn').onclick = function () {
            if (typeof BASE_URL !== 'undefined') {
                window.location.href = `${BASE_URL}/login?redirect=${encodeURIComponent(redirectUrl)}`;
            } else {
                console.error('BASE_URL is not defined!');
                window.location.href = `login?redirect=${encodeURIComponent(redirectUrl)}`;
            }
        };

        document.getElementById('signupBtn').onclick = function () {
            if (typeof BASE_URL !== 'undefined') {
                window.location.href = `${BASE_URL}/signup?redirect=${encodeURIComponent(redirectUrl)}`;
            } else {
                console.error('BASE_URL is not defined!');
                window.location.href = `signup?redirect=${encodeURIComponent(redirectUrl)}`;
            }
        };
    }
}


function getActionText(action) {
    switch (action) {
        case 'message':
            return 'send a message to this shop';
        case 'favorites':
            return 'save this shop to your favorites';
        case 'report':
            return 'report this shop';
        case 'review':
            return 'write a review for this shop';
        case 'like':
            return 'like this review';
        case 'book':
            return 'book an appointment with this shop';
        case 'emergency':
            return 'request emergency assistance from this shop';
        case 'emergency-provider':
            return 'request emergency assistance from this shop';
        case 'emergency-help':
            return 'request emergency assistance from this shop';
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