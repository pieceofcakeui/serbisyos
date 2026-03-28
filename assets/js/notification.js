$(document).ready(function() {
    $('#markAllAsRead').click(function(e) {
        e.preventDefault();
        $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        $.ajax({
            url: '../account/backend/mark_all_notifications_read.php',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#markAllAsRead').html('<i class="fas fa-check-circle"></i> Mark All as Read');
                if (response && response.success) {
                    $('.notification-item.unread').removeClass('unread').css({
                        'background-color': '#ffffff',
                        'border-left': '1px solid #e0e0e0'
                    });
                    showToast('All notifications marked as read', 'success');
                } else {
                    const errorMsg = response && response.message ? response.message : 'Unknown error occurred';
                    showToast('Failed to mark all as read: ' + errorMsg, 'error');
                }
            },
            error: function(xhr, status, error) {
                $('#markAllAsRead').html('<i class="fas fa-check-circle"></i> Mark All as Read');
                let errorMsg = error;
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response && response.message) {
                        errorMsg = response.message;
                    }
                } catch (e) {
                    if (xhr.responseText) {
                        errorMsg = xhr.responseText;
                    }
                }
                showToast('Error: ' + errorMsg, 'error');
            }
        });
    });
});

$(document).ready(function() {
    function getStatusBadgeClass(status) {
        switch (status.toLowerCase()) {
            case 'accepted':
                return 'bg-success';
            case 'completed':
                return 'bg-info';
            case 'rejected':
                return 'bg-danger';
            default:
                return 'bg-secondary';
        }
    }

    function formatDateTime(datetimeString) {
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        };
        return new Date(datetimeString).toLocaleString('en-US', options);
    }

    $(document).on('click', '.view-emergency-details', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const requestId = $(this).data('emergency-id');
        console.log('Emergency request ID:', requestId);
        if (!requestId) {
            console.error('No emergency request ID found');
            return;
        }
        const modal = new bootstrap.Modal(document.getElementById('emergencyRequestModal'));
        modal.show();
        $('#modalEmergencyRequesterName').text('Loading...');
        $('#modalEmergencyPhone').text('Loading...');
        $('#modalEmergencyAddress').text('Loading...');
        $('#modalEmergencyVehicleType').text('Loading...');
        $('#modalEmergencyVehicleModel').text('Loading...');
        $('#modalEmergencyDescription').text('Loading...');
        $('#modalEmergencyVideo').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading video...</div>');
        $.ajax({
            url: '../account/backend/get_emergency_request.php',
            type: 'GET',
            data: {
                request_id: requestId
            },
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Response:', response);
                if (response.success && response.request) {
                    const request = response.request;
                    $('#modalEmergencyRequesterName').text(request.requester_name || request.customer_name || 'Not provided');
                    $('#modalEmergencyPhone').text(request.contact_number || 'Not provided');
                    $('#modalEmergencyAddress').text(request.full_address || 'Not provided');
                    $('#modalEmergencyVehicleType').text(request.vehicle_type || 'Not specified');
                    $('#modalEmergencyVehicleModel').text(request.vehicle_model || 'Not specified');
                    $('#modalEmergencyDescription').text(request.issue_description || 'No description provided');

                    const videoContainer = $('#modalEmergencyVideo');
                    videoContainer.empty();
                    if (request.video) {
                        videoContainer.html(`
                                <div class="video-container">
                                    <video controls class="w-100 rounded">
                                        <source src="../account/uploads/emergency_videos/${request.video}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            `);
                    } else {
                        videoContainer.html(`
                                <div class="col-12 d-flex align-items-center justify-content-center">
                                    <div class="p-4 text-center w-100">
                                        <i class="fas fa-video-slash fa-2x text-muted mb-2"></i>
                                        <p class="text-muted mb-0">No video provided for this request</p>
                                    </div>
                                </div>
                            `);
                    }
                } else {
                    $('#modalEmergencyDescription').html(`
                            <div class="alert alert-danger">
                                Failed to load request details. ${response.message || 'Please try again later.'}
                            </div>
                        `);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                $('#modalEmergencyDescription').html(`
                        <div class="alert alert-danger">
                            Error loading request details: ${error}
                        </div>
                    `);
            }
        });
    });

    $(document).on('click', '.view-booking-details', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const bookingId = $(this).data('booking-id');
        fetchBookingDetails(bookingId);
    });

    function fetchBookingDetails(bookingId) {
        const modal = $('#bookingDetailsModal');
        modal.find('.form-control-static').text('Loading...');
        modal.modal('show');

        $.ajax({
            url: '../account/backend/notif_bookings.php',
            type: 'GET',
            data: {
                id: bookingId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.booking) {
                    populateModal(response.booking);
                } else {
                    showToast('Error: ' + (response.message || 'Could not load details.'), 'error');
                    modal.modal('hide');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
                showToast('A server error occurred. Please try again later.', 'error');
                modal.modal('hide');
            }
        });
    }

    function populateModal(data) {
        $('#modalCustomerName').text(data.customer_name || 'Not provided');
        $('#modalCustomerPhone').text(data.customer_phone || 'Not provided');
        $('#modalCustomerEmail').text(data.customer_email || 'Not provided');

        $('#modalVehicleMakeModel').text(data.vehicle_make_model || 'Not provided');
        $('#modalPlateNumber').text(data.plate_number || 'Not specified');
        $('#modalVehicleType').text(data.vehicle_type || 'Not specified');
        $('#modalVehicleYear').text(data.vehicle_year || 'Not specified');
        $('#modalTransmission').text(data.transmission_type || 'Not specified');
        $('#modalFuelType').text(data.fuel_type || 'Not specified');

        $('#modalServiceType').text(data.formatted_services || 'Not specified');
        $('#modalPreferredDateTime').text(data.preferred_datetime || 'Not specified');

        $('#modalVehicleIssues').text(data.vehicle_issues || 'No issues described');
        $('#modalCustomerNotes').text(data.customer_notes || 'None');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const menuButtons = document.querySelectorAll('.btn-menu');
    menuButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            document.querySelectorAll('.dropdown-content').forEach(content => {
                if (content !== dropdown) {
                    content.classList.remove('show');
                    content.style.display = 'none';
                }
            });
            document.querySelectorAll('.btn-menu').forEach(btn => {
                if (btn !== this) {
                    btn.classList.remove('active');
                }
            });
            if (dropdown.classList.contains('show')) {
                this.classList.remove('active');
                dropdown.classList.remove('show');
                dropdown.style.display = 'none';
            } else {
                this.classList.add('active');
                dropdown.style.display = 'block';
                void dropdown.offsetWidth;
                dropdown.classList.add('show');
            }
        });
    });
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-content').forEach(content => {
            content.classList.remove('show');
            content.style.display = 'none';
        });
        document.querySelectorAll('.btn-menu').forEach(btn => {
            btn.classList.remove('active');
        });
    });
    document.querySelectorAll('.dropdown-content').forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.closest('.ellipsis-dropdown') ||
                e.target.closest('.notification-actions') ||
                e.target.tagName === 'A' ||
                e.target.tagName === 'BUTTON') {
                return;
            }
            const notificationId = this.dataset.notificationId;
            if (notificationId && this.classList.contains('unread')) {
                markNotificationAsRead(notificationId, this);
            }
        });
    });
});

function showDeleteModal(link, event) {
    event.preventDefault();
    event.stopPropagation();
    const notificationItem = link.closest('.notification-item');
    notificationItem.classList.add('active-for-delete');
    const notificationId = notificationItem.dataset.notificationId;

    const modal = document.getElementById('deleteModal');
    modal.dataset.notificationId = notificationId;
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
    closeDropdown(link);
}

const confirmDeleteBtn = document.getElementById('confirmDelete');
if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener('click', function() {
        const deleteModal = document.getElementById('deleteModal');
        const notificationId = deleteModal.dataset.notificationId;
        const activeNotification = document.querySelector('.notification-item.active-for-delete');
        if (!notificationId) {
            showToast('Missing notification information', 'danger');
            return;
        }
        fetch('../account/backend/delete_notification.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `notification_id=${notificationId}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (activeNotification) {
                        const notificationList = activeNotification.closest('.notification-list');
                        activeNotification.remove();
                        const remainingNotifications = notificationList.querySelectorAll('.notification-item').length;
                        if (remainingNotifications === 0) {
                            let prevElement = notificationList.previousElementSibling;
                            while (prevElement) {
                                if (prevElement.classList.contains('category-title') ||
                                    (prevElement.tagName === 'HR' && prevElement.style.borderTop === '1px solid rgb(204, 204, 204)')) {
                                    const toRemove = prevElement;
                                    prevElement = prevElement.previousElementSibling;
                                    toRemove.remove();
                                } else {
                                    break;
                                }
                            }
                            notificationList.remove();
                        }
                    }
                    showToast('Notification removed successfully', 'success');
                    const badge = document.querySelector('.notification-badge');
                    if (badge) {
                        const count = parseInt(badge.textContent) - 1;
                        if (count > 0) {
                            badge.textContent = count;
                        } else {
                            badge.remove();
                        }
                    }
                    checkEmptyState();
                } else {
                    showToast(data.message || 'Failed to remove notification', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error removing notification', 'danger');
            })
            .finally(() => {
                const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                modalInstance.hide();
            });
    });
}

function checkEmptyState() {
    const anyNotifications = document.querySelector('.notification-list .notification-item');
    const emptyState = document.querySelector('.empty-state');
    const notificationListContainer = document.querySelector('.notification-list');

    if (!anyNotifications && notificationListContainer) {
        if (!emptyState) {
            const emptyStateHTML = `
                <div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>You don't have any notifications at this moment</p>
                </div>
            `;
            notificationListContainer.innerHTML = emptyStateHTML;
        }
    }
}


function shareNotification(link, event) {
    event.preventDefault();
    event.stopPropagation();
    const notificationItem = link.closest('.notification-item');
    const shopName = notificationItem.querySelector('.notification-message').textContent.replace(/^New (nearby|recommended) shop: /, '');
    const viewShopLink = notificationItem.querySelector('.notification-actions .btn-warning').href;
    const meta = notificationItem.querySelector('.notification-meta')?.textContent || '';
    if (navigator.share) {
        navigator.share({
            title: `Check out this auto shop: ${shopName}`,
            text: `I found this auto shop you might be interested in:\n${shopName}\nLocation: ${meta}`,
            url: viewShopLink
        }).catch(err => {
            console.log('Sharing failed:', err);
            fallbackShare(shopName, meta, viewShopLink);
        });
    } else {
        fallbackShare(shopName, meta, viewShopLink);
    }
    closeDropdown(link);
}

function fallbackShare(shopName, meta, viewShopLink) {
    const shareText = `Check out this auto shop: ${shopName}\nLocation: ${meta}\n\nView shop: ${viewShopLink}`;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(shareText).then(() => {
            showToast('Shop link copied to clipboard!', 'success');
        }).catch(() => {
            prompt('Copy this link to share:', viewShopLink);
        });
    } else {
        prompt('Copy this link to share:', viewShopLink);
    }
}

function closeDropdown(link) {
    const dropdown = link.closest('.dropdown-content');
    const button = link.closest('.ellipsis-dropdown').querySelector('.btn-menu');
    dropdown.classList.remove('show');
    dropdown.style.display = 'none';
    button.classList.remove('active');
}

function showToast(message, type) {
    const toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) return;

    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');

    toast.id = toastId;
    toast.className = `toast text-white bg-${type} border-0 show w-auto`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `<div class="toast-body">${message}</div>`;

    toastContainer.appendChild(toast);

    setTimeout(() => {
        const toastElement = document.getElementById(toastId);
        if (toastElement) {
            toastElement.classList.remove('show');
            setTimeout(() => toastElement.remove(), 500);
        }
    }, 3000);
}