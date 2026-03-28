document.addEventListener('DOMContentLoaded', function () {
    const brandsDeleteModal = new bootstrap.Modal(document.getElementById('brand-vehicle-delete-modal'));
    const editBrandsBtn = document.getElementById('editBrandsBtn');
    const brandsList = document.getElementById('brandsList');
    let currentBrands = [];
    let editingBrandIndex = null;
    let brandToDeleteIndex = null;
    let initialBrands = [];

    const brandsModalHTML = `
    <div class="modal fade" id="brandsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <h5 class="mb-4">Manage Vehicle Brands</h5>
                    <div class="mb-3">
                        <label for="brandInput" class="form-label">Brand Name</label>
                        <input type="text" class="form-control" id="brandInput" placeholder="Enter vehicle brand">
                    </div>
                    <div id="brandsContainer" class="mb-3"></div>
                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning text-white" id="saveBrandBtn" style="height: 40px; padding: 0 10px;" disabled>Add Brand</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', brandsModalHTML);
    const brandsModal = new bootstrap.Modal(document.getElementById('brandsModal'));
    const brandInput = document.getElementById('brandInput');
    const saveBrandBtn = document.getElementById('saveBrandBtn');
    const brandsContainer = document.getElementById('brandsContainer');
    const brandsDeleteText = document.getElementById('brands-delete-text');
    const confirmBrandsDelete = document.getElementById('confirm-brands-delete');

    function updateBrandSaveButton() {
        const hasBrandInput = brandInput.value.trim() !== '';
        saveBrandBtn.disabled = !hasBrandInput;
        saveBrandBtn.textContent = editingBrandIndex !== null ? 'Update Brand' : 'Add Brand';
    }

    function renderBrandsInModal() {
        brandsContainer.innerHTML = '';
        if (currentBrands.length === 0) {
            brandsContainer.innerHTML = '<p class="text-muted">No brands added yet</p>';
            return;
        }
        currentBrands.forEach((brand, index) => {
            const brandItem = document.createElement('div');
            brandItem.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded';
            brandItem.innerHTML = `
                <span>${brand}</span>
                <div>
                    <button class="btn btn-sm btn-outline-primary me-2 edit-btn"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger delete-btn"><i class="fas fa-trash"></i></button>
                </div>
            `;
            brandItem.querySelector('.edit-btn').addEventListener('click', () => {
                brandInput.value = brand;
                editingBrandIndex = index;
                updateBrandSaveButton();
            });
            brandItem.querySelector('.delete-btn').addEventListener('click', () => {
                brandToDeleteIndex = index;
                brandsDeleteText.textContent = `Are you sure you want to delete the brand "${brand}"?`;
                brandsDeleteModal.show();
            });
            brandsContainer.appendChild(brandItem);
        });
    }

    function renderBrandsOnPage() {
        brandsList.innerHTML = '';
        if (currentBrands.length === 0) {
            brandsList.innerHTML = '<div class="view-shop-category-item" role="listitem">No specific brands listed.</div>';
            return;
        }
        currentBrands.forEach(brand => {
            const brandItem = document.createElement('div');
            brandItem.className = 'view-shop-category-item';
            brandItem.setAttribute('role', 'listitem');
            brandItem.innerHTML = `
                <svg aria-hidden="true" focusable="false" viewBox="0 0 24 24">
                    <path d="M9 16.2l-3.5-3.5 1.41-1.41L9 13.38l7.09-7.1 1.41 1.43z"></path>
                </svg>
                ${brand.replace(/</g, "&lt;").replace(/>/g, "&gt;")}
            `;
            brandsList.appendChild(brandItem);
        });
    }
    
    editBrandsBtn.addEventListener('click', () => {
        fetch('../account/backend/brand-vehicle.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentBrands = data.brands;
                    initialBrands = [...currentBrands];
                    renderBrandsInModal();
                    brandsModal.show();
                } else {
                    toastr.error(data.message || 'Failed to load brands');
                }
            }).catch(() => toastr.error('An error occurred while loading brands.'));
    });

    saveBrandBtn.addEventListener('click', () => {
        const brand = brandInput.value.trim();
        if (!brand) return;

        const action = editingBrandIndex !== null ? 'edit' : 'add';
        const formData = new FormData();
        formData.append('action', action);
        formData.append('brand', brand);
        if (action === 'edit') formData.append('index', editingBrandIndex);

        fetch('../account/backend/brand-vehicle.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentBrands = data.brands;
                    renderBrandsInModal();
                    renderBrandsOnPage();
                    brandInput.value = '';
                    editingBrandIndex = null;
                    updateBrandSaveButton();
                    toastr.success(`Brand ${action === 'add' ? 'added' : 'updated'} successfully`);
                } else {
                    toastr.error(data.message || `Failed to ${action} brand`);
                }
            }).catch(() => toastr.error('An error occurred.'));
    });

    confirmBrandsDelete.addEventListener('click', function () {
        if (brandToDeleteIndex === null) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('index', brandToDeleteIndex);

        fetch('../account/backend/brand-vehicle.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentBrands = data.brands;
                    renderBrandsInModal();
                    renderBrandsOnPage();
                    toastr.success('Brand deleted successfully');
                } else {
                    toastr.error(data.message || 'Failed to delete brand');
                }
            }).catch(() => toastr.error('An error occurred while deleting brand.'));
        
        brandsDeleteModal.hide();
        brandToDeleteIndex = null;
    });

    brandInput.addEventListener('input', updateBrandSaveButton);
    document.getElementById('brandsModal').addEventListener('hidden.bs.modal', function () {
        brandInput.value = '';
        editingBrandIndex = null;
        updateBrandSaveButton();
    });
});