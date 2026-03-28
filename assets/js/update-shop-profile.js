document.addEventListener('DOMContentLoaded', () => {
    const shopInfoForm = document.getElementById('shopInfoForm');
    const updateBtn = document.getElementById('updateInfoBtn');
    const businessHoursContainer = document.getElementById('businessHoursContainer');
    const businessDaysContainer = document.getElementById('businessDaysContainer');
    const shopLogoInput = document.getElementById('shopLogoInput');
    const shopImgPreview = document.getElementById('shopImgPreview');
    const uploadLogoBtn = document.getElementById('uploadLogoBtn');
    const addServiceBtn = document.getElementById('add-service-btn');
    const newServiceInput = document.getElementById('new-service-input');
    
    let initialState = '';

    function getFormState() {
        const formData = new FormData(shopInfoForm);
        const data = {};

        const formElements = shopInfoForm.querySelectorAll('input, select, textarea');
        
        formElements.forEach(element => {
            if (element.type === 'checkbox') {
                if (element.name.endsWith('[]')) {
                    const cleanKey = element.name.slice(0, -2);
                    if (!data[cleanKey]) {
                        data[cleanKey] = [];
                    }
                    if (element.checked) {
                        data[cleanKey].push(element.value);
                    }
                } else {
                    data[element.name] = element.checked;
                }
            } else if (element.type === 'radio') {
                if (element.checked) {
                    data[element.name] = element.value;
                }
            } else if (element.name && element.name.endsWith('[]')) {
                const cleanKey = element.name.slice(0, -2);
                if (!data[cleanKey]) {
                    data[cleanKey] = [];
                }
                if (element.value.trim() !== '') {
                    data[cleanKey].push(element.value);
                }
            } else if (element.name) {
                data[element.name] = element.value;
            }
        });

        data.shopImgPreview = shopImgPreview.src;

        Object.keys(data).forEach(key => {
            if (Array.isArray(data[key])) {
                data[key].sort();
            }
        });
        
        return JSON.stringify(data, Object.keys(data).sort());
    }

    function checkForChanges() {
        const currentState = getFormState();
        const hasChanges = currentState !== initialState;
        updateBtn.disabled = !hasChanges;

        console.log('Initial state:', initialState);
        console.log('Current state:', currentState);
        console.log('Has changes:', hasChanges);
    }

    function initializeFormState() {
        setTimeout(() => {
            businessHoursContainer.style.display = '';
            businessDaysContainer.style.display = '';
            initialState = getFormState();
            updateBtn.disabled = true;
        }, 100);
    }

    uploadLogoBtn.addEventListener('click', () => {
        shopLogoInput.click();
    });

    shopLogoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (!file.type.startsWith('image/')) {
                toastr.error('Please select a valid image file.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                toastr.error('Image file is too large. Please select a file smaller than 5MB.');
                return;
            }
            const reader = new FileReader();
            reader.onload = (e) => {
                shopImgPreview.src = e.target.result;
                checkForChanges();
            };
            reader.readAsDataURL(file);
        }
    });

   shopInfoForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('.edit-shop-profile-btn-primary');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Updating...';
    submitBtn.disabled = true;

    fetch('../account/backend/update-shop-profile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            toastr.success(data.message);
            initialState = getFormState();
            updateBtn.disabled = true;
        } else {
            toastr.error(data.message);
            console.error('Error details:', data.debug || 'No debug info');
        }
    })
    .catch(error => {
        toastr.error('An error occurred: ' + error.message);
        console.error('Fetch error:', error);
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
});

    const formElements = shopInfoForm.querySelectorAll('input, select, textarea');
    formElements.forEach(element => {
        element.addEventListener('input', checkForChanges);
        element.addEventListener('change', checkForChanges);
    });

    initializeFormState();
});