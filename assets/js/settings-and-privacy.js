$(document).ready(function() {
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": false,
            "progressBar": false,
            "positionClass": "toast-top-center",
            "timeOut": 2000,
            "extendedTimeOut": 1500,
            "preventDuplicates": true
        };
    }

    function activateTabFromHash() {
        let currentHash = window.location.hash;
        const validTabs = ['#security', '#notifications', '#privacy'];
        let tabTarget = (currentHash && validTabs.includes(currentHash)) ? currentHash : '#security';
        const tabTrigger = document.querySelector(`a[data-bs-toggle="tab"][href="${tabTarget}"]`);
        if (tabTrigger) {
            bootstrap.Tab.getOrCreateInstance(tabTrigger).show();
        }
    }

    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        let hash = $(e.target).attr('href');
        if (history.pushState) {
            history.pushState(null, null, hash);
        } else {
            location.hash = hash;
        }
    });

    $('form').on('submit', function() {
        const activeTabHref = window.location.hash || '#security';
        if (!$(this).find('input[name="active_tab"]').length) {
            $(this).append(`<input type="hidden" name="active_tab" value="${activeTabHref}">`);
        }
    });

    function handleActionModals() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentHash = window.location.hash || '#security';
        const cleanUrl = window.location.pathname + currentHash;

        const showModalAndReload = (modalId) => {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                const myModal = new bootstrap.Modal(modalElement);
                myModal.show();
                setTimeout(() => {
                    window.location.href = cleanUrl;
                }, 2000);
            }
            window.history.replaceState({}, document.title, cleanUrl);
        };

        const showModal = (modalId) => {
            const modalElement = document.getElementById(modalId);
            if (modalElement) {
                new bootstrap.Modal(modalElement).show();
            }
            window.history.replaceState({}, document.title, cleanUrl);
        };

        if (urlParams.has('data_request_completed')) {
            showModalAndReload('dataRequestSuccessModal');
        } else if (urlParams.has('request_deleted')) {
            showModalAndReload('requestDeletedModal');
        } else if (urlParams.has('data_request_failed')) {
            showModal('dataRequestErrorModal');
        } else if (urlParams.has('data_ready')) {
            showModal('dataReadyModal');
        }
    }

    const deleteRequestModal = document.getElementById('deleteRequestModal');
    if (deleteRequestModal) {
        deleteRequestModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const deleteUrl = button.getAttribute('data-delete-url');
            const confirmButton = document.getElementById('confirmDeleteRequest');
            confirmButton.onclick = function() {
                const activeTab = window.location.hash || '#privacy';
                const finalUrl = deleteUrl + '&active_tab=' + encodeURIComponent(activeTab);
                window.location.href = finalUrl;
            };
        });
    }

    $('#deleteVerification').on('input', function() {
        const inputText = $(this).val().trim();
        const requiredText = 'DELETE MY ACCOUNT';
        const confirmDeleteBtn = $('#confirmDeleteBtn');
        const verificationError = $('#verificationError');
        if (inputText === requiredText) {
            confirmDeleteBtn.prop('disabled', false);
            verificationError.hide();
        } else {
            confirmDeleteBtn.prop('disabled', true);
            if (inputText.length >= requiredText.length) {
                verificationError.show();
            } else {
                verificationError.hide();
            }
        }
    });

    $('#deleteAccountForm').on('submit', function(e) {
        if ($('#deleteVerification').val().trim() !== 'DELETE MY ACCOUNT') {
            e.preventDefault();
            $('#verificationError').show();
        }
    });

    $(document).on('click', '.download-and-refresh-link', function() {
        setTimeout(function() {
            location.reload();
        }, 1500);
    });

    $('#newPassword').on('input', function() {
        const password = this.value;
        const strengthBar = $('#passwordStrengthBar');
        let strength = 0;
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[\W_]/.test(password)) strength++;
        const width = strength * 20;
        let color = 'red';
        if (strength >= 4) color = 'green';
        else if (strength >= 2) color = 'orange';
        strengthBar.css({
            'width': width + '%',
            'background-color': color
        });
    });

    $('#confirmPassword').on('input', function() {
        const newPassword = $('#newPassword').val();
        const confirmPassword = this.value;
        const message = $('#passwordMatchMessage');
        if (newPassword && confirmPassword) {
            if (newPassword === confirmPassword) {
                message.text('Passwords match!').css('color', 'green');
            } else {
                message.text('Passwords do not match!').css('color', 'red');
            }
        } else {
            message.text('');
        }
    });

    $('#modalToggleNewPassword, #modalToggleConfirmPassword').on('click', function() {
        const targetId = $(this).attr('id') === 'modalToggleNewPassword' ? 'modalNewPassword' : 'modalConfirmPassword';
        const passwordInput = $('#' + targetId);
        const icon = $(this).find('i');
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    $('#modalNewPassword').on('input', function() {
        const password = this.value;
        const strengthText = $('#modalPasswordStrength');
        strengthText.css('color', '').html('');
        if (password.length === 0) return;
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        const strengthMessages = ['Very Weak', 'Weak', 'Moderate', 'Strong', 'Very Strong'];
        const strengthColors = ['#dc3545', '#fd7e14', '#ffc107', '#28a745', '#20c997'];
        strengthText.text(`Strength: ${strengthMessages[strength]}`).css('color', strengthColors[strength]);
    });

    $('#modalConfirmPassword').on('input', function() {
        const confirmPassword = this.value;
        const newPassword = $('#modalNewPassword').val();
        const matchText = $('#modalPasswordMatch');
        if (confirmPassword.length === 0) {
            matchText.text('');
            return;
        }
        if (confirmPassword === newPassword) {
            matchText.text('Passwords match!').css('color', '#28a745');
        } else {
            matchText.text('Passwords do not match!').css('color', '#dc3545');
        }
    });

    $('#setPasswordForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('../account/backend/set_password-manual.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (typeof toastr !== 'undefined') {
                        toastr.success(data.message);
                    } else {
                        alert(data.message);
                    }
                    const modal = bootstrap.Modal.getInstance(document.getElementById('setPasswordModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    if (typeof toastr !== 'undefined') {
                        toastr.error(data.message);
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(error => {
                if (typeof toastr !== 'undefined') {
                    toastr.error('An error occurred. Please try again.');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
    });

    const bookingSettingsForm = $('#bookingSettingsForm');
    if (bookingSettingsForm.length) {
        const saveSettingsBtn = bookingSettingsForm.find('button[type="submit"]');
        const settingsStatus = $('#settingsStatus');
        const toggles = bookingSettingsForm.find('input[type="checkbox"]');
        let initialStates = {};
        toggles.each(function() {
            initialStates[this.id] = this.checked;
        });
        const checkFormChanges = () => {
            let hasChanges = false;
            toggles.each(function() {
                if (initialStates[this.id] !== this.checked) {
                    hasChanges = true;
                }
            });
            saveSettingsBtn.prop('disabled', !hasChanges);
        };
        toggles.on('change', checkFormChanges);
        bookingSettingsForm.on('submit', function(e) {
            e.preventDefault();
            saveSettingsBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
            settingsStatus.text('').removeClass('text-success text-danger');
            const formData = new FormData(this);
            formData.append('update_settings', 'true');
            fetch('settings-and-privacy.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        settingsStatus.text(data.message).addClass('text-success');
                        toggles.each(function() {
                            initialStates[this.id] = this.checked;
                        });
                    } else {
                        settingsStatus.text(data.message).addClass('text-danger');
                    }
                })
                .catch(error => {
                    settingsStatus.text('An error occurred while saving settings.').addClass('text-danger');
                })
                .finally(() => {
                    saveSettingsBtn.html('Save Preferences');
                    checkFormChanges();
                });
        });
        checkFormChanges();
    }

    activateTabFromHash();
    handleActionModals();
});