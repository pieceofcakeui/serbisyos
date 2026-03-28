    <div class="modal fade" id="brand-vehicle-delete-modal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-3">
          <div class="modal-body">
            <p id="brands-delete-text" class="mb-3">Are you sure you want to delete this vehicle brand?</p>
            <div class="d-flex justify-content-center gap-2">
              <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger btn-sm" id="confirm-brands-delete">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="modal fade" id="uploadGalleryModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-body text-center p-4">
            <h5 class="mb-4">Upload Gallery Images</h5>
            <form id="uploadGalleryForm" class="d-flex flex-column align-items-center">
              <input class="form-control" type="file" id="galleryImageInput" name="galleryImage" accept="image/*" multiple required style="display:none;">
              <label for="galleryImageInput" id="drop-zone" class="text-center p-4 mb-3">
                <div><i class="fas fa-cloud-upload-alt fa-3x mb-3"></i></div>
                <div>Drag & drop up to 3 files here or click to select</div>
                <div class="small text-muted mt-2">(Max 5MB per image)</div>
              </label>
              <div id="imagePreviewContainer" class="mb-4 w-100"></div>
              <div class="d-flex justify-content-center gap-3 mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="uploadGalleryBtn" style="height: 40px; padding: 0 10px;">Add</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div id="floatingImageViewer" class="d-none position-fixed top-0 start-0 w-100 h-100" style="z-index: 1060; background-color: rgba(0, 0, 0, 0.9);">
      <button id="closeFloatingViewer" class="position-absolute btn rounded-circle p-0 d-flex align-items-center justify-content-center" style="top: 20px; right: 20px; width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); color: white; border: none; z-index: 1070;">
        &times;
      </button>
      <div class="d-flex justify-content-center align-items-center h-100 position-relative">
        <img id="floatingViewImage" src="" class="img-fluid" style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
        <button id="downloadFloatingImage" class="position-absolute btn rounded-pill d-flex align-items-center justify-content-center" style="bottom: 20px; left: 50%; transform: translateX(-50%); background-color: rgba(255, 255, 255, 0.2); color: white; border: none; padding: 8px 16px; gap: 8px;">
          <i class="fas fa-download"></i>
          <span>Download</span>
        </button>
      </div>
    </div>
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable d-flex align-items-center justify-content-center" style="min-height: 100vh;">
        <div class="modal-content text-center">
          <div class="modal-body">
            <h5 class="mb-3" id="deleteConfirmModalLabel">Confirm Deletion</h5>
            <p>Are you sure you want to delete this image? This action cannot be undone.</p>
            <div class="d-flex justify-content-center gap-2 mt-4">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="ownerRestrictionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
          <div class="modal-body text-center">
            <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
            <p id="restrictionMessage" class="mb-3"></p>
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  


<script>
  function showOwnerRestrictionModal(actionType) {
    const modal = new bootstrap.Modal(document.getElementById('ownerRestrictionModal'));
    const messageEl = document.getElementById('restrictionMessage');

    if (actionType === 'book') {
      messageEl.textContent = 'You cannot book an appointment for your own shop. This feature is for customers only.';
    } else {
      messageEl.textContent = 'You cannot request emergency service for your own shop. This feature is for customers only.';
    }

    modal.show();
  }
</script>

<style>
  #brand-vehicle-delete-modal,
  #deleteConfirmModal {
    z-index: 1060;
  }

  body.modal-open-freeze {
    overflow: hidden;
    position: fixed;
    width: 100%;
    height: 100%;
  }

</style>

