document.addEventListener('DOMContentLoaded', function () {

    $(document).on('click', '.delete-btn', function () {
        const requestId = $(this).data('request-id');
        $('#deleteRequestId').val(requestId);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmationModal'));
        deleteModal.show();
    });

    $(document).on('click', '.cancel-btn', function () {
        const requestId = $(this).data('request-id');
        $('#cancelRequestId').val(requestId);
        const cancelModal = new bootstrap.Modal(document.getElementById('cancelConfirmationModal'));
        cancelModal.show();
    });

    if (typeof initializeLocation === 'function') {
        initializeLocation();
    }
});