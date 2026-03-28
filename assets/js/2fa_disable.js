document.addEventListener('DOMContentLoaded', function() {
    initializePasswordToggle();
    initializeFormValidation();
    initializeOTPSystem();
    initializeVerificationCode();
});

function initializePasswordToggle() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
}

function initializeFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
}

function initializeVerificationCode() {
    const verificationCode = document.getElementById('verification_code');
    
    if (verificationCode) {
        verificationCode.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
        });
    }
}

function initializeOTPSystem() {
    const verifyEmailBtn = document.getElementById('verifyEmailBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const otpSection = document.getElementById('otpVerificationSection');
    const emailInput = document.getElementById('email');

    if (verifyEmailBtn) {
        verifyEmailBtn.addEventListener('click', sendOtp);
    }

    if (verifyOtpBtn) {
        verifyOtpBtn.addEventListener('click', verifyOtp);
    }

    function sendOtp() {
        const email = emailInput.value.trim();
        const resultContainer = document.getElementById('emailVerificationResult');

        if (!email) {
            showAlert(resultContainer, 'Please enter email', 'danger');
            return;
        }

        if (!isValidEmail(email)) {
            showAlert(resultContainer, 'Please enter a valid email address', 'danger');
            return;
        }

        showAlert(resultContainer, 'Sending OTP...', 'info');
        verifyEmailBtn.disabled = true;
        verifyEmailBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';

        const formData = new FormData();
        formData.append('action', 'send_otp');
        formData.append('email', email);

        fetch('../functions/disable_2fa.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('OTP Response:', data);
            if (data.success) {
                showAlert(resultContainer, data.message, 'success');
                if (otpSection) {
                    otpSection.style.display = 'block';
                    const otpInput = document.getElementById('otp');
                    if (otpInput) {
                        otpInput.focus();
                    }
                }
            } else {
                showAlert(resultContainer, data.message || 'Failed to send OTP', 'danger');
            }
        })
        .catch(error => {
            console.error('OTP Error:', error);
            showAlert(resultContainer, 'Failed to send OTP. Please try again.', 'danger');
        })
        .finally(() => {
            verifyEmailBtn.disabled = false;
            verifyEmailBtn.innerHTML = '<i class="fas fa-envelope me-1"></i> Send OTP';
        });
    }

    function verifyOtp() {
        const otp = document.getElementById('otp').value.trim();
        const email = emailInput.value.trim();
        const resultContainer = document.getElementById('otpVerificationResult');

        if (!otp || otp.length !== 6) {
            showAlert(resultContainer, 'Please enter a valid 6-digit OTP', 'danger');
            return;
        }

        if (!isValidEmail(email)) {
            showAlert(resultContainer, 'Invalid email address', 'danger');
            return;
        }

        showAlert(resultContainer, 'Verifying OTP...', 'info');
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Verifying...';

        const formData = new FormData();
        formData.append('action', 'verify_otp');
        formData.append('otp', otp);
        formData.append('email', email);

        fetch('../functions/disable_2fa.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Verify Response:', data);
            if (data.success) {
                showAlert(resultContainer, data.message, 'success');
                verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Verified';
                verifyOtpBtn.disabled = true;
                
                const otpInput = document.getElementById('otp');
                if (otpInput) {
                    otpInput.readOnly = true;
                }
                
                const verificationCode = document.getElementById('verification_code');
                if (verificationCode) {
                    verificationCode.focus();
                }
            } else {
                showAlert(resultContainer, data.message || 'Invalid OTP', 'danger');
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Verify OTP';
            }
        })
        .catch(error => {
            console.error('Verify Error:', error);
            showAlert(resultContainer, 'OTP verification failed. Please try again.', 'danger');
            verifyOtpBtn.disabled = false;
            verifyOtpBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Verify OTP';
        });
    }
}

function showAlert(container, message, type) {
    if (!container) return;
    
    const alertClass = type === 'error' ? 'danger' : type;
    container.innerHTML = `
        <div class="alert alert-${alertClass} alert-dismissible fade show">
            <i class="fas fa-${getIconClass(alertClass)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
}

function getIconClass(type) {
    switch(type) {
        case 'success': return 'check-circle';
        case 'danger': return 'exclamation-circle';
        case 'warning': return 'exclamation-triangle';
        case 'info': return 'info-circle';
        default: return 'info-circle';
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}