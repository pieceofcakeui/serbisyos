<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password</title>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="icon" type="image/png" href="assets/img/favicon.png">
    <link rel="apple-touch-icon" href="assets/img/favicon.png">
    <link rel="shortcut icon" href="assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="stylesheet" href="assets/css/resetpass.css">
    <style>
        body,
        body *:not(i):not(.fas):not(.fa):not([class^="icon-"]):not([class*=" icon-"]) {
            font-family: 'Montserrat', sans-serif !important;
        }

        .form-disabled {
            opacity: 0.5;
            pointer-events: none;
        }

    </style>
</head>

<body>
    <?php include 'offline-handler.php'; ?>

    <section class="reset-password">
        <div class="reset-password-container">
            <h2>Set New Password</h2>
            <form id="reset-form" method="POST" action="functions/reset_password.php">
                <div class="input-group" style="position: relative;">
                    <label for="new_password" style="display: block; margin-bottom: 5px; font-weight: bold;">New Password</label>
                    <input type="password" id="new_password" name="new_password" required 
                           pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" 
                           title="Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.">
                    <span class="toggle-password" id="toggle-new-password" style="position: absolute; right: 10px; top: 38px; cursor: pointer;">
                        <i class="fa-regular fa-eye"></i>
                    </span>
                    <div id="password-strength" class="mt-2" style="height: 5px; width: 100%; background-color: #e0e0e0; border-radius: 5px; margin-top: 8px;">
                        <div id="password-strength-bar" style="height: 100%; width: 0%; background-color: red; border-radius: 5px; transition: width 0.4s, background-color 0.4s;"></div>
                    </div>
                </div>

                <div class="input-group" style="position: relative; margin-top: 20px;">
                    <label for="confirm_password" style="display: block; margin-bottom: 5px; font-weight: bold;">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <span class="toggle-password" id="toggle-confirm-password" style="position: absolute; right: 10px; top: 38px; cursor: pointer;">
                        <i class="fa-regular fa-eye"></i>
                    </span>
                    <div id="password-match-message" style="margin-top: 5px; font-size: 14px;"></div>
                </div>

                <button type="submit" name="reset_password" class="btn">Reset Password</button>
            </form>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'include/toast.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupPasswordToggle(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggle = document.getElementById(toggleId);
            if (passwordInput && toggle) {
                toggle.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        }
        setupPasswordToggle('new_password', 'toggle-new-password');
        setupPasswordToggle('confirm_password', 'toggle-confirm-password');

        const newPasswordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const strengthBar = document.getElementById('password-strength-bar');
        const matchMessage = document.getElementById('password-match-message');

        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                let color = 'red';
                let width = '0%';

                if (/.{8,}/.test(password)) strength++;
                if (/[a-z]/.test(password)) strength++;
                if (/[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[\W_]/.test(password)) strength++;

                switch (strength) {
                    case 1: case 2: width = '20%'; color = '#d32f2f'; break;
                    case 3: width = '40%'; color = '#ffc107'; break;
                    case 4: width = '70%'; color = '#66bb6a'; break;
                    case 5: width = '100%'; color = '#388e3c'; break;
                }
                strengthBar.style.width = width;
                strengthBar.style.backgroundColor = color;
                checkPasswordsMatch();
            });
        }
        
        if(confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', checkPasswordsMatch);
        }

        function checkPasswordsMatch() {
             if (confirmPasswordInput.value) {
                if (newPasswordInput.value === confirmPasswordInput.value) {
                    matchMessage.textContent = 'Passwords match!';
                    matchMessage.style.color = '#388e3c';
                } else {
                    matchMessage.textContent = 'Passwords do not match.';
                    matchMessage.style.color = '#d32f2f';
                }
             } else {
                matchMessage.textContent = '';
             }
        }

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success') && urlParams.get('success') === '1') {
            const form = document.getElementById('reset-form');
            if(form) {
                form.classList.add('form-disabled');
            }

            setTimeout(function() {
                window.location.href = 'login.php';
            }, 3000); 
        }
    });
    </script>
</body>

</html>