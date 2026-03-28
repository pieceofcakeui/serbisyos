<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="report-description mb-4">
                    <h5 class="text-center">Report an Issue</h5>
                </div>

                <form id="reportForm">
                    <input type="hidden" id="reportShopId" name="shop_id">
                    <div class="mb-3">
                        <label for="reportReason" class="form-label">Reason for reporting <span class="text-danger">*</span></label>
                        <select class="form-select" id="reportReason" name="reason" required>
                            <option value="" selected disabled>Select a reason</option>
                            <option value="Inaccurate Information">Inaccurate Information</option>
                            <option value="Poor Service Quality">Poor Service Quality</option>
                            <option value="Unprofessional Behavior">Unprofessional Behavior</option>
                            <option value="Safety Concerns">Safety Concerns</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reportDescription" class="form-label">Additional details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reportDescription" name="description" rows="3"
                            placeholder="Please provide more details about your report" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="submitReport">Submit Report</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteConversationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="mb-3">Delete Conversation</h5>
                <p>Are you sure you want to delete this conversation?</p>
                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteConversation" style="height: 40px; padding: 0 10px;">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade error-modal" id="errorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="mb-3">Error</h5>
                <div id="errorMessage" class="mb-3"></div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="settingsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content position-relative border-0 rounded-4 shadow-lg overflow-hidden">

      <div class="d-flex justify-content-between align-items-center px-4 pt-4">
        <h5 class="mb-0">Settings</h5>
        <button type="button" class="btn-close" onclick="closeSettingsModal()" aria-label="Close"
          style="outline: none; box-shadow: none;"></button>
      </div>

      <div class="modal-body px-4 pb-4 pt-3">
        <a href="#" onclick="openAutoMessageModal()" class="d-flex align-items-center text-decoration-none">
          <i class="fas fa-comment-dots me-2"></i>
          <span style="color: black;">Automated Message</span>
        </a>
      </div>

    </div>
  </div>
</div>


<script>
  function closeSettingsModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('settingsModal'));
    if (modal) {
      modal.hide();
    }
  }
</script>



<script>
let settingsModal, autoMessagesModal;

document.addEventListener('DOMContentLoaded', function() {
    settingsModal = new bootstrap.Modal(document.getElementById('settingsModal'));
    autoMessagesModal = new bootstrap.Modal(document.getElementById('autoMessagesModal'));
});

function closeModal(modalId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    modal.hide();
    cleanupBackdrop();
}

function openAutoMessageModal() {
    closeModal('settingsModal');
    autoMessagesModal.show();
}

function cleanupBackdrop() {
    setTimeout(() => {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }, 10);
}
</script>

