document.getElementById('totp-form').addEventListener('submit', function (e) {
    const digits = Array.from(document.querySelectorAll('.code-input'))
        .map(input => input.value)
        .join('');
    document.getElementById('verification_code').value = digits;
});
const inputs = document.querySelectorAll('.code-input');
inputs.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        if (e.target.value.length === 1) {
            if (index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        }
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
            inputs[index - 1].focus();
        }
    });
});
const backupInput = document.querySelector('.backup-input');
if (backupInput) {
    backupInput.addEventListener('input', function (e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 4) {
            value = value.substring(0, 4) + '-' + value.substring(4, 8);
        }
        this.value = value;
    });
}
document.getElementById('use-backup-link').addEventListener('click', function (e) {
    e.preventDefault();
    document.getElementById('main-form').style.display = 'none';
    document.getElementById('backup-form').style.display = 'block';
});
document.getElementById('use-totp-link').addEventListener('click', function (e) {
    e.preventDefault();
    document.getElementById('backup-form').style.display = 'none';
    document.getElementById('main-form').style.display = 'block';
});