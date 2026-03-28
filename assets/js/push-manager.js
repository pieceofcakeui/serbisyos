const VAPID_PUBLIC_KEY = 'BBwi2dtLvAVdCM7eLwpEZBuqHlQqBScZSLifVCx6qplkKimTQgy3wcQrYMt8qF2Ch0VFL5W0UkWS4kU2uClrAUY';
const enableButton = document.getElementById('enable-notifications-btn');
const buttonIcon = enableButton ? enableButton.querySelector('i') : null;

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

async function checkSubscriptionOnServer(subscription) {
    try {
        const response = await fetch('../account/backend/check-subscription-status.php', {
            method: 'POST',
            body: JSON.stringify({ endpoint: subscription.endpoint }),
            headers: { 'Content-Type': 'application/json' }
        });
        return await response.json();
    } catch (error) {
        console.error('Error checking subscription status:', error);
        return { status: 'error' };
    }
}

async function silentUnsubscribe(subscription) {
    try {
        await subscription.unsubscribe();
    } catch (e) {
        console.error('Error during silent unsubscribe:', e);
    }
}

async function updateButtonState() {
    if (!enableButton || !buttonIcon) return;

    const swReg = await navigator.serviceWorker.ready;
    const subscription = await swReg.pushManager.getSubscription();
    const existingHelp = document.getElementById('push-help-text');
    if (existingHelp) existingHelp.remove();

    let serverStatus = 'not_subscribed';
    if (subscription) {
        const serverState = await checkSubscriptionOnServer(subscription);
        serverStatus = serverState.status;
    }

    if (Notification.permission === 'denied') {
        enableButton.textContent = ' Notifications Blocked';
        buttonIcon.className = 'fas fa-ban me-2';
        enableButton.prepend(buttonIcon);
        enableButton.disabled = true;
        
        let helpText = document.createElement('small');
        helpText.id = 'push-help-text';
        helpText.className = 'text-muted d-block mt-2';
        helpText.textContent = 'You have blocked notifications. To enable them, go to your browser\'s site settings (click the padlock icon in the address bar).';
        enableButton.parentNode.appendChild(helpText);

    } else if (subscription && serverStatus === 'subscribed') {
        enableButton.textContent = ' Disable Push Notifications';
        buttonIcon.className = 'fas fa-bell-slash me-2';
        enableButton.prepend(buttonIcon);
        enableButton.classList.remove('btn-serbisyos', 'btn-primary');
        enableButton.classList.add('btn-secondary');
        enableButton.disabled = false;

    } else {
        if (subscription && serverStatus === 'not_subscribed') {
            await silentUnsubscribe(subscription);
        }
        enableButton.textContent = ' Enable Push Notifications';
        buttonIcon.className = 'fas fa-power-off me-2';
        enableButton.prepend(buttonIcon);
        enableButton.classList.remove('btn-secondary');
        enableButton.classList.add('btn-serbisyos');
        enableButton.disabled = false;
    }
}

async function handleSubscriptionClick() {
    enableButton.disabled = true;

    const swReg = await navigator.serviceWorker.ready;
    let subscription = await swReg.pushManager.getSubscription();

    const serverState = subscription ? await checkSubscriptionOnServer(subscription) : { status: 'not_subscribed' };

    if (subscription && serverState.status === 'subscribed') {
        await unsubscribeUser(subscription);
    } else {
        if (Notification.permission === 'default') {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                await subscribeUser();
            }
        } else if (Notification.permission === 'granted') {
            await subscribeUser();
        }
    }
    await updateButtonState();
}

async function subscribeUser() {
    try {
        const swReg = await navigator.serviceWorker.ready;
        const newSubscription = await swReg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });
        await saveSubscriptionToServer(newSubscription);
    } catch (err) {
        console.error('Error subscribing:', err);
        if (Notification.permission === 'denied') updateButtonState();
    }
}

async function unsubscribeUser(subscription) {
    try {
        await subscription.unsubscribe();
        await fetch('../account/backend/push-unsubscribe.php', {
            method: 'POST',
            body: JSON.stringify({ subscription: subscription }),
            headers: { 'Content-Type': 'application/json' }
        });
    } catch (err) {
        console.error('Error unsubscribing:', err);
    }
}

async function saveSubscriptionToServer(subscription) {
    try {
        await fetch('../account/backend/save-push-subscription.php', {
            method: 'POST',
            body: JSON.stringify(subscription),
            headers: { 'Content-Type': 'application/json' }
        });
    } catch (error) {
        console.error('Error saving push subscription:', error);
    }
}

if ('serviceWorker' in navigator && 'PushManager' in window) {
    navigator.serviceWorker.register('/sw.js').then(() => {
        updateButtonState();
        if (enableButton) {
            enableButton.addEventListener('click', handleSubscriptionClick);
        }
    }).catch(error => console.error('Service Worker registration failed:', error));
} else {
    if (enableButton) {
        enableButton.textContent = ' Push Notifications Not Supported';
        buttonIcon.className = 'fas fa-exclamation-triangle me-2';
        enableButton.prepend(buttonIcon);
        enableButton.disabled = true;
    }
}