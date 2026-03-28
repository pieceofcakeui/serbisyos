let map;
let marker;
let autocomplete;
let geocoder;

function initMap() {
    const defaultLatLng = { lat: 10.7203, lng: 122.5620 };
    const mapElement = document.getElementById("map");

    if (!mapElement) {
        return;
    }

    geocoder = new google.maps.Geocoder();

    map = new google.maps.Map(mapElement, {
        center: defaultLatLng,
        zoom: 13,
    });

    marker = new google.maps.Marker({
        position: defaultLatLng,
        map: map,
        draggable: true,
        title: "Drag me to your shop's location!"
    });

    google.maps.event.addListener(marker, 'dragend', function() {
        updateHiddenFields(marker.getPosition());
    });

    const searchInput = document.getElementById('address-search');
    autocomplete = new google.maps.places.Autocomplete(searchInput, {
        fields: ['geometry', 'name', 'formatted_address']
    });
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();

        if (!place.geometry || !place.geometry.location) {
            toastr.error("No details available for input: '" + place.name + "'");
            return;
        }

        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        marker.setPosition(place.geometry.location);
        
        const address = place.formatted_address || searchInput.value;
        const shopLocationInput = document.getElementById('shop_location');
        if(shopLocationInput) {
            shopLocationInput.value = address;
        }
        
        updateHiddenFields(place.geometry.location, address);
    });

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };
                map.setCenter(pos);
                map.setZoom(15);
                marker.setPosition(pos);
                updateHiddenFields(marker.getPosition());
            },
            () => {
                updateHiddenFields(defaultLatLng);
            }
        );
    } else {
        updateHiddenFields(defaultLatLng);
    }
}

function updateHiddenFields(position, knownAddress = null) {
    if (position) {
        const lat = position.lat();
        const lng = position.lng();
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const searchInput = document.getElementById('address-search');
        const shopLocationInput = document.getElementById('shop_location');
        
        if (latInput) latInput.value = lat;
        if (lngInput) lngInput.value = lng;
        if (latInput) latInput.classList.remove('is-invalid');
        if (lngInput) lngInput.classList.remove('is-invalid');
        
        if (knownAddress) {
             if (searchInput) searchInput.value = knownAddress;
             if (shopLocationInput) shopLocationInput.value = knownAddress;
             if (shopLocationInput) shopLocationInput.classList.remove('is-invalid');
        } else if (geocoder) {
            geocoder.geocode({ location: position }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        const address = results[0].formatted_address;
                        if (searchInput) searchInput.value = address;
                        if (shopLocationInput) shopLocationInput.value = address;
                        if (shopLocationInput) shopLocationInput.classList.remove('is-invalid');
                    } else {
                        if (searchInput) searchInput.value = '';
                        if (shopLocationInput) shopLocationInput.value = '';
                    }
                } else {
                     console.error("Geocoder failed due to: " + status);
                     if (searchInput) searchInput.value = '';
                     if (shopLocationInput) shopLocationInput.value = '';
                }
            });
        }

        if (document.getElementById('map')) {
            document.getElementById('map').style.border = '1px solid #ddd';
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const applyNowBtn = document.querySelector('.partner-intro a[href="#partnerForm"]');
    const formContainer = document.querySelector('.form-narrow-container');
    const introSection = document.querySelector('.partner-intro');

    if (applyNowBtn && formContainer && introSection) {
        applyNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            introSection.style.display = 'none';
            formContainer.style.display = 'block';
            formContainer.scrollIntoView({ behavior: 'smooth' });
        });
    }

    document.getElementById('add_brand')?.addEventListener('click', addBrandField);
    document.getElementById('partnerForm')?.addEventListener('submit', handleFormSubmit);
    document.querySelectorAll('input[type="file"]').forEach(input => input.addEventListener('change', validateFileSize));

    const brandsContainer = document.getElementById('brands_container');
    if (brandsContainer) {
        brandsContainer.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('brand-input')) {
                updateAddBrandButtonState();
            }
        });
    }
    updateAddBrandButtonState();

    const dtiSecInput = document.querySelector('input[name="dti_sec_number"]');
    const dtiSecFile = document.querySelector('input[name="dti_sec_file"]');
    if (dtiSecInput && dtiSecFile) {
        dtiSecInput.addEventListener('input', function() {
            const label = dtiSecFile.parentElement.querySelector('label');
            if (this.value.trim()) {
                dtiSecFile.setAttribute('required', 'required');
                if (label && !label.innerHTML.includes(' *')) {
                    label.innerHTML += ' *';
                }
            } else {
                dtiSecFile.removeAttribute('required');
                if (label) {
                    label.innerHTML = label.innerHTML.replace(' *', '');
                }
            }
        });
    }

    const nextBtns = document.querySelectorAll('.next-btn');
    const prevBtns = document.querySelectorAll('.prev-btn');
    const formSteps = document.querySelectorAll('.form-step');
    let currentStep = 0;
    
    const stepTitles = [
        "Basic Business Information",
        "Location & Service Area",
        "Business Licensing",
        "Services Offered",
        "Business Hours",
        "Review & Confirm"
    ];

    const stepCounterText = document.getElementById('step-counter-text');
    const progressLineFill = document.getElementById('progress-line-fill');
    const sectionTitleNumber = document.getElementById('section-title-number');
    const sectionTitleText = document.getElementById('section-title-text');

    function updateStepIndicator() {
        const totalSteps = formSteps.length;
        const requirementsBox = document.getElementById('application-requirements');

        if (stepCounterText) stepCounterText.textContent = `Step ${currentStep + 1} of ${totalSteps}`;
        
        const progressPercentage = ((currentStep) / (totalSteps - 1)) * 100;
        if (progressLineFill) progressLineFill.style.width = `${progressPercentage}%`;
        
        if (sectionTitleNumber) sectionTitleNumber.textContent = currentStep + 1;
        if (sectionTitleText) sectionTitleText.textContent = stepTitles[currentStep];

        if (requirementsBox) {
            requirementsBox.style.display = (currentStep === 0) ? 'block' : 'none';
        }
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                currentStep++;
                updateFormSteps();
                updateStepIndicator();
                window.scrollTo(0, 0);
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            currentStep--;
            updateFormSteps();
            updateStepIndicator();
            window.scrollTo(0, 0);
        });
    });

    function updateFormSteps() {
        formSteps.forEach((step, index) => {
            step.classList.toggle('active', index === currentStep);
        });
        if (currentStep === formSteps.length - 1) {
            populateSummary();
        }
    }

    function validateStep(stepIndex) {
        if (!formSteps[stepIndex]) return false;
        
        const currentStepDiv = formSteps[stepIndex];
        const inputs = currentStepDiv.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        if (document.getElementById('map')) {
            document.getElementById('map').style.border = '1px solid #ddd';
        }

        for (const input of inputs) {
            input.classList.remove('is-invalid');
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');

                if (input.name === 'latitude' || input.name === 'longitude' || input.name === 'shop_location') {
                    toastr.error(`Please pin your exact location on the map.`);
                    const mapEl = document.getElementById('map');
                    if (mapEl) {
                        mapEl.style.border = '2px solid #dc3545';
                        mapEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    input.reportValidity();
                    input.focus();
                    const label = input.parentElement.querySelector('label');
                    const fieldName = label ? label.innerText.replace('*', '').trim() : input.name;
                    toastr.error(`Please check the '${fieldName}' field.`);
                }
                
                isValid = false;
                break; 
            }
        }
        
        if (!isValid) return false;

        if (stepIndex === 3) {
            if (document.querySelectorAll('input[name="services_offered[]"]:checked').length === 0) {
                toastr.error('Please select at least one service.');
                return false;
            }
        }
        if (stepIndex === 4) {
            if (document.querySelectorAll('input[name="days_open[]"]:checked').length === 0) {
                toastr.error('Please select at least one day your business is open.');
                return false;
            }
        }

        return true;
    }

    function populateSummary() {
        const summaryContainer = document.getElementById('summary-container');
        if (!summaryContainer) return;
        
        summaryContainer.innerHTML = '';
        let summaryHtml = '<dl class="row">';

        const addSummaryRow = (label, value) => {
            if (value && value.trim() !== '' && value.trim() !== '<p class="text-muted">No services selected yet.</p>') {
                return `<dt class="col-sm-4">${label}</dt><dd class="col-sm-8">${value}</dd>`;
            }
            return '';
        };

        const getFileName = (selector) => {
            const fileInput = document.querySelector(selector);
            return fileInput && fileInput.files.length > 0 
                ? `<span class="file-name-display"><i class="bi bi-file-earmark-check"></i> ${fileInput.files[0].name}</span>` 
                : '<span class="text-muted">Not provided</span>';
        };

        summaryHtml += '<h5>Basic Info</h5>';
        summaryHtml += addSummaryRow('Shop Name', document.querySelector('[name="shop_name"]').value);
        summaryHtml += addSummaryRow('Owner/Manager', document.querySelector('[name="owner_name"]').value);
        summaryHtml += addSummaryRow('Years in Operation', document.querySelector('[name="years_operation"]').value);
        summaryHtml += addSummaryRow('Email', document.querySelector('[name="email"]').value);
        summaryHtml += addSummaryRow('Phone Number', document.querySelector('[name="phone"]').value);
        summaryHtml += '<hr>';

        summaryHtml += '<h5>Location</h5>';
        summaryHtml += addSummaryRow('City', document.querySelector('[name="town_city"]').value);
        summaryHtml += addSummaryRow('Province', document.querySelector('[name="province"]').value);
        summaryHtml += addSummaryRow('Postal Code', document.querySelector('[name="postal_code"]').value);
        summaryHtml += addSummaryRow('Shop Address', document.querySelector('[name="shop_location"]').value);
        
        const lat = document.querySelector('[name="latitude"]').value;
        const lng = document.querySelector('[name="longitude"]').value;
        if (lat && lng) {
            summaryHtml += addSummaryRow('Coordinates', `${parseFloat(lat).toFixed(6)}, ${parseFloat(lng).toFixed(6)}`);
        }
        
        summaryHtml += '<hr>';
        
        summaryHtml += '<h5>Licensing</h5>';
        summaryHtml += addSummaryRow('Business Permit No.', document.querySelector('[name="business_permit"]').value);
        summaryHtml += addSummaryRow('TIN', document.querySelector('[name="tax_id"]').value);
        summaryHtml += addSummaryRow('DTI/SEC No.', document.querySelector('[name="dti_sec_number"]').value);
        summaryHtml += addSummaryRow('Valid ID Type', document.querySelector('[name="valid_id_type"]').value);
        summaryHtml += addSummaryRow('Business Permit File', getFileName('[name="business_permit_file"]'));
        summaryHtml += addSummaryRow('TIN Document File', getFileName('[name="tax_id_file"]'));
        summaryHtml += addSummaryRow('DTI/SEC File', getFileName('[name="dti_sec_file"]'));
        summaryHtml += addSummaryRow('ID Front', getFileName('[name="valid_id_front"]'));
        summaryHtml += addSummaryRow('ID Back', getFileName('[name="valid_id_back"]'));
        summaryHtml += '<hr>';

        summaryHtml += '<h5>Services & Brands</h5>';
        const selectedServicesContainer = document.getElementById('modal-summary-content');
        const brands = Array.from(document.querySelectorAll('.brand-input')).map(input => input.value.trim()).filter(Boolean).join(', ');
        if (selectedServicesContainer) {
             summaryHtml += addSummaryRow('Services', selectedServicesContainer.innerHTML);
        }
        summaryHtml += addSummaryRow('Brands Serviced', brands);
        summaryHtml += '<hr>';

        summaryHtml += '<h5>Business Hours</h5>';
        const daysOpen = Array.from(document.querySelectorAll('[name="days_open[]"]:checked')).map(cb => cb.parentElement.querySelector('label').innerText).join(', ');
        summaryHtml += addSummaryRow('Opening Time (AM)', document.querySelector('[name="opening_time_am"]').value);
        summaryHtml += addSummaryRow('Closing Time (AM)', document.querySelector('[name="closing_time_am"]').value);
        summaryHtml += addSummaryRow('Opening Time (PM)', document.querySelector('[name="opening_time_pm"]').value);
        summaryHtml += addSummaryRow('Closing Time (PM)', document.querySelector('[name="closing_time_pm"]').value);
        summaryHtml += addSummaryRow('Days Open', daysOpen);
        
        summaryHtml += '</dl>';
        summaryContainer.innerHTML = summaryHtml;
    }
    
    updateStepIndicator();
});

function updateAddBrandButtonState() {
    const addBrandBtn = document.getElementById('add_brand');
    if (!addBrandBtn) return;
    const allBrandInputs = document.querySelectorAll('.brand-input');
    const lastBrandInput = allBrandInputs[allBrandInputs.length - 1];
    addBrandBtn.disabled = !!(lastBrandInput && lastBrandInput.value.trim() === '');
}

function addBrandField() {
    const container = document.getElementById("brands_container");
    const input = document.createElement("input");
    input.type = "text";
    input.className = "form-control mb-2 brand-input";
    input.placeholder = "e.g., Honda";
    input.required = true;
    container.appendChild(input);
    updateAddBrandButtonState();
}

function validateFileSize() {
    const maxSize = 5 * 1024 * 1024;
    for (let file of this.files) {
        if (file.size > maxSize) {
            toastr.error(`File "${file.name}" is too large. Maximum size is 5MB.`);
            this.value = '';
            return;
        }
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    const form = e.target;
    if (!document.getElementById('termsCheck').checked) {
        toastr.error('You must agree to the Terms & Conditions to submit.');
        return;
    }

    const submitBtn = document.getElementById('submitBtn');
    const formData = new FormData(form);

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
    statusText.textContent = 'Please wait, we are processing your submission...';
    modalFooter.style.display = 'none';
    submitBtn.disabled = true;

    processingModal.show();

    let progress = 0;
    let fakeProgressInterval = setInterval(() => {
        if (progress < 95) {
            progress += Math.floor(Math.random() * 5) + 1;
            progress = Math.min(progress, 95);
            progressBar.style.width = `${progress}%`;
            percentageText.textContent = `${progress}%`;
        }
    }, 250);

    try {
        const brands = Array.from(document.querySelectorAll('.brand-input')).map(input => input.value.trim()).filter(Boolean).join(', ');
        formData.set('brands_serviced', brands);

        const response = await fetch(form.action, {
            method: 'POST',
            body: formData
        });
        
        clearInterval(fakeProgressInterval);

        const resultText = await response.text();
        let result;

        try {
            result = JSON.parse(resultText);
        } catch (error) {
            console.error("Failed to parse JSON:", resultText);
            throw new Error("An unexpected server error occurred. Please check the console for details.");
        }
        
        progressBar.style.width = '100%';
        percentageText.textContent = '100%';

        if (result.success) {
            progressBar.classList.remove('bg-warning');
            progressBar.classList.add('bg-success');
            statusText.textContent = 'Application Submitted Successfully! Redirecting...';
            toastr.success(result.message);
            setTimeout(() => window.location.href = '../account/become-a-partner.php?status=application_submitted', 1500);
        } else {
            throw new Error(result.error || 'An unknown error occurred.');
        }

    } catch (error) {
        clearInterval(fakeProgressInterval);
        progressBar.style.width = '100%';
        percentageText.textContent = 'Error';
        progressBar.classList.remove('bg-warning');
        progressBar.classList.add('bg-danger');
        statusText.textContent = `Error: ${error.message}`;
        modalFooter.style.display = 'block';
        submitBtn.disabled = false;
        toastr.error(error.message);
    }
}

window.initMap = initMap;