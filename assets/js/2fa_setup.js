const qrServices = [
    'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=',
    'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data='
];

let currentServiceIndex = 0;

function showQRCode() {
    document.getElementById('qr-loading').style.display = 'none';
    document.getElementById('qr-image').style.display = 'block';
    document.getElementById('qr-success').style.display = 'block';
}

function handleQRError() {
    const qrImage = document.getElementById('qr-image');
    const qrContainer = document.getElementById('qr-container');
    currentServiceIndex++;
    if (currentServiceIndex < qrServices.length) {
        const otpauthUrl = getOTPAuthUrl();
        if (otpauthUrl) {
            qrImage.src = qrServices[currentServiceIndex] + encodeURIComponent(otpauthUrl);
            return;
        }
    }
    document.getElementById('qr-loading').style.display = 'none';
    qrContainer.innerHTML = `<div class="alert alert-danger text-start mb-0"><i class="fas fa-exclamation-circle me-2"></i><strong>QR code failed to load</strong><br><small>QR code services are currently unavailable. Please use the manual entry method with the secret key provided.</small><div class="mt-2"><button class="btn btn-sm btn-outline-danger" onclick="retryQRCode()"><i class="fas fa-redo me-1"></i> Retry</button></div></div>`;
}

function getOTPAuthUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    const secret = urlParams.get('secret');
    const otpauth = urlParams.get('otpauth');
    if (otpauth) return otpauth;
    if (!secret) return null;
    const appName = 'Serbisyos';
    const userEmail = '';
    const fullName = '';
    const issuer = encodeURIComponent(appName);
    const accountName = encodeURIComponent(fullName + ' (' + userEmail + ')');
    return `otpauth://totp/${issuer}:${accountName}?secret=${secret}&issuer=${issuer}`;
}

function retryQRCode() {
    const otpauthUrl = getOTPAuthUrl();
    if (!otpauthUrl) return;
    currentServiceIndex = 0;
    const qrContainer = document.getElementById('qr-container');
    qrContainer.innerHTML = `<div class="qr-loading" id="qr-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading QR Code...</span></div></div><img src="${qrServices[currentServiceIndex] + encodeURIComponent(otpauthUrl)}" alt="QR Code" class="img-fluid qr-code mb-3" id="qr-image" style="display: none;" onload="showQRCode()" onerror="handleQRError()"><p class="text-muted small mb-0" id="qr-success" style="display: none;"><i class="fas fa-qrcode me-1"></i> Scan this with your authenticator app</p>`;
}

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} show`;
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `<div class="toast-message">${message}</div>`;
    toastContainer.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('hide');
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

function startExpirationTimer() {
    const timerElement = document.createElement('div');
    timerElement.className = 'text-danger small mt-1';
    const formText = document.querySelector('.form-text');
    if (formText) formText.after(timerElement);
    let seconds = 30 - (Math.floor(Date.now() / 1000) % 30);
    function updateTimer() {
        timerElement.textContent = `Code expires in ${seconds} seconds`;
        seconds--;
        if (seconds < 0) {
            seconds = 29;
            timerElement.textContent = 'Code refreshed automatically';
            setTimeout(() => { timerElement.textContent = `Code expires in ${seconds} seconds`; }, 1000);
        }
    }
    updateTimer();
    setInterval(updateTimer, 1000);
}

function initialize2FAPage() {
    new ClipboardJS('.copy-btn').on('success', function (e) {
        showToast('Secret key copied to clipboard!', 'success');
        e.clearSelection();
    });
    const verificationInput = document.getElementById('verification_code');
    if (verificationInput) {
        verificationInput.focus();
        verificationInput.addEventListener('input', function (e) {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length === 6) {
                const form = document.querySelector('.needs-validation');
                if (form) form.submit();
            }
        });
    }
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    }
    const helpLink = document.getElementById('helpLink');
    if (helpLink) {
        helpLink.addEventListener('click', function (e) {
            e.preventDefault();
            const helpModal = new bootstrap.Modal(document.getElementById('helpModal'));
            helpModal.show();
        });
    }
    window.scrollTo(0, 0);
    startExpirationTimer();
}

document.addEventListener('DOMContentLoaded', function () {
    initialize2FAPage();
    const qrImage = document.getElementById('qr-image');
    if (qrImage && qrImage.complete) showQRCode();
});

window.addEventListener('load', function() { window.scrollTo(0, 0); });