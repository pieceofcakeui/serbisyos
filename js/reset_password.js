const newPasswordInput = document.getElementById('new_password');
const confirmPasswordInput = document.getElementById('confirm_password');
const strengthBar = document.getElementById('password-strength-bar');
const passwordMatchMessage = document.getElementById('password-match-message');

newPasswordInput.addEventListener('input', function () {
    const password = newPasswordInput.value;
    let strength = 0;

    if (password.length >= 8) strength += 1;
    if (/[a-z]/.test(password)) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/\d/.test(password)) strength += 1;
    if (/[\W_]/.test(password)) strength += 1;

    switch (strength) {
        case 0:
            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = 'red';
            break;
        case 1:
            strengthBar.style.width = '20%';
            strengthBar.style.backgroundColor = 'red';
            break;
        case 2:
            strengthBar.style.width = '40%';
            strengthBar.style.backgroundColor = 'orange';
            break;
        case 3:
            strengthBar.style.width = '60%';
            strengthBar.style.backgroundColor = 'yellow';
            break;
        case 4:
            strengthBar.style.width = '80%';
            strengthBar.style.backgroundColor = 'lightgreen';
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.style.backgroundColor = 'green';
            break;
    }
});


confirmPasswordInput.addEventListener('input', function () {
    if (confirmPasswordInput.value === newPasswordInput.value) {
        passwordMatchMessage.textContent = "✅ Passwords match!";
        passwordMatchMessage.style.color = "green";
    } else {
        passwordMatchMessage.textContent = "❌ Passwords do not match!";
        passwordMatchMessage.style.color = "red";
    }
});

newPasswordInput.addEventListener('input', function () {
    if (confirmPasswordInput.value !== "") {
        if (confirmPasswordInput.value === newPasswordInput.value) {
            passwordMatchMessage.textContent = "✅ Passwords match!";
            passwordMatchMessage.style.color = "green";
        } else {
            passwordMatchMessage.textContent = "❌ Passwords do not match!";
            passwordMatchMessage.style.color = "red";
        }
    }
});