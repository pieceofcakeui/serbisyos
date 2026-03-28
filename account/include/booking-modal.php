<style>
    /* This sets a maximum height for the modal's body */
    .modal-dialog-scrollable .modal-body {
        max-height: 65vh; /* Adjust this value as needed */
        scrollbar-width: thin; /* For Firefox */
        scrollbar-color: #0d6efd #e9ecef; /* For Firefox */
    }

    /* Webkit-based browsers (Chrome, Safari, Edge) */
    .modal-dialog-scrollable .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-track {
        background: #f1f3f5;
        border-radius: 4px;
    }

    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb {
        background-color: #0d6efd;
        border-radius: 4px;
        transition: background-color 0.3s ease;
    }

    .modal-dialog-scrollable .modal-body::-webkit-scrollbar-thumb:hover {
        background-color: #0b5ed7;
    }

    .modal-content {
        border: 1px solid #dee2e6;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
</style>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 700px; margin: 20px auto;">
        <div class="modal-content">
            <div class="modal-header flex-column align-items-center border-0 pb-0 pt-3">
                <div class="modal-header-content text-center">
                    <h2 class="modal-title fs-5 fw-bold mb-0" id="bookingModalLabel">Booking Details</h2>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-2">
                <div id="bookingDetails" class="booking-details-container">
                    </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-2 px-4">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade status-modal" id="confirmStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="status-icon mb-3">
                    <i class="fas fa-exclamation-circle" style="font-size: 60px; color: #ffc107;"></i>
                </div>
                <h5 class="mb-3" id="confirmStatusModalTitle">Confirm Action</h5>
                <div id="confirmStatusModalBody">
                    <!-- Content will be loaded dynamically -->
                </div>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning flex-grow-1" id="confirmStatusChange" style="height: 39px; padding: 0 10px;">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
