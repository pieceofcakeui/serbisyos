document.addEventListener('DOMContentLoaded', function () {
    const yearSelect = document.getElementById('vehicleYear');
    if (yearSelect) {
        const currentYear = new Date().getFullYear();
        while (yearSelect.options.length > 1) {
            yearSelect.remove(1);
        }
        for (let year = currentYear; year >= 1990; year--) {
            const option = document.createElement('option');
            option.value = year;
            option.textContent = year;
            yearSelect.appendChild(option);
        }
    }

    let currentStep = 1;
    const totalSteps = 4;
    const prevBtn = document.getElementById('prevStepBtn');
    const nextBtn = document.getElementById('nextStepBtn');
    const submitBtn = document.getElementById('submitBooking');
    const bookingForm = document.getElementById('bookingForm');
    const successAlert = document.getElementById('successAlert');
    const errorAlert = document.getElementById('errorAlert');
    const formIntro = document.getElementById('form-intro');
    const progressBar = document.getElementById('progress-bar-inner');
    const stepCounter = document.getElementById('step-counter');

    let unsavedBookingModal;
    let pendingNavigation = null;
    let isBookingFormDirty = false;

    const beforeUnloadListener = (e) => {
        if (isBookingFormDirty) {
            e.preventDefault(); 
            e.returnValue = 'You have an incomplete booking. Are you sure you want to leave?';
            return 'You have an incomplete booking. Are you sure you want to leave?';
        }
    };

    const unsavedModalEl = document.getElementById('unsavedBookingModal');
    if (unsavedModalEl) {
        unsavedBookingModal = new bootstrap.Modal(unsavedModalEl);
        
        const confirmLeaveBtn = document.getElementById('confirmBookingLeaveBtn');
        confirmLeaveBtn.addEventListener('click', () => {
            if (typeof pendingNavigation === 'function') {
                window.removeEventListener('beforeunload', beforeUnloadListener);
                pendingNavigation();
            }
            unsavedBookingModal.hide();
            pendingNavigation = null;
        });
    }

    window.addEventListener('beforeunload', beforeUnloadListener);

    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            
            if (link.getAttribute('href') === 'javascript:history.back()') {
                if (isBookingFormDirty) {
                    e.preventDefault();
                    pendingNavigation = () => {
                        window.removeEventListener('beforeunload', beforeUnloadListener);
                        history.back();
                    };
                    if (unsavedBookingModal) unsavedBookingModal.show();
                }
                return;
            }

            if (!href || href.startsWith('#') || link.hasAttribute('data-bs-toggle') || link.closest('.alert')) {
                return;
            }

            if (isBookingFormDirty) {
                e.preventDefault();
                pendingNavigation = () => {
                    window.removeEventListener('beforeunload', beforeUnloadListener);
                    window.location.href = href;
                };
                if (unsavedBookingModal) unsavedBookingModal.show();
            }
        });
    });

    function updateStepNavigation() {
        if (formIntro) {
            formIntro.classList.toggle('d-none', currentStep > 1);
        }

        if (progressBar && stepCounter) {
            stepCounter.textContent = `Step ${currentStep} of ${totalSteps}`;
            const progressPercentage = (currentStep / totalSteps) * 100;
            progressBar.style.width = progressPercentage + '%';
        }

        if (prevBtn) prevBtn.disabled = currentStep === 1;
        if (nextBtn) nextBtn.classList.toggle('d-none', currentStep === totalSteps);
        if (submitBtn) submitBtn.classList.toggle('d-none', currentStep !== totalSteps);

        document.querySelectorAll('.booking-step').forEach(step => {
            if (step.id === `step${currentStep}`) {
                step.classList.remove('d-none');
            } else {
                step.classList.add('d-none');
            }
        });

        if (currentStep === 4) {
            updateConfirmationDetails();
        }
    }

    function updateConfirmationDetails() {
        document.getElementById('confirm-name').textContent = `Name: ${document.getElementById('customerName').value}`;
        document.getElementById('confirm-phone').textContent = `Phone: ${document.getElementById('customerPhone').value}`;
        document.getElementById('confirm-email').textContent = `Email: ${document.getElementById('customerEmail').value}`;
        document.getElementById('confirm-vehicle').textContent = `Vehicle: ${document.getElementById('vehicleMake').value} ${document.getElementById('vehicleModel').value}`;
        document.getElementById('confirm-year').textContent = `Year: ${document.getElementById('vehicleYear').value}`;
        document.getElementById('confirm-plate-number').textContent = `Plate No: ${document.getElementById('plateNumber').value}`;
        document.getElementById('confirm-transmission').textContent = `Transmission: ${document.getElementById('transmissionType').value}`;
        document.getElementById('confirm-fuel').textContent = `Fuel Type: ${document.getElementById('fuelType').value}`;
        document.getElementById('confirm-vehicle-type').textContent = `Type: ${document.getElementById('vehicleType').value}`;
        const servicesList = document.getElementById('confirm-services');
        servicesList.innerHTML = '';
        const selectedServices = document.querySelectorAll('input[name="services[]"]:checked');
        if (selectedServices.length === 0) {
            servicesList.innerHTML = '<li>No services selected</li>';
        } else {
            selectedServices.forEach(service => {
                const li = document.createElement('li');
                li.textContent = service.value;
                servicesList.appendChild(li);
            });
        }
        document.getElementById('confirm-date-time').textContent = `Date & Time: ${document.getElementById('preferredDateTime').value}`;
        const additionalNotes = document.getElementById('additionalNotes').value;
        document.getElementById('confirm-notes').textContent = additionalNotes ? `Notes: ${additionalNotes}` : '';
    }

    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) {
            console.warn(`showFieldError: Field with ID "${fieldId}" not found.`);
            if (fieldId.startsWith('service')) {
                alert(message);
            }
            return;
        }
        const parent = field.closest('.form-check') || field.parentElement;
        const existingErrorMsg = parent.querySelector('.invalid-feedback');
        field.classList.add('is-invalid');
        if (!existingErrorMsg) {
            const errorMsg = document.createElement('div');
            errorMsg.className = 'invalid-feedback';
            errorMsg.textContent = message;
            parent.appendChild(errorMsg);
        } else {
            existingErrorMsg.textContent = message;
        }
        field.focus();
    }

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(field => {
            field.classList.remove('is-invalid');
            const parent = field.closest('.form-check') || field.parentElement;
            const errorMsg = parent.querySelector('.invalid-feedback');
            if (errorMsg) errorMsg.remove();
        });
    }

    function validateStep(step) {
        clearValidationErrors();
        let isValid = true;
        if (step === 1) {
            if (!document.getElementById('customerName').value.trim()) { showFieldError('customerName', 'Please enter your name'); isValid = false; }
            if (!document.getElementById('customerPhone').value.trim()) { showFieldError('customerPhone', 'Please enter your phone number'); isValid = false; } else if (!/^\d{11}$/.test(document.getElementById('customerPhone').value.trim())) { showFieldError('customerPhone', 'Phone number must be exactly 11 digits'); isValid = false; }
            if (!document.getElementById('customerEmail').value.trim()) { showFieldError('customerEmail', 'Please enter your email address'); isValid = false; } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(document.getElementById('customerEmail').value.trim())) { showFieldError('customerEmail', 'Please enter a valid email address'); isValid = false; }
        } else if (step === 2) {
            if (!document.getElementById('vehicleMake').value.trim()) { showFieldError('vehicleMake', 'Please enter your vehicle make'); isValid = false; }
            if (!document.getElementById('vehicleModel').value.trim()) { showFieldError('vehicleModel', 'Please enter your vehicle model'); isValid = false; }
            if (!document.getElementById('vehicleYear').value) { showFieldError('vehicleYear', 'Please select your vehicle year'); isValid = false; }
            if (!document.getElementById('plateNumber').value.trim()) { showFieldError('plateNumber', 'Please enter your plate number'); isValid = false; }
            if (!document.getElementById('transmissionType').value) { showFieldError('transmissionType', 'Please select your transmission type'); isValid = false; }
            if (!document.getElementById('fuelType').value) { showFieldError('fuelType', 'Please select your fuel type'); isValid = false; }
            if (!document.getElementById('vehicleType').value) { showFieldError('vehicleType', 'Please select your vehicle type'); isValid = false; }
            const selectedServices = document.querySelectorAll('input[name="services[]"]:checked');
            if (selectedServices.length === 0) {
                alert('Please select at least one service');
                isValid = false;
            }
        } else if (step === 3) {
            if (!document.getElementById('preferredDateTime').value) { showFieldError('preferredDateTime', 'Please select a date and time'); isValid = false; }
        } else if (step === 4) {
            if (!document.getElementById('finalConfirm').checked) {
                alert('Please confirm you want to proceed with the booking');
                isValid = false;
            }
        }
        return isValid;
    }

    function displayErrorMessage(data) {
        let errorMessage = '';
        if (data.message) {
            switch (data.message) {
                case 'Shop ID is required': errorMessage = 'Session expired. Please refresh the page and try again.'; break;
                case 'Validation failed': errorMessage = 'Please check the following issues:'; break;
                case 'Shop booking settings not found': errorMessage = 'This shop is currently not accepting bookings. Please try again later or contact the shop directly.'; break;
                case 'Selected time slot is no longer available. Please refresh the page and select another slot.': errorMessage = 'Sorry, someone else just booked this time slot. Please select a different time.'; break;
                default: errorMessage = data.message;
            }
        } else { errorMessage = 'An unexpected error occurred while processing your booking.'; }
        if (data.errors && Array.isArray(data.errors) && data.errors.length > 0) {
            errorMessage += '\n\nDetails:\n• ' + data.errors.join('\n• ');
        }
        return errorMessage;
    }

    if (bookingForm) {
        bookingForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!validateStep(4)) { return; }

            const selectedDateTime = document.getElementById('preferredDateTime');
            const selectedOption = selectedDateTime.options[selectedDateTime.selectedIndex];
            if (!selectedDateTime.value || !selectedOption.getAttribute('data-slots')) { showFieldError('preferredDateTime', 'Please select a valid time slot'); return; }
            const currentSlots = parseInt(selectedOption.getAttribute('data-slots'));
            if (currentSlots <= 0) { showFieldError('preferredDateTime', 'This time slot is no longer available. Please select another slot.'); setTimeout(() => { location.reload(); }, 2000); return; }

            errorAlert.style.display = 'none';
            successAlert.style.display = 'none';
            
            const processingModalEl = document.getElementById('processingModal');
            const processingModal = new bootstrap.Modal(processingModalEl);
            const progressBar = document.getElementById('processingProgressBar');
            const percentageText = document.getElementById('processingPercentage');
            const statusText = document.getElementById('processingStatusText');
            const modalFooter = document.getElementById('processingModalFooter');

            progressBar.style.width = '0%';
            progressBar.classList.remove('bg-success', 'bg-danger');
            progressBar.classList.add('bg-warning');
            percentageText.textContent = '0%';
            statusText.textContent = 'Please wait, submitting your booking...';
            modalFooter.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...';
            
            processingModal.show();

            let progress = 0;
            const fakeProgressInterval = setInterval(() => {
                if (progress < 95) {
                    progress += Math.floor(Math.random() * 5) + 1;
                    progress = Math.min(progress, 95);
                    progressBar.style.width = `${progress}%`;
                    percentageText.textContent = `${progress}%`;
                }
            }, 250);

            const formData = new FormData(this);
            const selectedServices = [];
            document.querySelectorAll('input[name="services[]"]:checked').forEach(service => {
                selectedServices.push(service.value);
            });
            formData.append('selected_services', JSON.stringify(selectedServices));

            fetch('../account/backend/bookings.php', {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            })
            .then(response => {
                const contentType = response.headers.get('Content-Type');
                if (!contentType || !contentType.includes('application/json')) { throw new Error('Server returned invalid response format. Please try again.'); }
                return response.json().then(data => { data.statusCode = response.status; return data; });
            })
            .then(data => {
                clearInterval(fakeProgressInterval);
                progressBar.style.width = '100%';
                percentageText.textContent = '100%';

                if (data.success) {
                    isBookingFormDirty = false;
                    window.removeEventListener('beforeunload', beforeUnloadListener);
                    
                    progressBar.classList.remove('bg-warning');
                    progressBar.classList.add('bg-success');
                    statusText.textContent = 'Booking Confirmed! Please wait...';
                    
                    setTimeout(() => {
                        processingModal.hide();
                        document.getElementById('confirmationDate').textContent = data.details.date || 'N/A';
                        document.getElementById('confirmationVehicle').textContent = data.details.vehicle || 'N/A';
                        bookingForm.style.display = 'none';
                        const progressBarContainer = document.getElementById('modern-progress-bar-container');
                        if (progressBarContainer) progressBarContainer.style.display = 'none';
                        successAlert.style.display = 'block';
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        bookingForm.reset();
                        if (data.slots_remaining !== undefined) {
                            const remainingSlots = data.slots_remaining;
                            if (remainingSlots <= 0) {
                                selectedOption.remove();
                            } else {
                                const optionText = selectedOption.textContent;
                                const updatedText = optionText.replace(/\(\d+\s*slots\)/, `(${remainingSlots} slots)`);
                                selectedOption.textContent = updatedText;
                                selectedOption.setAttribute('data-slots', remainingSlots);
                            }
                        }
                    }, 1000);
                } else {
                    processingModal.hide();
                    const errorMessage = displayErrorMessage(data);
                    document.getElementById('errorMessage').textContent = errorMessage;
                    errorAlert.style.display = 'block';
                    errorAlert.scrollIntoView({ behavior: 'smooth' });
                    if (data.message && (data.message.includes('no longer available') || data.message.includes('time slot'))) {
                        setTimeout(() => { location.reload(); }, 3000);
                    }
                }
            })
            .catch(error => {
                clearInterval(fakeProgressInterval);
                processingModal.hide();
                console.error('Error:', error);
                let errorMessage = 'An unexpected error occurred. Please check your internet connection and try again.';
                if (error.message.includes('Failed to fetch')) { errorMessage = 'Network error. Please check your internet connection and try again.'; } 
                else if (error.message.includes('invalid response format')) { errorMessage = 'Server error. Please try again in a few moments or contact support.'; } 
                else if (error.message) { errorMessage = error.message; }
                document.getElementById('errorMessage').textContent = errorMessage;
                errorAlert.style.display = 'block';
                errorAlert.scrollIntoView({ behavior: 'smooth' });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Confirm';
            });
        });

        updateStepNavigation();

        nextBtn.addEventListener('click', function () {
            if (validateStep(currentStep)) {
                currentStep++;
                updateStepNavigation();
                document.querySelector('.booking-container').scrollIntoView({ behavior: 'smooth' });
            }
        });

        prevBtn.addEventListener('click', function () {
            currentStep--;
            updateStepNavigation();
            document.querySelector('.booking-container').scrollIntoView({ behavior: 'smooth' });
        });

        document.querySelectorAll('#bookingForm input, #bookingForm select, #bookingForm textarea').forEach(element => {
            element.addEventListener('input', function () {
                isBookingFormDirty = true;
                this.classList.remove('is-invalid');
                const parent = this.closest('.form-check') || this.parentElement;
                const errorMsg = parent.querySelector('.invalid-feedback');
                if (errorMsg) errorMsg.remove();
            });
        });
    }
});