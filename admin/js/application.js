$(document).ready(function () {
    $('.view-btn').on('click', function () {
        var id = $(this).data('id');
        $.ajax({
            url: './backend/view_application.php',
            type: 'GET',
            data: { id: id },
            success: function (response) {
                $('#modal-body-content').html(response);
            },
            error: function () {
                $('#modal-body-content').html('Error loading application details.');
            }
        });
    });

    flatpickr("#date_start, #date_end", {
        dateFormat: "Y-m-d",
        allowInput: true
    });
});

let toastTimeout;

function showToast(message, type = 'success') {
    const toast = document.getElementById('customToast');
    const messageSpan = document.getElementById('customToastMessage');

    if (!toast || !messageSpan) return;

    clearTimeout(toastTimeout);

    messageSpan.textContent = message;
    toast.className = (type === 'success') ? 'toast-success' : 'toast-error';
    toast.style.display = 'block';

    toastTimeout = setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

function initiateRejection(id) {
    $('#reject_app_id').val(id);
    $('#rejection_reason').val('');
    $('#rejection_reason').removeClass('is-invalid');
    
    var rejectionModal = new bootstrap.Modal(document.getElementById('rejectionModal'));
    rejectionModal.show();
}

function confirmRejection() {
    var id = $('#reject_app_id').val();
    var reason = $('#rejection_reason').val().trim();
    var modalEl = document.getElementById('rejectionModal');
    var modal = bootstrap.Modal.getInstance(modalEl);

    if (reason === "") {
        $('#rejection_reason').addClass('is-invalid');
        return;
    }

    modal.hide();

    $.ajax({
        url: './update_status.php',
        type: 'POST',
        data: { 
            id: id, 
            status: 'Rejected', 
            reason: reason
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                showToast('Application rejected successfully.', 'success');
                setTimeout(() => location.reload(), 1500); 
            } else {
                showToast(response.message || 'An error occurred.', 'error');
            }
        },
        error: function () {
            showToast('An error occurred while communicating with the server.', 'error');
        }
    });
}

function updateStatus(id, status) {
    if (status === 'Approved') {
        const row = document.querySelector(`tr[data-id='${id}']`);
        const shopLocation = row ? row.getAttribute('data-location') : '';

        if (!shopLocation || shopLocation.trim() === '') {
            showToast('Shop location must be added before approving.', 'error');
            return; 
        }
    }

    const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    const messageElement = document.getElementById('confirmationMessage');
    const confirmButton = document.getElementById('confirmActionButton');
    const modalTitle = document.getElementById('confirmationModalLabel');

    modalTitle.textContent = 'Confirm ' + status;
    messageElement.textContent = 'Are you sure you want to ' + status.toLowerCase() + ' this application?';
    
    $(confirmButton).off('click').on('click', function () {
        confirmationModal.hide();

        $.ajax({
            url: './update_status.php',
            type: 'POST',
            data: { id: id, status: status },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showToast('Status updated successfully!', 'success');
                    setTimeout(() => location.reload(), 1500); 
                } else {
                    showToast(response.message || 'An unknown error occurred.', 'error');
                }
            },
            error: function () {
                showToast('An error occurred while communicating with the server.', 'error');
            }
        });
    });

    confirmationModal.show();
}

function openImageModal(src) {
    var modal = document.getElementById("imageModal");
    var modalImage = document.getElementById("modalImage");
    modal.style.display = "block";
    modalImage.src = src;
    document.body.style.overflow = "hidden";
}

function closeImageModal() {
    var modal = document.getElementById("imageModal");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

window.onclick = function(event) {
    var modal = document.getElementById("imageModal");
    if (event.target == modal) {
        closeImageModal();
    }
}