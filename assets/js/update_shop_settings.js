document.addEventListener('DOMContentLoaded', function () {
    const bookNowToggle = document.getElementById('shopShowBookNowToggle');
    const emergencyToggle = document.getElementById('shopShowEmergencyToggle');
    const bookingFormCustomization = document.getElementById('bookingFormCustomization');
    const saveBtn = document.getElementById('shopSaveSettingsBtn');
    const modalElement = document.getElementById('editShopModal');

    let editingFuelRow = null, editingServiceRow = null, editingScheduleRow = null, editingVehicleRow = null;

    function initialize() {
        setupEventListeners();
        loadShopBookingSettings();
    }

    function setupEventListeners() {
        if (bookNowToggle && bookingFormCustomization) {
            bookNowToggle.addEventListener('change', function () {
                bookingFormCustomization.style.display = this.checked ? 'block' : 'none';
                validateSaveButton();
            });
        }

        if (emergencyToggle) {
            emergencyToggle.addEventListener('change', validateSaveButton);
        }

        setupCrudEventListeners('fuel');
        setupCrudEventListeners('vehicle');
        setupCrudEventListeners('service');
        setupScheduleEventListeners();

        $('#shopBookingSettingsForm').on('submit', handleFormSubmit);
    }

    function loadShopBookingSettings() {
        $.ajax({
            url: '../account/backend/get_shop_settings.php',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    populateTable('fuel', response.fuel_types || []);
                    populateTable('vehicle', response.vehicle_types || []);
                    populateTable('service', response.service_types || []);
                    populateScheduleTable(response.business_hours || {});
                    
                    initializeToggleStates();
                    validateSaveButton();
                } else {
                    console.error("Failed to load shop settings:", response.message);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error loading settings:", textStatus, errorThrown);
            }
        });
    }

    function populateTable(type, data) {
        const tableBody = document.getElementById(`${type}TableBody`);
        if (!tableBody) return;
        tableBody.innerHTML = '';
        data.forEach(item => {
            if (item) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="${type}-name">${item}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-${type}-btn"><i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-${type}-btn"><i class="fas fa-trash"></i></button>
                    </td>`;
                tableBody.appendChild(row);
            }
        });
    }

    function populateScheduleTable(data) {
        const scheduleTableBody = document.getElementById('scheduleTableBody');
        if (!scheduleTableBody) return;
        scheduleTableBody.innerHTML = '';
        Object.entries(data).forEach(([day, hours]) => {
            if (hours.start && hours.end) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="day-name">${day}</td>
                    <td class="day-time">${hours.start} - ${hours.end}</td>
                    <td class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-schedule-btn"><i class="fas fa-edit"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-schedule-btn"><i class="fas fa-trash"></i></button>
                    </td>`;
                scheduleTableBody.appendChild(row);
            }
        });
    }

    function setupCrudEventListeners(type) {
        const actionBtn = document.getElementById(`${type}ActionBtn`);
        const input = document.getElementById(`${type}Input`);
        const tableBody = document.getElementById(`${type}TableBody`);
        let editingRow = null;

        if (actionBtn) {
            actionBtn.addEventListener('click', function () {
                const value = input.value.trim();
                if (!value) return;

                if (editingRow) {
                    editingRow.querySelector(`.${type}-name`).textContent = value;
                    editingRow = null;
                    actionBtn.innerHTML = '<i class="fas fa-plus"></i> Add';
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="${type}-name">${value}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-${type}-btn"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-${type}-btn"><i class="fas fa-trash"></i></button>
                        </td>`;
                    tableBody.appendChild(row);
                }
                input.value = '';
                validateSaveButton();
            });
        }

        if (tableBody) {
            tableBody.addEventListener('click', function (e) {
                const editBtn = e.target.closest(`.edit-${type}-btn`);
                const deleteBtn = e.target.closest(`.delete-${type}-btn`);

                if (editBtn) {
                    const row = editBtn.closest('tr');
                    input.value = row.querySelector(`.${type}-name`).textContent.trim();
                    editingRow = row;
                    actionBtn.innerHTML = '<i class="fas fa-sync"></i> Update';
                } else if (deleteBtn) {
                    deleteBtn.closest('tr').remove();
                    validateSaveButton();
                }
            });
        }
    }

    function setupScheduleEventListeners() {
        const scheduleActionBtn = document.getElementById('scheduleActionBtn');
        const scheduleTableBody = document.getElementById('scheduleTableBody');
        const daySelect = document.getElementById('daySelect');
        const startTimeInput = document.getElementById('startTimeInput');
        const endTimeInput = document.getElementById('endTimeInput');
        let editingScheduleRow = null;

        if (scheduleActionBtn) {
            scheduleActionBtn.addEventListener('click', function() {
                const day = daySelect.value;
                const start = startTimeInput.value;
                const end = endTimeInput.value;

                if (!day || !start || !end) {
                    alert('Please select a day and enter both start and end times.');
                    return;
                }
                
                const existingRowForDay = [...scheduleTableBody.querySelectorAll('tr')].find(r => r.querySelector('.day-name').textContent.trim() === day);

                if (editingScheduleRow) {
                    if (editingScheduleRow.querySelector('.day-name').textContent.trim() !== day && existingRowForDay) {
                        alert('A schedule for this day already exists.');
                        return;
                    }
                    editingScheduleRow.querySelector('.day-name').textContent = day;
                    editingScheduleRow.querySelector('.day-time').textContent = `${start} - ${end}`;
                    editingScheduleRow = null;
                    scheduleActionBtn.innerHTML = '<i class="fas fa-plus"></i> Add';
                } else if (existingRowForDay) {
                    alert('A schedule for this day already exists.');
                    return;
                } else {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td class="day-name">${day}</td>
                        <td class="day-time">${start} - ${end}</td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary edit-schedule-btn"><i class="fas fa-edit"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-schedule-btn"><i class="fas fa-trash"></i></button>
                        </td>`;
                    scheduleTableBody.appendChild(row);
                }

                daySelect.value = '';
                startTimeInput.value = '';
                endTimeInput.value = '';
                validateSaveButton();
            });
        }
        
        if (scheduleTableBody) {
            scheduleTableBody.addEventListener('click', function(e) {
                const editBtn = e.target.closest('.edit-schedule-btn');
                const deleteBtn = e.target.closest('.delete-schedule-btn');

                if (editBtn) {
                    const row = editBtn.closest('tr');
                    const day = row.querySelector('.day-name').textContent.trim();
                    const [start, end] = row.querySelector('.day-time').textContent.trim().split(' - ');
                    daySelect.value = day;
                    startTimeInput.value = start;
                    endTimeInput.value = end;
                    editingScheduleRow = row;
                    scheduleActionBtn.innerHTML = '<i class="fas fa-sync"></i> Update';
                } else if (deleteBtn) {
                    deleteBtn.closest('tr').remove();
                    validateSaveButton();
                }
            });
        }
    }

    function validateSaveButton() {
        if (!saveBtn) return;

        const isDataChanged = () => {
            const initialData = {
                bookNow: saveBtn.getAttribute('data-initial-booknow') === 'true',
                emergency: saveBtn.getAttribute('data-initial-emergency') === 'true',
                fuel: saveBtn.getAttribute('data-initial-fuel') || '[]',
                vehicle: saveBtn.getAttribute('data-initial-vehicle') || '[]',
                service: saveBtn.getAttribute('data-initial-service') || '[]',
                schedule: saveBtn.getAttribute('data-initial-schedule') || '{}'
            };
            const currentData = getCurrentFormData();
            return JSON.stringify(initialData) !== JSON.stringify(currentData);
        };

        const bookNowEnabled = bookNowToggle && bookNowToggle.checked;
        let bookingFormValid = true;

        if (bookNowEnabled) {
            const fuelRows = document.querySelectorAll('#fuelTableBody tr').length > 0;
            const vehicleRows = document.querySelectorAll('#vehicleTableBody tr').length > 0;
            const serviceRows = document.querySelectorAll('#serviceTableBody tr').length > 0;
            const scheduleRows = document.querySelectorAll('#scheduleTableBody tr').length > 0;

            bookingFormValid = fuelRows && vehicleRows && serviceRows && scheduleRows;

            highlightEmptySection('fuelTableBody', !fuelRows);
            highlightEmptySection('vehicleTableBody', !vehicleRows);
            highlightEmptySection('serviceTableBody', !serviceRows);
            highlightEmptySection('scheduleTableBody', !scheduleRows);
        } else {
            ['fuel', 'vehicle', 'service', 'schedule'].forEach(type => highlightEmptySection(`${type}TableBody`, false));
        }

        const atLeastOneToggleEnabled = bookNowEnabled || (emergencyToggle && emergencyToggle.checked);
        saveBtn.disabled = !(atLeastOneToggleEnabled && isDataChanged() && bookingFormValid);
    }
    
    function highlightEmptySection(sectionId, isEmpty) {
        const section = document.getElementById(sectionId);
        const parentCard = section ? section.closest('.border.rounded') : null;
        if (parentCard) {
            parentCard.classList.toggle('border-danger', isEmpty);
            parentCard.classList.toggle('bg-danger-light', isEmpty);
        }
    }
    
    function initializeToggleStates() {
        if (!saveBtn) return;
        const data = getCurrentFormData();
        saveBtn.setAttribute('data-initial-booknow', data.bookNow.toString());
        saveBtn.setAttribute('data-initial-emergency', data.emergency.toString());
        saveBtn.setAttribute('data-initial-fuel', data.fuel);
        saveBtn.setAttribute('data-initial-vehicle', data.vehicle);
        saveBtn.setAttribute('data-initial-service', data.service);
        saveBtn.setAttribute('data-initial-schedule', data.schedule);
    }
    
    function getCurrentFormData() {
        return {
            bookNow: bookNowToggle ? bookNowToggle.checked : false,
            emergency: emergencyToggle ? emergencyToggle.checked : false,
            fuel: JSON.stringify([...document.querySelectorAll('#fuelTableBody .fuel-name')].map(el => el.textContent)),
            vehicle: JSON.stringify([...document.querySelectorAll('#vehicleTableBody .vehicle-name')].map(el => el.textContent)),
            service: JSON.stringify([...document.querySelectorAll('#serviceTableBody .service-name')].map(el => el.textContent)),
            schedule: JSON.stringify(Object.fromEntries([...document.querySelectorAll('#scheduleTableBody tr')].map(row => [row.querySelector('.day-name').textContent, row.querySelector('.day-time').textContent])))
        };
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        const $form = $(this);
        const $saveBtn = $('#shopSaveSettingsBtn');
        const $status = $('#shopSettingsStatus');

        if ($saveBtn.prop('disabled')) return;

        $saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Saving...');
        $status.empty().removeClass('alert-success alert-danger');

        const formData = $form.serializeArray().reduce((obj, item) => {
            obj[item.name] = item.value;
            return obj;
        }, {});

        const bookingData = {
            fuel_types: JSON.stringify([...document.querySelectorAll('#fuelTableBody .fuel-name')].map(el => el.textContent.trim())),
            vehicle_types: JSON.stringify([...document.querySelectorAll('#vehicleTableBody .vehicle-name')].map(el => el.textContent.trim())),
            service_types: JSON.stringify([...document.querySelectorAll('#serviceTableBody .service-name')].map(el => el.textContent.trim())),
            business_hours: JSON.stringify(Object.fromEntries([...document.querySelectorAll('#scheduleTableBody tr')].map(row => {
                const day = row.querySelector('.day-name').textContent.trim();
                const timeRange = row.querySelector('.day-time').textContent.trim().split(' - ');
                return [day, { start: timeRange[0], end: timeRange[1] }];
            })))
        };

        $.ajax({
            url: '../account/backend/update_shop_settings.php',
            type: 'POST',
            data: { ...formData, ...bookingData },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    $status.html('<div class="alert alert-success">Settings saved successfully! Reloading...</div>');
                    
                    setTimeout(() => {
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (modalInstance) {
                            modalElement.addEventListener('hidden.bs.modal', () => {
                                location.reload();
                            }, { once: true });
                            modalInstance.hide();
                        } else {
                            location.reload();
                        }
                    }, 1000);

                } else {
                    $status.html(`<div class="alert alert-danger">${res.message || 'An unknown error occurred.'}</div>`);
                    $saveBtn.prop('disabled', false).html('Save Preferences');
                }
            },
            error: function () {
                $status.html('<div class="alert alert-danger">A network error occurred. Please try again.</div>');
                $saveBtn.prop('disabled', false).html('Save Preferences');
            }
        });
    }

    initialize();

    document.getElementById('fuelActionBtn')?.addEventListener('click', function () {
        const fuelInput = document.getElementById('fuelInput');
        const fuelType = fuelInput.value.trim();

        if (fuelType) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${fuelType}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-fuel">Remove</button>
                </td>
            `;
            document.getElementById('fuelTableBody').appendChild(newRow);
            fuelInput.value = '';
            updateHiddenInputs();
        }
    });

    document.getElementById('vehicleActionBtn')?.addEventListener('click', function () {
        const vehicleInput = document.getElementById('vehicleInput');
        const vehicleType = vehicleInput.value.trim();

        if (vehicleType) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${vehicleType}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-vehicle">Remove</button>
                </td>
            `;
            document.getElementById('vehicleTableBody').appendChild(newRow);
            vehicleInput.value = '';
            updateHiddenInputs();
        }
    });

    document.getElementById('paymentActionBtn')?.addEventListener('click', function () {
        const paymentInput = document.getElementById('paymentInput');
        const paymentType = paymentInput.value.trim();

        if (paymentType) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${paymentType}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-payment">Remove</button>
                </td>
            `;
            document.getElementById('paymentTableBody').appendChild(newRow);
            paymentInput.value = '';
            updateHiddenInputs();
        }
    });

    document.getElementById('serviceActionBtn')?.addEventListener('click', function () {
        const serviceInput = document.getElementById('serviceInput');
        const serviceType = serviceInput.value.trim();

        if (serviceType) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${serviceType}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-service">Remove</button>
                </td>
            `;
            document.getElementById('serviceTableBody').appendChild(newRow);
            serviceInput.value = '';
            updateHiddenInputs();
        }
    });

    document.getElementById('scheduleActionBtn')?.addEventListener('click', function () {
        const daySelect = document.getElementById('daySelect');
        const startTimeInput = document.getElementById('startTimeInput');
        const endTimeInput = document.getElementById('endTimeInput');

        if (daySelect.value && startTimeInput.value && endTimeInput.value) {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>${daySelect.value}</td>
                <td>${startTimeInput.value} - ${endTimeInput.value}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger remove-schedule">Remove</button>
                </td>
            `;
            document.getElementById('scheduleTableBody').appendChild(newRow);
            daySelect.value = '';
            startTimeInput.value = '';
            endTimeInput.value = '';
            updateHiddenInputs();
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-fuel')) {
            e.target.closest('tr').remove();
            updateHiddenInputs();
        }
        if (e.target.classList.contains('remove-vehicle')) {
            e.target.closest('tr').remove();
            updateHiddenInputs();
        }
        if (e.target.classList.contains('remove-payment')) {
            e.target.closest('tr').remove();
            updateHiddenInputs();
        }
        if (e.target.classList.contains('remove-service')) {
            e.target.closest('tr').remove();
            updateHiddenInputs();
        }
        if (e.target.classList.contains('remove-schedule')) {
            e.target.closest('tr').remove();
            updateHiddenInputs();
        }
    });

    function updateHiddenInputs() {
        const fuelTypes = [];
        document.querySelectorAll('#fuelTableBody tr').forEach(row => {
            fuelTypes.push(row.cells[0].textContent.trim());
        });
        if (document.getElementById('fuelTypesInput')) {
            document.getElementById('fuelTypesInput').value = JSON.stringify(fuelTypes);
        }

        const vehicleTypes = [];
        document.querySelectorAll('#vehicleTableBody tr').forEach(row => {
            vehicleTypes.push(row.cells[0].textContent.trim());
        });
        if (document.getElementById('vehicleTypesInput')) {
            document.getElementById('vehicleTypesInput').value = JSON.stringify(vehicleTypes);
        }

        const paymentMethods = [];
        document.querySelectorAll('#paymentTableBody tr').forEach(row => {
            paymentMethods.push(row.cells[0].textContent.trim());
        });
        if (document.getElementById('paymentMethodsInput')) {
            document.getElementById('paymentMethodsInput').value = JSON.stringify(paymentMethods);
        }

        const serviceTypes = [];
        document.querySelectorAll('#serviceTableBody tr').forEach(row => {
            serviceTypes.push(row.cells[0].textContent.trim());
        });
        if (document.getElementById('serviceTypesInput')) {
            document.getElementById('serviceTypesInput').value = JSON.stringify(serviceTypes);
        }

        const businessHours = {};
        document.querySelectorAll('#scheduleTableBody tr').forEach(row => {
            const day = row.cells[0].textContent.trim();
            const times = row.cells[1].textContent.trim().split(' - ');
            businessHours[day] = {
                start: times[0],
                end: times[1]
            };
        });
        if (document.getElementById('businessHoursInput')) {
            document.getElementById('businessHoursInput').value = JSON.stringify(businessHours);
        }
    }

    document.querySelector('form')?.addEventListener('submit', function () {
        updateHiddenInputs();
    });
});