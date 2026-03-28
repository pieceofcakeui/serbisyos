document.addEventListener('DOMContentLoaded', function () {

    $(document).on('click', '.delete-btn', function () {
        const bookingId = $(this).data('booking-id');
        $('#deleteBookingId').val(bookingId);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    });

    $(document).on('click', '.cancel-btn', function () {
        const bookingId = $(this).data('booking-id');
        $('#cancelBookingId').val(bookingId);
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelConfirmationModal'));
        cancelModal.show();
    });

    if (typeof initializeLocation === 'function') {
        initializeLocation();
    }
});