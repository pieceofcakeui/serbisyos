$(document).ready(function () {
    $('.booking-tab').click(function () {
        $('.booking-tab').removeClass('active');
        $(this).addClass('active');
        const status = $(this).data('status');
        filterBookings(status);
    });

    function filterBookings(status) {
        $('.empty-state-all, .empty-state-pending, .empty-state-accept, .empty-state-completed, .empty-state-cancelled').hide();

        if (status === 'all') {
            $('.booking-row').show();
            if ($('.booking-row').length > 0) {
                $('#bookingTableContainer').removeClass('hide').show();
            } else {
                $('#bookingTableContainer').addClass('hide').hide();
                $('.empty-state-all').show();
            }
        } else {
            $('.booking-row').hide();
            const matchingRows = $(`.booking-row[data-status="${status}"]`);
            matchingRows.show();
            if (matchingRows.length > 0) {
                $('#bookingTableContainer').removeClass('hide').show();
            } else {
                $('#bookingTableContainer').addClass('hide').hide();
                $(`.empty-state-${status.toLowerCase()}`).show();
            }
        }
    }

    filterBookings('all');

    if ($('.booking-row').length === 0) {
        $('#bookingTableContainer').addClass('hide').hide();
        $('.empty-state-all').show();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('booking-section').classList.remove('d-none');
});

$(document).ready(function () {
    document.addEventListener('click', function (e) {
        if (e.target.closest('.view-booking')) {
            handleBookingViewClick(e.target.closest('.view-booking'));
        }
    });

    function handleBookingViewClick(button) {
        const bookingId = button.dataset.id;
        const modal = new bootstrap.Modal(document.getElementById('bookingModal'));
        const detailsContainer = document.getElementById('bookingDetails');
        showLoadingState(detailsContainer, 'Loading booking details...');
        modal.show();
        fetchBookingDetails(bookingId, detailsContainer);
    }

    function showLoadingState(container, message = 'Loading...') {
        if (!container) return;
        container.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">${message}</p>
                </div>
            `;
    }

    function fetchBookingDetails(bookingId, container) {
        fetch(`../account/backend/get_booking_details.php?id=${bookingId}`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.text();
            })
            .then(data => {
                if (container) {
                    container.innerHTML = data;
                    enhanceLoadedDetails();
                }
            })
            .catch(error => {
                console.error('Error fetching booking details:', error);
                if (container) showErrorMessage(container, `Error loading booking details: ${error.message}`);
            });
    }

    function enhanceLoadedDetails() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    function showErrorMessage(container, message) {
        container.innerHTML = `
                <div class="alert alert-danger m-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <h5 class="alert-heading mb-0">Error Loading Details</h5>
                    </div>
                    <hr>
                    <p class="mb-1">${message}</p>
                    <p class="mb-0">Please try again or contact support.</p>
                </div>
            `;
    }

    let currentStatusChangeData = null;
    const confirmStatusModal = new bootstrap.Modal(document.getElementById('confirmStatusModal'), {
        keyboard: false
    });

    $(document).on('click', '.change-status', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const bookingId = $(this).data('id');
        let newStatus = $(this).data('status');
        const rowElement = $(this).closest('.booking-row');
        if (newStatus === 'Delete') newStatus = 'delete';
        const action = newStatus === 'delete' ? 'delete' : 'update';
        const actionText = newStatus === 'delete' ? 'delete this booking' : `to ${newStatus} this booking`;

        currentStatusChangeData = {
            bookingId,
            newStatus,
            rowElement,
            action,
            actionText
        };

        const statusModal = $('#confirmStatusModal');
        const statusIcon = statusModal.find('.status-icon i');
        const statusTitle = statusModal.find('#confirmStatusModalTitle');

        if (newStatus === 'delete') {
            statusIcon.removeClass().addClass('fas fa-trash-alt text-danger');
            statusTitle.text('Confirm Deletion');
        } else if (newStatus === 'Accept') {
            statusIcon.removeClass().addClass('fas fa-check-circle text-success');
            statusTitle.text('Confirm Acceptance');
        } else if (newStatus === 'Reject') {
            statusIcon.removeClass().addClass('fas fa-times-circle text-danger');
            statusTitle.text('Confirm Rejection');
        } else if (newStatus === 'Completed') {
            statusIcon.removeClass().addClass('fas fa-flag-checkered text-info');
            statusTitle.text('Confirm Completion');
        } else if (newStatus === 'Cancelled') {
            statusIcon.removeClass().addClass('fas fa-ban text-secondary');
            statusTitle.text('Confirm Cancellation');
        } else {
            statusIcon.removeClass().addClass('fas fa-exclamation-circle text-warning');
            statusTitle.text('Confirm Action');
        }

        $('#confirmStatusModalBody').html(`
                <p>Are you sure you want to ${actionText}?</p>
            `);

        confirmStatusModal.show();
    });

    $('#confirmStatusChange').on('click', function () {
        if (!currentStatusChangeData) return;

        const confirmBtn = $(this);
        const originalBtnHtml = confirmBtn.html();
        const { bookingId, newStatus, rowElement } = currentStatusChangeData;

        confirmBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Confirming...
        `);
        rowElement.addClass('updating').css('opacity', '0.6');

        $.ajax({
            url: '../account/backend/update_booking_status.php',
            type: 'POST',
            dataType: 'json',
            data: {
                id: bookingId,
                status: newStatus
            },
            success: function (response) {
                showToast({
                    message: getStatusUpdateMessage(newStatus),
                    type: 'success',
                    icon: 'check-circle',
                    autoHide: true
                });
                confirmStatusModal.hide();
                setTimeout(() => {
                    location.reload();
                }, 1500);
            },
            error: function (xhr) {
                const errorMsg = xhr.responseJSON?.message || xhr.responseText || 'Unknown error';
                showToast({
                    message: `Failed to update status: ${errorMsg}`,
                    type: 'error',
                    icon: 'exclamation-circle',
                    autoHide: true
                });
                confirmStatusModal.hide();
            },
            complete: function () {
                confirmBtn.prop('disabled', false).html(originalBtnHtml);
                rowElement.removeClass('updating').css('opacity', '1');
            }
        });
    });

    function getStatusUpdateMessage(status) {
        switch (status) {
            case 'Accept': return 'Booking accepted successfully';
            case 'Reject': return 'Booking rejected successfully';
            case 'Completed': return 'Booking marked as completed';
            case 'Cancelled': return 'Booking cancelled successfully';
            case 'delete': return 'Booking deleted successfully';
            default: return 'Booking status updated';
        }
    }

    function showToast(options) {
        const defaults = {
            message: '',
            type: 'success',
            icon: 'check-circle',
            autoHide: true,
            delay: 2000
        };
        const settings = { ...defaults, ...options };
        const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
        const toastClass = settings.type === 'success' ? 'toast-success' : 'toast-error';
        const toastHtml = `
        <div id="${toastId}" class="${toastClass}">
            ${settings.message}
        </div>
    `;

        $('#toastContainer').append(toastHtml);
        const toastElement = $(`#${toastId}`);

        if (settings.autoHide) {
            setTimeout(() => {
                toastElement.fadeOut(300, function () {
                    $(this).remove();
                });
            }, settings.delay);
        }


        toastElement.find('.toast-close-button').on('click', function () {
            toastElement.fadeOut(300, function () {
                $(this).remove();
            });
        });
    }

    $('[data-bs-toggle="tooltip"]').tooltip();

});