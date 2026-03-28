document.addEventListener('DOMContentLoaded', function() {
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const nextToStep2 = document.getElementById('nextToStep2');
    const nextToStep3 = document.getElementById('nextToStep3');
    const backToStep1 = document.getElementById('backToStep1');
    const backToStep2 = document.getElementById('backToStep2');
    const progressBar = document.getElementById('formProgress');
    const progressText = document.getElementById('progressText');
    const vehicleType = document.getElementById('emergencyVehicleType');
    const vehicleModel = document.getElementById('emergencyVehicleModel');
    const issueDescription = document.getElementById('emergencyIssue');
    const locationInput = document.getElementById('emergencyLocation');
    const contactNumber = document.getElementById('emergencyContact');
    const emergencyFormContainer = document.getElementById('emergencyFormContainer');
    const successContainer = document.getElementById('successContainer');
    const formIntroHeader = document.getElementById('form-intro-header');
    const emergencyRequestForm = document.getElementById('emergencyRequestForm');

    let isEmergencyFormDirty = false;
    let isSubmissionSuccessful = false;
    let unsavedEmergencyModal;
    let pendingNavigation = null;

    const beforeUnloadListener = (e) => {
        if (isEmergencyFormDirty && !isSubmissionSuccessful) {
            e.preventDefault(); 
            e.returnValue = 'You have an incomplete emergency request. Are you sure you want to leave?';
            return 'You have an incomplete emergency request. Are you sure you want to leave?';
        }
    };

    window.addEventListener('beforeunload', beforeUnloadListener);

    const unsavedModalEl = document.getElementById('unsavedEmergencyModal');
    if (unsavedModalEl) {
        unsavedEmergencyModal = new bootstrap.Modal(unsavedModalEl);
        
        const confirmLeaveBtn = document.getElementById('confirmEmergencyLeaveBtn');
        confirmLeaveBtn.addEventListener('click', () => {
            if (typeof pendingNavigation === 'function') {
                window.removeEventListener('beforeunload', beforeUnloadListener);
                isEmergencyFormDirty = false;
                pendingNavigation();
            }
            unsavedEmergencyModal.hide();
            pendingNavigation = null;
        });
    }

    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            const isBackButton = href === 'javascript:history.back()';
            
            if (!isEmergencyFormDirty || isSubmissionSuccessful) return;
            if (!href && !isBackButton) return;
            if (href && (href.startsWith('#') || link.hasAttribute('data-bs-toggle'))) return;

            e.preventDefault();
            
            pendingNavigation = () => {
                window.removeEventListener('beforeunload', beforeUnloadListener);
                if (isBackButton) {
                    history.back();
                } else {
                    window.location.href = href;
                }
            };
            if (unsavedEmergencyModal) unsavedEmergencyModal.show();
        });
    });

    if (emergencyRequestForm) {
        emergencyRequestForm.querySelectorAll('input, select, textarea').forEach(element => {
            element.addEventListener('input', () => {
                isEmergencyFormDirty = true;
            });
            element.addEventListener('change', () => {
                isEmergencyFormDirty = true;
            });
        });
    }

    function checkStep1Complete() {
        return vehicleType.value && vehicleModel.value.trim() !== '';
    }

    function checkStep2Complete() {
        return issueDescription.value.trim() !== '';
    }

    function checkStep3Complete() {
        return locationInput.value.trim() !== '' &&
               contactNumber.value.trim() !== '' &&
               contactNumber.checkValidity();
    }

    function updateButtonStates() {
        if(nextToStep2) nextToStep2.disabled = !checkStep1Complete();
        if(nextToStep3) nextToStep3.disabled = !checkStep2Complete();
        const submitBtn = document.getElementById('emergencySubmitBtn');
        if(submitBtn) submitBtn.disabled = !checkStep3Complete();
    }

    if(vehicleType) vehicleType.addEventListener('change', updateButtonStates);
    if(vehicleModel) vehicleModel.addEventListener('input', updateButtonStates);
    if(issueDescription) issueDescription.addEventListener('input', updateButtonStates);
    if(locationInput) locationInput.addEventListener('input', updateButtonStates);
    if(contactNumber) contactNumber.addEventListener('input', updateButtonStates);

    function showStep(stepNumber) {
        if (formIntroHeader) {
            formIntroHeader.style.display = (stepNumber === 1) ? '' : 'none';
        }

        if(step1) step1.style.display = (stepNumber === 1) ? 'block' : 'none';
        if(step2) step2.style.display = (stepNumber === 2) ? 'block' : 'none';
        if(step3) step3.style.display = (stepNumber === 3) ? 'block' : 'none';

        if (stepNumber === 1) {
            if(progressBar) progressBar.style.width = '33%';
            if(progressText) progressText.textContent = 'Step 1 of 3';
        } else if (stepNumber === 2) {
            if(progressBar) progressBar.style.width = '66%';
            if(progressText) progressText.textContent = 'Step 2 of 3';
        } else if (stepNumber === 3) {
            if(progressBar) progressBar.style.width = '100%';
            if(progressText) progressText.textContent = 'Step 3 of 3';
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    if(nextToStep2) nextToStep2.addEventListener('click', function() {
        if (checkStep1Complete()) {
            showStep(2);
        }
    });

    if(nextToStep3) nextToStep3.addEventListener('click', function() {
        if (checkStep2Complete()) {
            showStep(3);
        }
    });

    if(backToStep1) backToStep1.addEventListener('click', function() {
        showStep(1);
    });

    if(backToStep2) backToStep2.addEventListener('click', function() {
        showStep(2);
    });

    updateButtonStates();

    let emergencyMap;
    let emergencyMarker;
    let emergencyInfoWindow;
    let isLocationConfirmed = false;

    const getLocationBtn = document.getElementById('emergencyGetLocationBtn');
    if (getLocationBtn) {
        getLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                const btn = this;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Detecting...';
                btn.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    async function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        const latLng = { lat: latitude, lng: longitude };

                        document.getElementById('emergencyLatitude').value = latitude;
                        document.getElementById('emergencyLongitude').value = longitude;

                        initEmergencyMap(latLng);

                        let accuracyMessage = document.getElementById('accuracyMessage');
                        if (!accuracyMessage) {
                            accuracyMessage = document.createElement('small');
                            accuracyMessage.id = 'accuracyMessage';
                            accuracyMessage.className = 'form-text text-muted mt-1';
                            document.getElementById('emergencyLocation').parentNode.appendChild(accuracyMessage);
                        }

                        let accuracyText = '';
                        if (accuracy) {
                            accuracyText = `Location detected (Accuracy: ±${Math.round(accuracy)} meters)`;
                            if (accuracy > 100) {
                                accuracyMessage.className = 'form-text text-warning mt-1';
                                accuracyText += ' - Position may be imprecise. Please verify on map.';
                            } else {
                                accuracyMessage.className = 'form-text text-success mt-1';
                            }
                        }

                        try {
                            const address = await getAccuratePhilippineAddress(latitude, longitude);
                            document.getElementById('emergencyLocation').value = address;
                            updateInfoWindowContent(address);
                            emergencyInfoWindow.open(emergencyMap, emergencyMarker);

                            if (accuracyText) {
                                accuracyMessage.textContent = accuracyText;
                            }
                        } catch (error) {
                            const errorMessage = 'Unable to determine exact address';
                            document.getElementById('emergencyLocation').value = errorMessage;
                            updateInfoWindowContent(errorMessage);
                            emergencyInfoWindow.open(emergencyMap, emergencyMarker);

                            if (accuracyText) {
                                accuracyMessage.textContent = `${errorMessage}. ${accuracyText}`;
                            } else {
                                accuracyMessage.textContent = errorMessage;
                            }
                            accuracyMessage.className = 'form-text text-warning mt-1';
                        }

                        showLocationVerification();
                        btn.innerHTML = '<i class="fas fa-location-arrow me-1"></i>';
                        btn.disabled = false;
                        isEmergencyFormDirty = true;
                    },
                    function(error) {
                        let errorMessage = 'Could not get your location. Please enter it manually.';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Location access was denied. Please enable location services in your browser settings.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Location information is unavailable. Please check your network connection.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'The request to get location timed out. Please try again.';
                                break;
                        }

                        let accuracyMessage = document.getElementById('accuracyMessage');
                        if (!accuracyMessage) {
                            accuracyMessage = document.createElement('small');
                            accuracyMessage.id = 'accuracyMessage';
                            accuracyMessage.className = 'form-text text-danger mt-1';
                            document.getElementById('emergencyLocation').parentNode.appendChild(accuracyMessage);
                        }
                        accuracyMessage.textContent = errorMessage;

                        btn.innerHTML = '<i class="fas fa-location-arrow me-1"></i>';
                        btn.disabled = false;
                    }, {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 0
                    }
                );
            } else {
                let accuracyMessage = document.getElementById('accuracyMessage');
                if (!accuracyMessage) {
                    accuracyMessage = document.createElement('small');
                    accuracyMessage.id = 'accuracyMessage';
                    accuracyMessage.className = 'form-text text-danger mt-1';
                    document.getElementById('emergencyLocation').parentNode.appendChild(accuracyMessage);
                }
                accuracyMessage.textContent = 'Geolocation is not supported by your browser. Please enter your location manually.';
            }
        });
    }

    function updateInfoWindowContent(content) {
        if(emergencyInfoWindow) emergencyInfoWindow.setContent(content);
    }

    function initEmergencyMap(latLng) {
        const mapContainer = document.getElementById('emergencyMapContainer');
        mapContainer.style.height = '400px';
        mapContainer.style.display = 'block';

        if (!emergencyMap) {
            emergencyMap = new google.maps.Map(mapContainer, {
                center: latLng,
                zoom: 18,
                mapTypeId: 'roadmap',
                zoomControl: true,
                mapTypeControl: false,
                streetViewControl: false,
                fullscreenControl: true,
                gestureHandling: 'greedy'
            });

            emergencyMarker = new google.maps.Marker({
                position: latLng,
                map: emergencyMap,
                draggable: true,
                animation: google.maps.Animation.DROP,
                title: 'Your current location',
                optimized: false
            });

            emergencyInfoWindow = new google.maps.InfoWindow({
                maxWidth: 300
            });

            emergencyMarker.addListener('click', () => {
                emergencyInfoWindow.open(emergencyMap, emergencyMarker);
            });

            google.maps.event.addListener(emergencyMarker, 'dragend', async function() {
                const position = emergencyMarker.getPosition();
                const lat = position.lat();
                const lng = position.lng();

                document.getElementById('emergencyLatitude').value = lat;
                document.getElementById('emergencyLongitude').value = lng;
                emergencyMap.setCenter(position);

                try {
                    const address = await getAccuratePhilippineAddress(lat, lng);
                    document.getElementById('emergencyLocation').value = address;
                    updateInfoWindowContent(address);
                } catch (error) {
                    const errorMessage = 'Unable to determine exact address';
                    document.getElementById('emergencyLocation').value = errorMessage;
                    updateInfoWindowContent(errorMessage);
                }
                emergencyInfoWindow.open(emergencyMap, emergencyMarker);

                document.getElementById('locationInstructions').style.display = 'block';
                document.getElementById('locationConfirmation').style.display = 'none';
                isLocationConfirmed = false;
                document.getElementById('emergencySubmitBtn').disabled = true;
            });

            google.maps.event.addListener(emergencyMap, 'click', async function(e) {
                emergencyMarker.setPosition(e.latLng);
                const lat = e.latLng.lat();
                const lng = e.latLng.lng();

                document.getElementById('emergencyLatitude').value = lat;
                document.getElementById('emergencyLongitude').value = lng;
                emergencyMap.setCenter(e.latLng);

                try {
                    const address = await getAccuratePhilippineAddress(lat, lng);
                    document.getElementById('emergencyLocation').value = address;
                    updateInfoWindowContent(address);
                } catch (error) {
                    const errorMessage = 'Unable to determine exact address';
                    document.getElementById('emergencyLocation').value = errorMessage;
                    updateInfoWindowContent(errorMessage);
                }
                emergencyInfoWindow.open(emergencyMap, emergencyMarker);

                document.getElementById('locationInstructions').style.display = 'block';
                document.getElementById('locationConfirmation').style.display = 'none';
                isLocationConfirmed = false;
                document.getElementById('emergencySubmitBtn').disabled = true;
            });

            let accuracyCircle;
            navigator.geolocation.getCurrentPosition(function(position) {
                if (position.coords.accuracy) {
                    if (accuracyCircle) accuracyCircle.setMap(null);

                    accuracyCircle = new google.maps.Circle({
                        strokeColor: '#4285F4',
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: '#4285F4',
                        fillOpacity: 0.2,
                        map: emergencyMap,
                        center: latLng,
                        radius: position.coords.accuracy
                    });
                }
            });
        } else {
            emergencyMap.setCenter(latLng);
            emergencyMarker.setPosition(latLng);
            emergencyMap.setZoom(18);
        }
    }

    function showLocationVerification() {
        document.getElementById('locationVerificationSection').style.display = 'block';
        document.getElementById('locationInstructions').style.display = 'block';
        document.getElementById('locationConfirmation').style.display = 'none';
        isLocationConfirmed = false;
        document.getElementById('emergencySubmitBtn').disabled = true;
    }

    const confirmLocationBtn = document.getElementById('confirmLocationBtn');
    if(confirmLocationBtn) {
        confirmLocationBtn.addEventListener('click', function() {
            isLocationConfirmed = true;
            document.getElementById('locationInstructions').style.display = 'none';
            document.getElementById('locationConfirmation').style.display = 'block';
            document.getElementById('emergencySubmitBtn').disabled = false;
            isEmergencyFormDirty = true;
        });
    }

    const updateLocationBtn = document.getElementById('updateLocationBtn');
    if(updateLocationBtn) {
        updateLocationBtn.addEventListener('click', function() {
            document.getElementById('locationInstructions').style.display = 'block';
            document.getElementById('locationConfirmation').style.display = 'none';
            isLocationConfirmed = false;
            document.getElementById('emergencySubmitBtn').disabled = true;
        });
    }

    const videoUploadArea = document.getElementById('videoUploadArea');
    const fileInput = document.getElementById('emergencyVideo');
    const videoPreviewContainer = document.getElementById('videoPreviewContainer');
    let uploadInProgress = false;
    let allUploadsComplete = true;
    let isSubmitting = false;
    let uploadedFileName = null;

    if(videoUploadArea) {
        videoUploadArea.addEventListener('click', function() {
            fileInput.value = '';
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const file = e.target.files[0];
                if (file.size > 100 * 1024 * 1024) {
                    showToast('Video file is too large (max 100MB)', 'danger');
                    return;
                }
                if (file.type !== 'video/mp4') {
                    showToast('Only MP4 videos are allowed', 'danger');
                    return;
                }
                handleFile(file);
            }
        });

        videoUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            videoUploadArea.classList.add('border-primary', 'bg-light');
        });

        videoUploadArea.addEventListener('dragleave', function() {
            videoUploadArea.classList.remove('border-primary', 'bg-light');
        });

        videoUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            videoUploadArea.classList.remove('border-primary', 'bg-light');
            const files = Array.from(e.dataTransfer.files)
                .filter(file => file.type === 'video/mp4');

            if (files.length > 0) {
                const file = files[0];
                if (file.size > 100 * 1024 * 1024) {
                    showToast('Video file is too large (max 100MB)', 'danger');
                    return;
                }
                handleFile(file);
            }
        });
    }

    function handleFile(file) {
        videoPreviewContainer.innerHTML = '';
        uploadedFileName = file.name;

        const filePreviewContainer = document.createElement('div');
        filePreviewContainer.className = 'file-preview-container d-flex flex-column align-items-center me-3 mb-3';
        filePreviewContainer.style.width = '100%';

        const videoContainer = document.createElement('div');
        videoContainer.className = 'position-relative';
        videoContainer.style.width = '100%';
        videoContainer.style.maxWidth = '300px';

        const video = document.createElement('video');
        video.src = URL.createObjectURL(file);
        video.controls = true;
        video.style.width = '100%';
        video.style.height = 'auto';
        video.style.borderRadius = '4px';
        video.dataset.fileName = file.name;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle p-0';
        removeBtn.style.width = '20px';
        removeBtn.style.height = '20px';
        removeBtn.innerHTML = '<i class="fas fa-times" style="font-size: 10px;"></i>';
        removeBtn.dataset.fileName = file.name;
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            filePreviewContainer.remove();
            resetFileInput();
        });

        const progressContainer = document.createElement('div');
        progressContainer.className = 'progress-container w-100 mt-2';

        const progressBar = document.createElement('div');
        progressBar.className = 'progress';
        progressBar.style.height = '6px';

        const progressBarInner = document.createElement('div');
        progressBarInner.className = 'progress-bar progress-bar-striped progress-bar-animated';
        progressBarInner.style.width = '0%';
        progressBarInner.setAttribute('aria-valuenow', '0');
        progressBarInner.setAttribute('aria-valuemin', '0');
        progressBarInner.setAttribute('aria-valuemax', '100');
        progressBarInner.dataset.fileName = file.name;

        const progressText = document.createElement('div');
        progressText.className = 'progress-text text-center small mt-1';
        progressText.style.fontSize = '10px';
        progressText.textContent = 'Uploading... 0%';
        progressText.dataset.fileName = file.name;

        videoContainer.appendChild(video);
        videoContainer.appendChild(removeBtn);

        progressBar.appendChild(progressBarInner);
        progressContainer.appendChild(progressBar);
        progressContainer.appendChild(progressText);

        filePreviewContainer.appendChild(videoContainer);
        filePreviewContainer.appendChild(progressContainer);

        videoPreviewContainer.appendChild(filePreviewContainer);

        isEmergencyFormDirty = true;
        startUploadProgress(file.name);
    }

    function resetFileInput() {
        fileInput.value = '';
        uploadedFileName = null;
        allUploadsComplete = true;
        updateAllButtonStates();
    }

    function startUploadProgress(fileName) {
        uploadInProgress = true;
        allUploadsComplete = false;
        updateAllButtonStates();

        let progress = 0;
        const duration = Math.random() * 3000 + 2000;
        const step = 100 / (duration / 100);

        const interval = setInterval(() => {
            progress += step;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                uploadInProgress = false;
                allUploadsComplete = true;
                updateAllButtonStates();
            }
            updateFileProgress(fileName, Math.round(progress));
        }, 100);
    }

    function updateFileProgress(fileName, progress) {
        const progressBars = document.querySelectorAll(`.progress-bar[data-file-name="${fileName}"]`);
        const progressTexts = document.querySelectorAll(`.progress-text[data-file-name="${fileName}"]`);

        progressBars.forEach(bar => {
            bar.style.width = progress + '%';
            bar.setAttribute('aria-valuenow', progress);
            if (progress >= 100) {
                bar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                bar.classList.add('bg-success');
            }
        });

        progressTexts.forEach(text => {
            if (progress < 100) {
                text.textContent = `Uploading... ${progress}%`;
            } else {
                text.textContent = 'Upload complete';
                text.classList.add('text-success');
            }
        });
    }

    function updateAllButtonStates() {
        updateNextButtonState();
        updateSubmitButtonState();
    }

    function updateSubmitButtonState() {
        const emergencySubmitBtn = document.getElementById('emergencySubmitBtn');
        if (emergencySubmitBtn) {
            const allRequiredComplete = allUploadsComplete && checkStep3Complete() && isLocationConfirmed;
            emergencySubmitBtn.disabled = !allRequiredComplete;
            if (!allRequiredComplete && !allUploadsComplete) {
                emergencySubmitBtn.setAttribute('title', 'Please wait for video upload to complete');
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(emergencySubmitBtn, { trigger: 'hover' });
                }
            } else {
                emergencySubmitBtn.removeAttribute('title');
            }
        }
    }

    function updateNextButtonState() {
        const emergencyIssue = document.getElementById('emergencyIssue');
        const nextToStep3 = document.getElementById('nextToStep3');

        if (emergencyIssue && nextToStep3) {
            const issueDescribed = emergencyIssue.value.trim() !== '';
            const uploadStatus = !fileInput.files || fileInput.files.length === 0 || allUploadsComplete;
            nextToStep3.disabled = !(issueDescribed && uploadStatus);
            if (issueDescribed && !uploadStatus) {
                nextToStep3.setAttribute('title', 'Please wait for video upload to complete');
                if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                    new bootstrap.Tooltip(nextToStep3, { trigger: 'hover' });
                }
            } else {
                nextToStep3.removeAttribute('title');
            }
        }
    }

    if (emergencyRequestForm) {
        emergencyRequestForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            if (isSubmitting) return;
            isSubmitting = true;
            const form = this;
            const btn = document.getElementById('emergencySubmitBtn');
            form.classList.remove('was-validated');
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                isSubmitting = false;
                return;
            }

            const requiredFields = [
                'shop_id', 'shop_user_id', 'vehicle_type', 'vehicle_model',
                'issue_description', 'full_address', 'contact_number',
                'latitude', 'longitude'
            ];

            let missingFields = [];
            for (const field of requiredFields) {
                const element = form.querySelector(`[name="${field}"]`);
                if (!element || !element.value.trim()) {
                    missingFields.push(field);
                }
            }

            if (!isLocationConfirmed) {
                missingFields.push("Location Confirmation");
            }

            if (!allUploadsComplete) {
                missingFields.push("Video Upload Completion");
            }

            if (missingFields.length > 0) {
                showToast(`Missing required information: ${missingFields.join(', ')}. Please complete all fields and confirm location/video upload.`, 'danger');
                isSubmitting = false;
                return;
            }

            const processingModalEl = document.getElementById('processingModal');
            const processingModal = new bootstrap.Modal(processingModalEl);
            const progressBar = document.getElementById('processingProgressBar');
            const percentageText = document.getElementById('processingPercentage');
            const statusText = document.getElementById('processingStatusText');
            const modalFooter = document.getElementById('processingModalFooter');

            progressBar.style.width = '0%';
            progressBar.classList.remove('bg-success', 'bg-secondary');
            progressBar.classList.add('bg-danger');
            percentageText.textContent = '0%';
            statusText.textContent = 'Please wait, we are dispatching your request...';
            modalFooter.style.display = 'none';

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

            const formData = new FormData(form);
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...';
            btn.disabled = true;

            let requestSuccessful = false;

            try {
                const response = await fetch('../account/backend/process_emergency.php', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData
                });

                const responseText = await response.text();
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}, response: ${responseText}`);
                }

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    if (responseText.includes('success') || responseText.toLowerCase().includes('emergency request submitted')) {
                        data = { success: true, message: responseText };
                    } else {
                        throw new Error(responseText || 'Invalid response from server');
                    }
                }

                clearInterval(fakeProgressInterval);
                progressBar.style.width = '100%';
                percentageText.textContent = '100%';

                if (data.success) {
                    isEmergencyFormDirty = false;
                    isSubmissionSuccessful = true;
                    window.removeEventListener('beforeunload', beforeUnloadListener);
                    
                    requestSuccessful = true;
                    progressBar.classList.remove('bg-danger');
                    progressBar.classList.add('bg-success');
                    statusText.textContent = 'Request Sent Successfully!';

                    setTimeout(() => {
                        processingModal.hide();
                        emergencyFormContainer.style.display = 'none';
                        successContainer.style.display = 'block';
                        resetEmergencyForm();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Request failed');
                }
            } catch (error) {
                clearInterval(fakeProgressInterval);
                processingModal.hide();
                showToast(error.message || 'Failed to send request. Please try again.', 'danger');
            } finally {
                if (!requestSuccessful) {
                    btn.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send';
                    btn.disabled = false;
                }
                isSubmitting = false;
            }
        });
    }

    function resetEmergencyForm() {
        const form = document.getElementById('emergencyRequestForm');
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }

        const step1 = document.getElementById('step1');
        const step2 = document.getElementById('step2');
        const step3 = document.getElementById('step3');
        if (step1) step1.style.display = 'block';
        if (step2) step2.style.display = 'none';
        if (step3) step3.style.display = 'none';

        const progressBar = document.getElementById('formProgress');
        const progressText = document.getElementById('progressText');
        if (progressBar) progressBar.style.width = '33%';
        if (progressText) progressText.textContent = 'Step 1 of 3';

        const locationBtn = document.getElementById('emergencyGetLocationBtn');
        if (locationBtn) {
            locationBtn.innerHTML = '<i class="fas fa-location-arrow text-danger fs-5"></i>';
            locationBtn.disabled = false;
        }

        if (videoPreviewContainer) {
            videoPreviewContainer.innerHTML = '';
        }

        const mapContainer = document.getElementById('emergencyMapContainer');
        if (mapContainer) {
            mapContainer.style.display = 'none';
        }

        const locationVerificationSection = document.getElementById('locationVerificationSection');
        if (locationVerificationSection) {
            locationVerificationSection.style.display = 'none';
        }

        uploadInProgress = false;
        allUploadsComplete = true;
        isSubmitting = false;
        uploadedFileName = null;
        isLocationConfirmed = false;
        isEmergencyFormDirty = false;
        isSubmissionSuccessful = false;
        window.addEventListener('beforeunload', beforeUnloadListener);

        if (fileInput) {
            fileInput.value = '';
        }

        updateButtonStates();
        updateNextButtonState();
        updateSubmitButtonState();
    }

    function showToast(message, type) {
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style.position = 'fixed';
            toastContainer.style.top = '20px';
            toastContainer.style.right = '20px';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    async function getAccuratePhilippineAddress(latitude, longitude) {
        if (!latitude || !longitude ||
            latitude < -90 || latitude > 90 ||
            longitude < -180 || longitude > 180) {
            throw new Error('Invalid coordinates provided');
        }

        const philippinesBounds = {
            north: 21.120611,
            south: 4.586387,
            east: 126.604004,
            west: 116.931557
        };

        if (latitude < philippinesBounds.south || latitude > philippinesBounds.north ||
            longitude < philippinesBounds.west || longitude > philippinesBounds.east) {
            console.warn('Coordinates appear to be outside Philippines');
        }

        const apiKey = 'AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE';

        try {
            const googleResponse = await fetch(
                `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}&language=en&region=PH`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                }
            );

            if (!googleResponse.ok) {
                throw new Error(`Google API HTTP error: ${googleResponse.status}`);
            }

            const googleData = await googleResponse.json();

            if (googleData.error_message) {
                throw new Error(`Google API error: ${googleData.error_message}`);
            }

            if (googleData.status === 'OK' && googleData.results && googleData.results.length > 0) {
                const result = googleData.results[0];

                const preferredTypes = ['street_address', 'route', 'intersection', 'political'];
                const bestResult = googleData.results.find(r =>
                    r.types.some(type => preferredTypes.includes(type))
                ) || result;

                return bestResult.formatted_address;
            }

            throw new Error(`No results found. Status: ${googleData.status}`);

        } catch (error) {
            console.error('Primary geocoding failed:', error);

            try {
                return await fallbackGeocoding(latitude, longitude);
            } catch (fallbackError) {
                console.error('Fallback geocoding failed:', fallbackError);

                return `Location: ${latitude.toFixed(6)}, ${longitude.toFixed(6)} (Exact address unavailable)`;
            }
        }
    }

    async function fallbackGeocoding(latitude, longitude) {
        try {
            const nominatimResponse = await fetch(
                `https://nominatim.openstreetmap.org/reverse?format=json&lat=${latitude}&lon=${longitude}&zoom=18&addressdetails=1`, {
                    headers: {
                        'User-Agent': 'Emergency Vehicle Request System'
                    }
                }
            );

            if (nominatimResponse.ok) {
                const nominatimData = await nominatimResponse.json();
                if (nominatimData.display_name) {
                    return nominatimData.display_name;
                }
            }
        } catch (nominatimError) {
            console.error('Nominatim geocoding failed:', nominatimError);
        }

        return generateBasicLocationDescription(latitude, longitude);
    }

    function generateBasicLocationDescription(latitude, longitude) {
        const regions = [
            { name: 'Luzon', bounds: { north: 21.12, south: 12.0, east: 126.6, west: 116.9 } },
            { name: 'Visayas', bounds: { north: 12.0, south: 8.0, east: 126.6, west: 116.9 } },
            { name: 'Mindanao', bounds: { north: 9.0, south: 4.5, east: 126.6, west: 116.9 } }
        ];

        let region = 'Philippines';
        for (const r of regions) {
            if (latitude >= r.bounds.south && latitude <= r.bounds.north &&
                longitude >= r.bounds.west && longitude <= r.bounds.east) {
                region = r.name;
                break;
            }
        }

        return `${region} - ${latitude.toFixed(4)}, ${longitude.toFixed(4)}`;
    }

    function enhancedLocationDetection() {
        const getLocationBtn = document.getElementById('emergencyGetLocationBtn');
        if (getLocationBtn) {
            getLocationBtn.addEventListener('click', function() {
                if (!navigator.geolocation) {
                    showToast('Geolocation is not supported by your browser. Please enter your location manually.', 'warning');
                    return;
                }

                const btn = this;
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Detecting...';
                btn.disabled = true;

                const options = {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 60000
                };

                navigator.geolocation.getCurrentPosition(
                    async function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        const latLng = { lat: latitude, lng: longitude };

                        document.getElementById('emergencyLatitude').value = latitude;
                        document.getElementById('emergencyLongitude').value = longitude;

                        initEmergencyMap(latLng);

                        try {
                            const locationInput = document.getElementById('emergencyLocation');
                            locationInput.value = 'Determining address...';
                            locationInput.disabled = true;

                            const address = await getAccuratePhilippineAddress(latitude, longitude);
                            locationInput.value = address;
                            locationInput.disabled = false;
                            isEmergencyFormDirty = true;

                            updateInfoWindowContent(address);
                            emergencyInfoWindow.open(emergencyMap, emergencyMarker);

                            if (accuracy) {
                                const accuracyText = accuracy < 100 ? 'High accuracy' : accuracy < 1000 ? 'Medium accuracy' : 'Low accuracy';
                                showToast(`Location detected with ${accuracyText} (±${Math.round(accuracy)}m)`, 'success');
                            }

                        } catch (error) {
                            console.error('Address determination failed:', error);
                            document.getElementById('emergencyLocation').value = 'Unable to determine exact address - please verify location on map';
                            document.getElementById('emergencyLocation').disabled = false;
                            showToast('Location detected but address lookup failed. Please verify your location on the map.', 'warning');
                        }

                        showLocationVerification();
                    },
                    function(error) {
                        let errorMessage = 'Could not get your location. Please enter it manually.';
                        let toastType = 'warning';

                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage = 'Location access was denied. Please enable location services and refresh the page.';
                                toastType = 'danger';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage = 'Location information is unavailable. Please check your network connection and try again.';
                                break;
                            case error.TIMEOUT:
                                errorMessage = 'Location request timed out. Please try again or enter your location manually.';
                                break;
                            default:
                                errorMessage = `Location error: ${error.message}. Please enter your location manually.`;
                                break;
                        }

                        showToast(errorMessage, toastType);
                    },
                    options
                );

                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }, 16000);
            });
        }
    }

    async function validateApiKey() {
        const apiKey = 'AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE';

        try {
            const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?address=Manila&key=${apiKey}`);
            const data = await response.json();

            if (data.error_message) {
                console.error('API Key validation failed:', data.error_message);
                return false;
            }

            return data.status === 'OK';
        } catch (error) {
            console.error('API Key validation error:', error);
            return false;
        }
    }

    validateApiKey().then(isValid => {
        if (!isValid) {
            console.warn('Google Maps API key may be invalid or have insufficient permissions');
        }
    });

    enhancedLocationDetection();

    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    updateButtonStates();
    updateAllButtonStates();
});