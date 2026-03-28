function showReportModal(shopId) {
    if (!window.userLoggedIn) {
        toastr.warning('Please log in to report this shop.');
        return;
    }

   const modalHtml = `
    <div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h5 class="mb-0">Report Shop</h5>
                    </div>
                    <form id="reportForm" class="d-flex flex-column gap-3">
                        <input type="hidden" id="reportShopId" name="shop_id" value="${shopId}">
                        <div class="mb-3">
                            <label for="reportReason" class="form-label">Reason</label>
                            <select class="form-select" id="reportReason" name="reason" required>
                                <option value="" selected disabled>Select a reason</option>
                                <option value="Inaccurate Information">Inaccurate Information</option>
                                <option value="Fake Reviews">Fake Reviews</option>
                                <option value="Spam or Scam">Spam or Scam</option>
                                <option value="Inappropriate Content">Inappropriate Content</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reportDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="reportDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="submitReport" style="height: 37px; padding: 0 10px;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>`;
    
    const existingModal = document.getElementById('reportModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = new bootstrap.Modal(document.getElementById('reportModal'));
    modal.show();

    document.getElementById('submitReport').addEventListener('click', function() {
        const form = document.getElementById('reportForm');
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        const formData = new FormData(form);

        fetch(`${BASE_URL}/account/backend/submit_report.php`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Report submitted successfully');
                    modal.hide();
                } else {
                    toastr.error(data.message || 'Error submitting report');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while submitting the report');
            });
    });

    document.getElementById('reportModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}