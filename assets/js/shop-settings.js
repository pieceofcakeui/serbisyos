let isEditingEmergency = false;
let currentEditingEmergencyItem = null;
let isEditingDateTime = false;
let currentEditingItem = null;
let allEmergencyServices = {};
let shopStatusSelect;
let permanentCloseWarning;
let permanentClosedInfo;

let allBookingServices = {};
let currentBookingServiceIds = [];

let currentTransmissionTypes = [];
let currentFuelTypes = [];
let currentVehicleTypes = [];
let currentPreferredDateTimes = [];
let currentEmergencyHours = [];
let currentEmergencyServices = [];

const PREDEFINED_TRANSMISSIONS = ['Automatic', 'Manual', 'Semi-Automatic', 'CVT'];
const PREDEFINED_FUELS = ['Gasoline', 'Diesel', 'Electric', 'Hybrid', 'LPG'];
const PREDEFINED_VEHICLES = ['Sedan', 'SUV', 'Truck', 'Motorcycle', 'Van', 'Hatchback', 'Coupe'];

let unsavedChangesModal;
let pendingNavigation = null;

const beforeUnloadListener = (e) => {
    if (hasUnsavedSettings()) {
        e.preventDefault(); 
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return 'You have unsaved changes. Are you sure you want to leave?';
    }
};

function hasUnsavedSettings() {
    const settingsTab = document.getElementById('settingsTab');
    const saveSettingsBtn = document.getElementById('saveSettingsBtn');
    
    if (settingsTab && settingsTab.classList.contains('edit-shop-profile-active') && saveSettingsBtn && !saveSettingsBtn.disabled) {
        return true;
    }
    return false;
}

document.addEventListener('DOMContentLoaded', () => {
    const bookingToggle = document.getElementById('bookingToggle');
    const emergencyToggle = document.getElementById('emergencyToggle');
    const saveSettingsBtn = document.getElementById('saveSettingsBtn');
    let initialSettingsState = {};

    shopStatusSelect = document.getElementById('shopStatusSelect');
    permanentCloseWarning = document.getElementById('permanentCloseWarning');
    permanentClosedInfo = document.getElementById('permanentClosedInfo');

    const unsavedModalEl = document.getElementById('unsavedChangesModal');
    if (unsavedModalEl) {
        unsavedChangesModal = new bootstrap.Modal(unsavedModalEl);
        
        const confirmLeaveBtn = document.getElementById('confirmLeaveBtn');
        confirmLeaveBtn.addEventListener('click', () => {
            if (typeof pendingNavigation === 'function') {
                window.removeEventListener('beforeunload', beforeUnloadListener);
                pendingNavigation();
            }
            unsavedChangesModal.hide();
            pendingNavigation = null;
        });
    }

    window.addEventListener('beforeunload', beforeUnloadListener);

    document.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || link.hasAttribute('data-bs-toggle')) {
                return;
            }

            if (hasUnsavedSettings()) {
                e.preventDefault();
                pendingNavigation = () => {
                    window.location.href = href;
                };
                unsavedChangesModal.show();
            }
        });
    });

    initModal('transmissionTypesModal', {
        listContainerId: 'transmissionTypesList',
        currentItems: () => currentTransmissionTypes,
        predefinedListId: 'predefinedTransmissionsList',
        predefinedItems: PREDEFINED_TRANSMISSIONS,
        newItemInputId: 'newTransmissionType',
        selectAllCheckboxId: 'selectAllTransmissions',
        type: 'transmissionTypes'
    });
    
    initModal('fuelTypesModal', {
        listContainerId: 'fuelTypesList',
        currentItems: () => currentFuelTypes,
        predefinedListId: 'predefinedFuelTypesList',
        predefinedItems: PREDEFINED_FUELS,
        newItemInputId: 'newFuelType',
        selectAllCheckboxId: 'selectAllFuelTypes',
        type: 'fuelTypes'
    });

    initModal('vehicleTypesModal', {
        listContainerId: 'vehicleTypesList',
        currentItems: () => currentVehicleTypes,
        predefinedListId: 'predefinedVehicleTypesList',
        predefinedItems: PREDEFINED_VEHICLES,
        newItemInputId: 'newVehicleType',
        selectAllCheckboxId: 'selectAllVehicleTypes',
        type: 'vehicleTypes'
    });
    
    const dateTimeModalEl = document.getElementById('preferredDateTimesModal');
    if (dateTimeModalEl) {
        dateTimeModalEl.addEventListener('show.bs.modal', () => {
            populateDateTimeList();
        });
    }

    const emergencyModalEl = document.getElementById('emergencyConfigModal');
    if (emergencyModalEl) {
        emergencyModalEl.addEventListener('show.bs.modal', () => {
            populateEmergencyHoursList();
            populateEmergencyServicesModal();
            updateEmergencyServicesSummary();
        });
    }

    function loadInitialSettings() {
        fetch('../account/backend/update_shop_settings.php')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (!data || !data.success) throw new Error(data?.message || 'Invalid response from server');

                const settings = data.settings || {};
                const bookingConfig = settings.bookingConfig || {};
                const emergencyConfig = settings.emergencyConfig || {};
                
                allEmergencyServices = settings.all_emergency_services || {};
                allBookingServices = settings.all_booking_services || {};

                if (shopStatusSelect) {
                    shopStatusSelect.value = settings.shop_status || 'open';
                    
                    if (shopStatusSelect.value === 'permanently_closed') {
                        permanentClosedInfo.style.display = 'block';
                        permanentCloseWarning.style.display = 'none';
                        shopStatusSelect.disabled = true;
                    } else {
                        shopStatusSelect.disabled = false;
                        permanentClosedInfo.style.display = 'none';
                    }
                }

                if (settings.show_book_now) {
                    bookingToggle.classList.add('edit-shop-profile-active');
                    const bookingSection = document.getElementById('bookingConfigSection');
                    bookingSection.style.display = 'block';
                }

                if (settings.show_emergency) {
                    emergencyToggle.classList.add('edit-shop-profile-active');
                    const emergencySection = document.getElementById('emergencyConfigSection');
                    emergencySection.style.display = 'block';
                }
                
                currentBookingServiceIds = bookingConfig.serviceTypes || [];
                currentTransmissionTypes = bookingConfig.transmissionTypes || [];
                currentFuelTypes = bookingConfig.fuelTypes || [];
                currentVehicleTypes = bookingConfig.vehicleTypes || [];
                currentPreferredDateTimes = bookingConfig.preferredDateTimes || [];
                currentEmergencyHours = emergencyConfig.emergencyHours || [];
                currentEmergencyServices = emergencyConfig.offeredServices || [];

                populateBookingServicesModal();
                updateBookingServicesSummary();

                updateListSummary('transmissionTypes', currentTransmissionTypes, 'type');
                updateListSummary('fuelTypes', currentFuelTypes, 'type');
                updateListSummary('vehicleTypes', currentVehicleTypes, 'type');
                updateListSummary('preferredDateTimes', currentPreferredDateTimes, 'slot');
                updateEmergencySummary(currentEmergencyHours, currentEmergencyServices);

                initialSettingsState = serializeSettingsForm();
                checkSettingsFormState();
            })
            .catch(error => {
                console.error('Error loading settings:', error);
                toastr.error('Failed to load settings. Please try again.');
            });
    }

    function serializeSettingsForm() {
        const data = {
            shop_status: shopStatusSelect ? shopStatusSelect.value : 'open',
            show_book_now: bookingToggle.classList.contains('edit-shop-profile-active'),
            show_emergency: emergencyToggle.classList.contains('edit-shop-profile-active'),
            bookingConfig: {
                serviceTypes: currentBookingServiceIds,
                transmissionTypes: currentTransmissionTypes,
                fuelTypes: currentFuelTypes,
                vehicleTypes: currentVehicleTypes,
                preferredDateTimes: currentPreferredDateTimes
            },
            emergencyConfig: {
                emergencyHours: currentEmergencyHours,
                offeredServices: currentEmergencyServices
            }
        };
        return JSON.stringify(data);
    }

    window.checkSettingsFormState = function() {
        const currentState = serializeSettingsForm();
        const isStateChanged = currentState !== initialSettingsState;
        saveSettingsBtn.disabled = !isStateChanged;
    }

    function toggleFeature(toggleElement, featureName) {
        toggleElement.classList.toggle('edit-shop-profile-active');
        const isActive = toggleElement.classList.contains('edit-shop-profile-active');

        if (featureName === 'show_book_now') {
            document.getElementById('bookingConfigSection').style.display = isActive ? 'block' : 'none';
        } else if (featureName === 'show_emergency') {
            document.getElementById('emergencyConfigSection').style.display = isActive ? 'block' : 'none';
        }
        checkSettingsFormState();
    }

    bookingToggle.addEventListener('click', () => toggleFeature(bookingToggle, 'show_book_now'));
    emergencyToggle.addEventListener('click', () => toggleFeature(emergencyToggle, 'show_emergency'));

    if (shopStatusSelect) {
        shopStatusSelect.addEventListener('change', () => {
            if (shopStatusSelect.value === 'permanently_closed') {
                permanentCloseWarning.style.display = 'block';
            } else {
                permanentCloseWarning.style.display = 'none';
            }
            checkSettingsFormState();
        });
    }

    document.getElementById('settingsTab').addEventListener('input', checkSettingsFormState);
    document.getElementById('settingsTab').addEventListener('click', (e) => {
        if (e.target.closest('button') && !e.target.closest('.collapsible-header')) {
            setTimeout(checkSettingsFormState, 50);
        }
    });

    document.querySelectorAll('.collapsible-header').forEach(header => {
        header.addEventListener('click', () => {
            const content = header.nextElementSibling;
            const icon = header.querySelector('.collapsible-icon');
            content.classList.toggle('open');
            icon.classList.toggle('open');
        });
    });

    window.saveSettings = function() {
        const btn = saveSettingsBtn;
        const payload = JSON.parse(serializeSettingsForm());
        let isConfigurationValid = true;
        let validationMessages = [];

        if (payload.show_book_now) {
            const bookingConfig = payload.bookingConfig;
            if (bookingConfig.serviceTypes.length === 0 ||
                bookingConfig.transmissionTypes.length === 0 ||
                bookingConfig.fuelTypes.length === 0 ||
                bookingConfig.vehicleTypes.length === 0 ||
                bookingConfig.preferredDateTimes.length === 0) {

                isConfigurationValid = false;
                validationMessages.push("Booking configuration is incomplete. Please add at least one item to all booking lists (Services, Transmissions, etc.).");
            }
        }

        if (payload.show_emergency) {
            const emergencyConfig = payload.emergencyConfig;
            if (emergencyConfig.emergencyHours.length === 0 ||
                emergencyConfig.offeredServices.length === 0) {

                isConfigurationValid = false;
                validationMessages.push("Emergency configuration is incomplete. Please add at least one 'Emergency Hour' and one 'Offered Service'.");
            }
        }

        if (!isConfigurationValid) {
            validationMessages.forEach(msg => toastr.warning(msg));
            return;
        }

        const originalText = btn.textContent;
        btn.textContent = 'Saving...';
        btn.disabled = true;

        fetch('../account/backend/update_shop_settings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Settings saved successfully!');
                    initialSettingsState = serializeSettingsForm();
                    checkSettingsFormState();
                } else {
                    toastr.error(data.message || 'Failed to save settings.');
                    btn.disabled = false;
                }
            })
            .catch((error) => {
                console.error("Save Error:", error);
                toastr.error('An error occurred while saving settings.');
                btn.disabled = false;
            })
            .finally(() => {
                btn.textContent = originalText;
            });
    };

    loadInitialSettings();
});

function initModal(modalId, config) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', () => {
        const predefinedList = document.getElementById(config.predefinedListId);
        predefinedList.innerHTML = '';
        config.predefinedItems.forEach(item => {
            const isChecked = config.currentItems().includes(item);
            predefinedList.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${item}" id="${config.type}-${item}" 
                           onchange="handlePredefinedCheckboxChange(this, '${config.type}')" ${isChecked ? 'checked' : ''}>
                    <label class="form-check-label" for="${config.type}-${item}">${item}</label>
                </div>
            `;
        });
        updateSelectAllState(config.type);
        populateModalList(config.type);
    });

    const selectAllCheckbox = document.getElementById(config.selectAllCheckboxId);
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => handleSelectAll(selectAllCheckbox, config.type));
    }
}

function getItemsArrayForType(type) {
    if (type === 'transmissionTypes') return currentTransmissionTypes;
    if (type === 'fuelTypes') return currentFuelTypes;
    if (type === 'vehicleTypes') return currentVehicleTypes;
    return [];
}

function populateModalList(type) {
    const listContainer = document.getElementById(`${type}List`);
    const currentItems = getItemsArrayForType(type);
    listContainer.innerHTML = '';

    if (currentItems.length === 0) {
        listContainer.innerHTML = '<p class="text-muted small text-center">No types selected.</p>';
    }

    currentItems.forEach(item => {
        const listItem = document.createElement('div');
        listItem.className = 'edit-shop-profile-dynamic-list-item';
        listItem.innerHTML = `
            <span>${item}</span>
            <div class="edit-shop-profile-actions">
                <button type="button" onclick="removeItemFromList(this, '${type}')"><i class="fas fa-trash-alt" style="color: red;"></i></button>
            </div>
        `;
        listContainer.appendChild(listItem);
    });
    updateListSummary(type, currentItems, 'type');
    checkSettingsFormState();
}

function handlePredefinedCheckboxChange(checkbox, type) {
    const value = checkbox.value;
    const currentItems = getItemsArrayForType(type);
    
    if (checkbox.checked) {
        if (!currentItems.includes(value)) {
            currentItems.push(value);
        }
    } else {
        const index = currentItems.indexOf(value);
        if (index > -1) {
            currentItems.splice(index, 1);
        }
    }
    populateModalList(type);
    updateSelectAllState(type);
}

function handleSelectAll(selectAllCheckbox, type) {
    const currentItems = getItemsArrayForType(type);
    const predefinedItems = (type === 'transmissionTypes') ? PREDEFINED_TRANSMISSIONS : (type === 'fuelTypes') ? PREDEFINED_FUELS : PREDEFINED_VEHICLES;
    
    const predefinedCheckboxes = document.querySelectorAll(`#predefined${type.charAt(0).toUpperCase() + type.slice(1)}List .form-check-input`);

    if (selectAllCheckbox.checked) {
        predefinedItems.forEach(item => {
            if (!currentItems.includes(item)) {
                currentItems.push(item);
            }
        });
        predefinedCheckboxes.forEach(cb => cb.checked = true);
    } else {
        predefinedItems.forEach(item => {
            const index = currentItems.indexOf(item);
            if (index > -1) {
                currentItems.splice(index, 1);
            }
        });
        predefinedCheckboxes.forEach(cb => cb.checked = false);
    }
    populateModalList(type);
}

function updateSelectAllState(type) {
    const predefinedItems = (type === 'transmissionTypes') ? PREDEFINED_TRANSMISSIONS : (type === 'fuelTypes') ? PREDEFINED_FUELS : PREDEFINED_VEHICLES;
    const currentItems = getItemsArrayForType(type);
    const selectAllCheckbox = document.getElementById(`selectAll${type.charAt(0).toUpperCase() + type.slice(1)}`);
    
    if (selectAllCheckbox) {
        const allSelected = predefinedItems.every(item => currentItems.includes(item));
        selectAllCheckbox.checked = allSelected;
    }
}

window.addManualItem = function(type) {
    const inputId = `new${type.charAt(0).toUpperCase() + type.slice(1, -1)}`;
    const input = document.getElementById(inputId);
    const value = input.value.trim();
    const currentItems = getItemsArrayForType(type);

    if (value) {
        if (!currentItems.includes(value)) {
            currentItems.push(value);
            populateModalList(type);
            input.value = '';
        } else {
            toastr.warning('This item is already in the list.');
        }
    }
}

window.removeItemFromList = function(button, type) {
    const item = button.closest('.edit-shop-profile-dynamic-list-item');
    const value = item.querySelector('span').textContent;
    const currentItems = getItemsArrayForType(type);

    const index = currentItems.indexOf(value);
    if (index > -1) {
        currentItems.splice(index, 1);
    }
    
    populateModalList(type);
    
    const predefinedCheckbox = document.getElementById(`${type}-${value}`);
    if (predefinedCheckbox) {
        predefinedCheckbox.checked = false;
    }
    updateSelectAllState(type);
}

function updateListSummary(type, items, itemType = 'type') {
    const summaryEl = document.getElementById(`${type}Summary`);
    if (!summaryEl) return;
    
    const count = items.length;
    if (count === 0) {
        summaryEl.textContent = itemType === 'slot' ? 'No slots defined.' : 'No types selected.';
    } else {
        summaryEl.textContent = `${count} ${itemType}${count > 1 ? 's' : ''} selected.`;
    }
}

function populateDateTimeList() {
    const list = document.getElementById('preferredDateTimesList');
    list.innerHTML = '';
    
    if (currentPreferredDateTimes.length === 0) {
        list.innerHTML = '<p class="text-muted small text-center">No slots defined.</p>';
    }

    currentPreferredDateTimes.forEach(value => {
        const listItem = document.createElement('div');
        listItem.className = 'edit-shop-profile-dynamic-list-item';
        listItem.innerHTML = `
            <span>${value}</span>
            <div class="edit-shop-profile-actions">
                <button type="button" onclick="editDateTimeItem(this)"><i class="fas fa-edit"></i></button>
                <button type="button" onclick="removeDateTimeItem(this)"><i class="fas fa-trash-alt" style="color: red;"></i></button>
            </div>
        `;
        list.appendChild(listItem);
    });
    updateListSummary('preferredDateTimes', currentPreferredDateTimes, 'slot');
    checkSettingsFormState();
}

window.addDateTimeItem = function() {
    const dateInput = document.getElementById('newPreferredDate');
    const openTimeInput = document.getElementById('newPreferredOpenTime');
    const closeTimeInput = document.getElementById('newPreferredCloseTime');
    const slotsInput = document.getElementById('newSlots');

    const dateValue = dateInput.value;
    const openTime = openTimeInput.value;
    const closeTime = closeTimeInput.value;
    const slots = slotsInput.value;

    if (dateValue && openTime && closeTime && slots > 0) {
        if (openTime >= closeTime) {
            toastr.warning('Closing time must be after opening time.');
            return;
        }

        const dateObj = new Date(dateValue + 'T00:00:00');
        const month = String(dateObj.getMonth() + 1).padStart(2, '0');
        const day = String(dateObj.getDate()).padStart(2, '0');
        const year = dateObj.getFullYear();
        const formattedDate = `${month}/${day}/${year}`;
        const value = `${formattedDate}, ${openTime} - ${closeTime} (${slots} slots)`;

        const isDuplicate = currentPreferredDateTimes.some(item => {
            const existingDate = item.split(',')[0].trim();
            return existingDate === formattedDate && (!isEditingDateTime || item !== currentEditingItem);
        });

        if (isDuplicate) {
            toastr.warning('A schedule for this date already exists. Please edit the existing entry.');
            return;
        }

        if (isEditingDateTime && currentEditingItem) {
            const index = currentPreferredDateTimes.indexOf(currentEditingItem);
            if (index > -1) {
                currentPreferredDateTimes.splice(index, 1);
            }
        }
        
        currentPreferredDateTimes.push(value);
        currentPreferredDateTimes.sort((a, b) => new Date(a.split(',')[0]) - new Date(b.split(',')[0]));
        
        toastr.success(`Schedule ${isEditingDateTime ? 'updated' : 'added'} successfully!`);
        populateDateTimeList();
        cancelDateTimeEdit();
    } else {
        toastr.warning('Please fill all date, time, and slot fields correctly.');
    }
}

window.editDateTimeItem = function(button) {
    if (isEditingDateTime) cancelDateTimeEdit();

    const item = button.closest('.edit-shop-profile-dynamic-list-item');
    const currentValue = item.querySelector('span').textContent;
    const regex = /^(\d{2}\/\d{2}\/\d{4}),\s*(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})\s*\((\d+)\s*slots?\)$/;
    const match = currentValue.match(regex);

    if (match) {
        const [, dateStr, openTime, closeTime, slots] = match;
        const parts = dateStr.split('/');
        const isoDate = `${parts[2]}-${parts[0]}-${parts[1]}`;

        document.getElementById('newPreferredDate').value = isoDate;
        document.getElementById('newPreferredOpenTime').value = openTime;
        document.getElementById('newPreferredCloseTime').value = closeTime;
        document.getElementById('newSlots').value = slots;

        isEditingDateTime = true;
        currentEditingItem = currentValue;
        item.style.display = 'none';

        const addButton = document.querySelector('#preferredDateTimesModal .datetime-buttons button[onclick="addDateTimeItem()"]');
        const cancelButton = document.getElementById('cancelDateTimeEdit');
        addButton.textContent = 'Update';
        cancelButton.style.display = 'inline-block';
    } else {
        toastr.error('Could not parse the date/time format for editing.');
    }
}

window.cancelDateTimeEdit = function() {
    if (isEditingDateTime && currentEditingItem) {
        const itemElement = Array.from(document.querySelectorAll('#preferredDateTimesList .edit-shop-profile-dynamic-list-item span'))
                                .find(span => span.textContent === currentEditingItem)
                                ?.closest('.edit-shop-profile-dynamic-list-item');
        if (itemElement) {
            itemElement.style.display = 'flex';
        }
    }

    document.getElementById('newPreferredDate').value = '';
    document.getElementById('newPreferredOpenTime').value = '';
    document.getElementById('newPreferredCloseTime').value = '';
    document.getElementById('newSlots').value = '';

    isEditingDateTime = false;
    currentEditingItem = null;

    const addButton = document.querySelector('#preferredDateTimesModal .datetime-buttons button[onclick="addDateTimeItem()"]');
    const cancelButton = document.getElementById('cancelDateTimeEdit');

    if (addButton) addButton.textContent = 'Add';
    if (cancelButton) cancelButton.style.display = 'none';
}

window.removeDateTimeItem = function(button) {
    const item = button.closest('.edit-shop-profile-dynamic-list-item');
    const value = item.querySelector('span').textContent;
    
    if (isEditingDateTime && currentEditingItem === value) {
        cancelDateTimeEdit();
    }
    
    const index = currentPreferredDateTimes.indexOf(value);
    if (index > -1) {
        currentPreferredDateTimes.splice(index, 1);
    }
    
    populateDateTimeList();
}

function updateEmergencySummary(hours, services) {
    const summaryEl = document.getElementById('emergencyConfigSummary');
    if (!summaryEl) return;
    
    const hoursCount = hours.length;
    const servicesCount = services.length;

    if (hoursCount === 0 && servicesCount === 0) {
        summaryEl.textContent = 'No configuration set.';
    } else {
        summaryEl.textContent = `${hoursCount} hour slot${hoursCount !== 1 ? 's' : ''} & ${servicesCount} service${servicesCount !== 1 ? 's' : ''} defined.`;
    }
}

function populateEmergencyHoursList() {
    const list = document.getElementById('emergencyHoursList');
    list.innerHTML = '';
    
    if (currentEmergencyHours.length === 0) {
        list.innerHTML = '<p class="text-muted small text-center">No hours defined.</p>';
    }

    currentEmergencyHours.forEach(value => {
        renderEmergencyHourItem(list, value, true);
    });
    updateEmergencySummary(currentEmergencyHours, currentEmergencyServices);
    checkSettingsFormState();
}

function renderEmergencyHourItem(list, value, canEdit = false) {
    const listItem = document.createElement('div');
    listItem.className = 'edit-shop-profile-dynamic-list-item';
    const editButton = canEdit ? `<button type="button" onclick="editEmergencyHour(this)"><i class="fas fa-edit"></i></button>` : '';
    listItem.innerHTML = `
        <span>${value}</span>
        <div class="edit-shop-profile-actions">
            ${editButton}
            <button type="button" onclick="removeEmergencyHour(this)"><i class="fas fa-trash-alt" style="color: red;"></i></button>
        </div>
    `;
    list.appendChild(listItem);
}

window.removeEmergencyHour = function(button) {
    const item = button.closest('.edit-shop-profile-dynamic-list-item');
    const value = item.querySelector('span').textContent;

    if (isEditingEmergency && currentEditingItem === value) {
        cancelEmergencyEdit();
    }
    
    const index = currentEmergencyHours.indexOf(value);
    if (index > -1) {
        currentEmergencyHours.splice(index, 1);
    }
    
    populateEmergencyHoursList();
}

window.addEmergencyHour = function() {
    const dayInput = document.getElementById('newEmergencyDay');
    const openTimeInput = document.getElementById('newEmergencyOpenTime');
    const closeTimeInput = document.getElementById('newEmergencyCloseTime');

    const day = dayInput.value;
    const openTime = openTimeInput.value;
    const closeTime = closeTimeInput.value;

    if (day && openTime && closeTime) {
        if (openTime >= closeTime) {
            toastr.warning('Closing time must be after opening time.');
            return;
        }
        const value = `${day}, ${openTime} - ${closeTime}`;

        const isDuplicate = currentEmergencyHours.some(item => {
            const existingDay = item.split(',')[0].trim();
            return existingDay === day && (!isEditingEmergency || item !== currentEditingItem);
        });

        if (isDuplicate) {
            toastr.warning('Emergency hours for this day already exist. Please edit the existing entry.');
            return;
        }

        if (isEditingEmergency && currentEditingItem) {
            const index = currentEmergencyHours.indexOf(currentEditingItem);
            if (index > -1) {
                currentEmergencyHours.splice(index, 1);
            }
        }

        currentEmergencyHours.push(value);
        toastr.success(`Emergency hour ${isEditingEmergency ? 'updated' : 'added'}!`);

        populateEmergencyHoursList();
        cancelEmergencyEdit();
    } else {
        toastr.warning('Please fill all fields (Day, Open Time, and Close Time).');
    }
}

window.editEmergencyHour = function(button) {
    if (isEditingEmergency) cancelEmergencyEdit();
    const item = button.closest('.edit-shop-profile-dynamic-list-item');
    const currentValue = item.querySelector('span').textContent;

    const regex = /^(.+?),\s*(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/;
    const match = currentValue.match(regex);

    if (match) {
        const [, day, openTime, closeTime] = match;
        document.getElementById('newEmergencyDay').value = day.trim();
        document.getElementById('newEmergencyOpenTime').value = openTime;
        document.getElementById('newEmergencyCloseTime').value = closeTime;

        isEditingEmergency = true;
        currentEditingItem = currentValue;
        item.style.display = 'none';

        const addButton = document.querySelector('#emergencyConfigModal .datetime-buttons button[onclick="addEmergencyHour()"]');
        const cancelButton = document.getElementById('cancelEmergencyEdit');
        addButton.textContent = 'Update';
        cancelButton.style.display = 'inline-block';
    } else {
        toastr.error('Could not parse the emergency hour format for editing.');
    }
}

window.cancelEmergencyEdit = function() {
    if (isEditingEmergency && currentEditingItem) {
        const itemElement = Array.from(document.querySelectorAll('#emergencyHoursList .edit-shop-profile-dynamic-list-item span'))
                                .find(span => span.textContent === currentEditingItem)
                                ?.closest('.edit-shop-profile-dynamic-list-item');
        if (itemElement) {
            itemElement.style.display = 'flex';
        }
    }

    document.getElementById('newEmergencyDay').value = '';
    document.getElementById('newEmergencyOpenTime').value = '';
    document.getElementById('newEmergencyCloseTime').value = '';

    isEditingEmergency = false;
    currentEditingItem = null;

    const addButton = document.querySelector('#emergencyConfigModal .datetime-buttons button[onclick="addEmergencyHour()"]');
    const cancelButton = document.getElementById('cancelEmergencyEdit');

    if (addButton) addButton.textContent = 'Add';
    if (cancelButton) cancelButton.style.display = 'none';
}

function populateEmergencyServicesModal() {
    const accordionContainer = document.getElementById('emergencyCategoryAccordion');
    if (!accordionContainer || Object.keys(allEmergencyServices).length === 0) {
        accordionContainer.innerHTML = '<p class="text-center text-muted">No emergency services found in the database.</p>';
        return;
    }
    accordionContainer.innerHTML = ''; 

    let categoryIndex = 0;
    for (const categoryName in allEmergencyServices) {
        const subcategories = allEmergencyServices[categoryName];
        let subcategoriesHtml = '';
        let subcategoryIndex = 0;

        for (const subcategoryName in subcategories) {
            const services = subcategories[subcategoryName];
            let servicesHtml = '';
            
            if (services && services.length > 0) {
                servicesHtml += `
                    <div class="form-check form-check-inline border-bottom pb-2 mb-2 w-100">
                        <input class="form-check-input" type="checkbox" 
                               id="emergency-select-all-${categoryIndex}-${subcategoryIndex}" 
                               data-category="${categoryName}" data-subcategory="${subcategoryName}"
                               onchange="toggleAllEmergencyServices(this)">
                        <label class="form-check-label fw-bold" for="emergency-select-all-${categoryIndex}-${subcategoryIndex}">
                            Select All
                        </label>
                    </div>
                `;
                
                services.forEach(service => {
                    const isChecked = currentEmergencyServices.includes(service.value);
                    servicesHtml += `
                        <div class="form-check">
                            <input class="form-check-input emergency-service-checkbox" 
                                   type="checkbox" 
                                   value="${service.value}" 
                                   id="emergency-service-${service.value}"
                                   data-category="${categoryName}" data-subcategory="${subcategoryName}"
                                   ${isChecked ? 'checked' : ''} 
                                   onclick="handleEmergencyServiceChange(this)">
                            <label class="form-check-label" for="emergency-service-${service.value}">
                                ${service.name}
                            </label>
                        </div>
                    `;
                });
            } else {
                servicesHtml = '<p class="text-muted small mb-0">No specific services listed.</p>';
            }

            subcategoriesHtml += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="emergency-headingSubcat-${categoryIndex}-${subcategoryIndex}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#emergency-collapseSubcat-${categoryIndex}-${subcategoryIndex}">
                            ${subcategoryName}
                        </button>
                    </h2>
                    <div id="emergency-collapseSubcat-${categoryIndex}-${subcategoryIndex}" class="accordion-collapse collapse" data-bs-parent="#emergency-subcategoryAccordion-${categoryIndex}">
                        <div class="accordion-body">
                            ${servicesHtml}
                        </div>
                    </div>
                </div>
            `;
            subcategoryIndex++;
        }

        const categoryHtml = `
            <div class="accordion-item mb-3">
                <h2 class="accordion-header" id="emergency-headingCategory-${categoryIndex}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#emergency-collapseCategory-${categoryIndex}">
                        ${categoryName}
                    </button>
                </h2>
                <div id="emergency-collapseCategory-${categoryIndex}" class="accordion-collapse collapse" data-bs-parent="#emergencyCategoryAccordion">
                    <div class="accordion-body">
                        <div class="accordion" id="emergency-subcategoryAccordion-${categoryIndex}">
                            ${subcategoriesHtml || '<p class="text-muted small mb-0">No sub-categories available.</p>'}
                        </div>
                    </div>
                </div>
            </div>
        `;
        accordionContainer.innerHTML += categoryHtml;
        categoryIndex++;
    }
    updateAllEmergencySelectAllStates();
}

function handleEmergencyServiceChange(checkbox) {
    const serviceValue = checkbox.value;
    const isChecked = checkbox.checked;

    if (isChecked) {
        if (!currentEmergencyServices.includes(serviceValue)) {
            currentEmergencyServices.push(serviceValue);
        }
    } else {
        currentEmergencyServices = currentEmergencyServices.filter(id => id !== serviceValue);
    }

    updateEmergencyServicesSummary();
    updateEmergencySelectAllState(checkbox);
    checkSettingsFormState();
}

function toggleAllEmergencyServices(selectAllCheckbox) {
    const category = selectAllCheckbox.dataset.category;
    const subcategory = selectAllCheckbox.dataset.subcategory;
    const isChecked = selectAllCheckbox.checked;
    
    const serviceCheckboxes = document.querySelectorAll(`.emergency-service-checkbox[data-category="${category}"][data-subcategory="${subcategory}"]`);

    serviceCheckboxes.forEach(checkbox => {
        if (checkbox.checked !== isChecked) {
            checkbox.checked = isChecked;
            handleEmergencyServiceChange(checkbox);
        }
    });
}

function updateEmergencySelectAllState(serviceCheckbox) {
    const accordionBody = serviceCheckbox.closest('.accordion-body');
    if (!accordionBody) return;

    const selectAllCheckbox = accordionBody.querySelector('input[id^="emergency-select-all-"]');
    if (!selectAllCheckbox) return;

    const category = selectAllCheckbox.dataset.category;
    const subcategory = selectAllCheckbox.dataset.subcategory;
    
    const allServiceCheckboxes = accordionBody.querySelectorAll(`.emergency-service-checkbox[data-category="${category}"][data-subcategory="${subcategory}"]`);

    if (allServiceCheckboxes.length > 0) {
        const allChecked = Array.from(allServiceCheckboxes).every(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
    }
}

function updateAllEmergencySelectAllStates() {
    const allSelectAllCheckboxes = document.querySelectorAll('input[id^="emergency-select-all-"]');
    allSelectAllCheckboxes.forEach(selectAllCheckbox => {
        const category = selectAllCheckbox.dataset.category;
        const subcategory = selectAllCheckbox.dataset.subcategory;
        const serviceCheckboxes = document.querySelectorAll(`.emergency-service-checkbox[data-category="${category}"][data-subcategory="${subcategory}"]`);
        
        if (serviceCheckboxes.length > 0) {
            const allChecked = Array.from(serviceCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        }
    });
}

function updateEmergencyServicesSummary() {
    const summaryContainer = document.getElementById('emergency-edit-summary-container');
    const summaryCountBadge = document.getElementById('emergency-summary-count');
    const summaryCountBadge2 = document.getElementById('emergency-summary-count-2');

    const count = currentEmergencyServices.length;
    if (summaryCountBadge) summaryCountBadge.textContent = count;
    if (summaryCountBadge2) summaryCountBadge2.textContent = count;
    
    updateEmergencySummary(currentEmergencyHours, currentEmergencyServices);
    
    if (!summaryContainer) return;
    if (count === 0) {
        summaryContainer.innerHTML = `
        <div class="text-center text-muted p-4 d-flex flex-column justify-content-center align-items-center" style="height: 100%;">
            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
            <p class="mb-1 fw-bold">No Services Selected</p>
            <p class="small">Your chosen services will appear here.</p>
        </div>`;
        return;
    }

    let summaryHtml = '';
    const summary = {};

    currentEmergencyServices.forEach(serviceValue => {
        let found = false;
        for (const categoryName in allEmergencyServices) {
            for (const subcategoryName in allEmergencyServices[categoryName]) {
                const service = allEmergencyServices[categoryName][subcategoryName].find(s => s.value === serviceValue);
                if (service) {
                    if (!summary[categoryName]) summary[categoryName] = {};
                    if (!summary[categoryName][subcategoryName]) summary[categoryName][subcategoryName] = [];
                    summary[categoryName][subcategoryName].push(service.name);
                    found = true;
                    break;
                }
            }
            if(found) break;
        }
    });

    for (const catName in summary) {
        summaryHtml += `<h6 class="summary-category mt-3 mb-2">${catName}</h6>`;
        for (const subName in summary[catName]) {
            summaryHtml += `<div class="summary-subcategory ps-2">${subName}</div>`;
            summaryHtml += `<ul class="summary-service-list">`;
            summary[catName][subName].forEach(serviceName => {
                summaryHtml += `<li>${serviceName}</li>`;
            });
            summaryHtml += `</ul>`;
        }
    }
    summaryContainer.innerHTML = summaryHtml;
}

function populateBookingServicesModal() {
    const accordionContainer = document.getElementById('bookingCategoryAccordion');
    if (!accordionContainer || Object.keys(allBookingServices).length === 0) {
        accordionContainer.innerHTML = '<p class="text-center text-muted">No services found. Please add services to your shop profile first.</p>';
        return;
    }
    accordionContainer.innerHTML = ''; 

    for (const catId in allBookingServices) {
        const category = allBookingServices[catId];
        let subcategoriesHtml = '';

        for (const subId in category.subcategories) {
            const sub = category.subcategories[subId];
            let servicesHtml = '';
            
            if (sub.services && sub.services.length > 0) {
                servicesHtml += `
                    <div class="form-check form-check-inline border-bottom pb-2 mb-2 w-100">
                        <input class="form-check-input" type="checkbox" 
                               id="booking-select-all-${sub.id}" 
                               data-sub-id="${sub.id}" 
                               onchange="toggleAllBookingServices(this)">
                        <label class="form-check-label fw-bold" for="booking-select-all-${sub.id}">
                            Select All
                        </label>
                    </div>
                `;
                
                sub.services.forEach(service => {
                    const isChecked = currentBookingServiceIds.includes(String(service.id));
                    servicesHtml += `
                        <div class="form-check">
                            <input class="form-check-input booking-service-checkbox-${sub.id}" 
                                   type="checkbox" 
                                   value="${service.id}" 
                                   id="booking-service-${service.id}" 
                                   ${isChecked ? 'checked' : ''} 
                                   onclick="handleBookingServiceChange(this)">
                            <label class="form-check-label" for="booking-service-${service.id}">
                                ${service.name}
                            </label>
                        </div>
                    `;
                });
            } else {
                servicesHtml = '<p class="text-muted small mb-0">No specific services listed.</p>';
            }

            subcategoriesHtml += `
                <div class="accordion-item">
                    <h2 class="accordion-header" id="booking-headingSubcat-${sub.id}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#booking-collapseSubcat-${sub.id}">
                            ${sub.name}
                        </button>
                    </h2>
                    <div id="booking-collapseSubcat-${sub.id}" class="accordion-collapse collapse" data-bs-parent="#booking-subcategoryAccordion-${category.id}">
                        <div class="accordion-body">
                            ${servicesHtml}
                        </div>
                    </div>
                </div>
            `;
        }

        const categoryHtml = `
            <div class="accordion-item mb-3">
                <h2 class="accordion-header" id="booking-headingCategory-${category.id}">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#booking-collapseCategory-${category.id}">
                        <i class="fas ${category.icon} me-2"></i>
                        ${category.name}
                    </button>
                </h2>
                <div id="booking-collapseCategory-${category.id}" class="accordion-collapse collapse" data-bs-parent="#bookingCategoryAccordion">
                    <div class="accordion-body">
                        <div class="accordion" id="booking-subcategoryAccordion-${category.id}">
                            ${subcategoriesHtml || '<p class="text-muted small mb-0">No sub-categories available.</p>'}
                        </div>
                    </div>
                </div>
            </div>
        `;
        accordionContainer.innerHTML += categoryHtml;
    }
    updateAllBookingSelectAllStates();
}

function handleBookingServiceChange(checkbox) {
    const serviceId = checkbox.value;
    const isChecked = checkbox.checked;

    if (isChecked) {
        if (!currentBookingServiceIds.includes(serviceId)) {
            currentBookingServiceIds.push(serviceId);
        }
    } else {
        currentBookingServiceIds = currentBookingServiceIds.filter(id => id !== serviceId);
    }

    updateBookingServicesSummary();
    updateBookingSelectAllState(checkbox);
    checkSettingsFormState();
}

function toggleAllBookingServices(selectAllCheckbox) {
    const subId = selectAllCheckbox.dataset.subId;
    const isChecked = selectAllCheckbox.checked;
    const serviceCheckboxes = document.querySelectorAll(`.booking-service-checkbox-${subId}`);

    serviceCheckboxes.forEach(checkbox => {
        if (checkbox.checked !== isChecked) {
            checkbox.checked = isChecked;
            handleBookingServiceChange(checkbox);
        }
    });
}

function updateBookingSelectAllState(serviceCheckbox) {
    const accordionBody = serviceCheckbox.closest('.accordion-body');
    if (!accordionBody) return;

    const selectAllCheckbox = accordionBody.querySelector('input[id^="booking-select-all-"]');
    if (!selectAllCheckbox) return;

    const subId = selectAllCheckbox.dataset.subId;
    const allServiceCheckboxes = accordionBody.querySelectorAll(`.booking-service-checkbox-${subId}`);

    if (allServiceCheckboxes.length > 0) {
        const allChecked = Array.from(allServiceCheckboxes).every(cb => cb.checked);
        selectAllCheckbox.checked = allChecked;
    }
}

function updateAllBookingSelectAllStates() {
    const allSelectAllCheckboxes = document.querySelectorAll('input[id^="booking-select-all-"]');
    allSelectAllCheckboxes.forEach(selectAllCheckbox => {
        const subId = selectAllCheckbox.dataset.subId;
        const serviceCheckboxes = document.querySelectorAll(`.booking-service-checkbox-${subId}`);
        
        if (serviceCheckboxes.length > 0) {
            const allChecked = Array.from(serviceCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        }
    });
}

function updateBookingServicesSummary() {
    const summaryButton = document.getElementById('bookingServicesSummary');
    const summaryContainer = document.getElementById('booking-edit-summary-container');
    const summaryCountBadge = document.getElementById('booking-summary-count');

    const count = currentBookingServiceIds.length;
    summaryCountBadge.textContent = count;
    
    if (summaryButton) {
        summaryButton.textContent = count === 0 ? 'No services selected.' : `${count} service${count > 1 ? 's' : ''} selected.`;
    }

    if (!summaryContainer) return;
    
    if (count === 0) {
        summaryContainer.innerHTML = `
        <div class="text-center text-muted p-4 d-flex flex-column justify-content-center align-items-center" style="height: 100%;">
            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
            <p class="mb-1 fw-bold">No Services Selected</p>
            <p class="small">Your chosen services will appear here.</p>
        </div>`;
        return;
    }

    let summaryHtml = '';
    const summary = {};

    currentBookingServiceIds.forEach(serviceId => {
        for (const cat of Object.values(allBookingServices)) {
            for (const sub of Object.values(cat.subcategories)) {
                const service = sub.services.find(s => String(s.id) === serviceId);
                if (service) {
                    if (!summary[cat.name]) summary[cat.name] = {};
                    if (!summary[cat.name][sub.name]) summary[cat.name][sub.name] = [];
                    summary[cat.name][sub.name].push(service.name);
                    break;
                }
            }
        }
    });

    for (const catName in summary) {
        summaryHtml += `<h6 class="summary-category mt-3 mb-2">${catName}</h6>`;
        for (const subName in summary[catName]) {
            summaryHtml += `<div class="summary-subcategory ps-2">${subName}</div>`;
            summaryHtml += `<ul class="summary-service-list">`;
            summary[catName][subName].forEach(serviceName => {
                summaryHtml += `<li>${serviceName}</li>`;
            });
            summaryHtml += `</ul>`;
        }
    }
    summaryContainer.innerHTML = summaryHtml;
}