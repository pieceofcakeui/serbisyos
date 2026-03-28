<?php
include 'backend/auth.php';
include 'backend/db_connection.php';
include 'backend/security_helper.php';

$specific_sub_id = 0;

if (isset($_POST['sub_id'])) {
    $encrypted_id = $_POST['sub_id'];
    $decrypted_id = URLSecurity::decryptId($encrypted_id);

    if ($decrypted_id > 0) {
        $specific_sub_id = $decrypted_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Verification</title>
       <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .id-image {
            max-height: 250px;
            width: 100%;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .modal-body img {
            max-width: 100%;
        }

        #qr-canvas {
            display: none;
        }

        .nav-tabs .nav-link {
            cursor: pointer;
        }

        .nav-tabs .nav-link.active {
            font-weight: bold;
        }

        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .nav-tabs .nav-item {
            flex-shrink: 0;
        }

        #table-header {
            display: none;
        }

        #cropper-container>img {
            max-width: 100%;
        }

        .personal-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .info-row {
            margin-bottom: 10px;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
        }

        .info-value {
            color: #212529;
        }

        .image-placeholder {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 250px;
            border: 2px dashed #ced4da;
            border-radius: 8px;
            background-color: #f8f9fa;
            color: #6c757d;
            width: 100%;
        }

        #detailsModal .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        #cropper-actions {
            position: relative;
            z-index: 9999 !important;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }

        #cropper-actions button {
            pointer-events: auto !important;
            position: relative;
            z-index: 10000 !important;
        }

        #cropper-container {
            max-width: 100%;
        }

        #cropper-container .cropper-container {
            position: relative;
            z-index: 1;
            max-height: 400px;
        }

        #image-to-crop {
            max-width: 100%;
            max-height: 350px;
            width: auto;
            height: auto;
        }

        .modal {
            z-index: 1055;
        }

        .cropper-modal {
            z-index: 1 !important;
        }
    </style>
</head>

<body>
    <?php include 'include/offline-handler.php'; ?>
    <div class="d-flex">
        <?php include 'include/sidebar.php'; ?>
        <?php include 'include/modalForSignOut.php'; ?>

        <div class="w-100 d-flex flex-column" style="min-height: 100vh;">
            <?php include 'include/navbar.php'; ?>

            <main class="container-fluid p-4" style="flex: 1;">
                <h2>User Account Verifications</h2>
                <p>Manage and review all user verification submissions.</p>

                <ul class="nav nav-tabs mt-4" id="verificationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" type="button" data-status="pending">
                            Pending <span class="badge bg-primary rounded-pill" id="pending-count">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" type="button" data-status="verified">
                            Verified <span class="badge bg-success rounded-pill" id="verified-count">0</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" type="button" data-status="rejected">
                            Rejected <span class="badge bg-danger rounded-pill" id="rejected-count">0</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead id="table-header"></thead>
                            <tbody id="submissions-list"></tbody>
                        </table>
                    </div>
                    <div id="pagination-controls" class="d-flex justify-content-between align-items-center mt-3"></div>
                </div>
            </main>
            <?php include 'include/footer.php'; ?>
        </div>
    </div>

    <?php include 'include/back-to-top.php'; ?>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Review Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="personal-info mb-4">
                        <h6><i class="bi bi-person-badge"></i> Personal Information</h6>
                        <div class="row">
                            <div class="col-md-6 info-row">
                                <span class="info-label">ID Type:</span>
                                <span class="info-value" id="info-id-type">-</span>
                            </div>
                            <div class="col-md-6 info-row">
                                <span class="info-label">Full Name:</span>
                                <span class="info-value" id="info-full-name">-</span>
                            </div>
                            <div class="col-md-6 info-row">
                                <span class="info-label">Gender:</span>
                                <span class="info-value" id="info-gender">-</span>
                            </div>
                            <div class="col-md-6 info-row">
                                <span class="info-label">Birthday:</span>
                                <span class="info-value" id="info-birthday">-</span>
                            </div>
                            <div class="col-12 info-row">
                                <span class="info-label">Address:</span>
                                <span class="info-value" id="info-address">-</span>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs" id="imageTab" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="front-id-tab" data-bs-toggle="tab" data-bs-target="#front-id-pane" type="button" role="tab">Front ID</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="back-id-tab" data-bs-toggle="tab" data-bs-target="#back-id-pane" type="button" role="tab">Back ID</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="selfie-tab" data-bs-toggle="tab" data-bs-target="#selfie-pane" type="button" role="tab">Live Selfie</button></li>
                    </ul>
                    <div class="tab-content p-3 border border-top-0 rounded-bottom" id="imageTabContent">
                        <div class="tab-pane fade show active text-center" id="front-id-pane" role="tabpanel">
                            <img src="" id="modalFrontImg" class="id-image" style="display: none;">
                            <div class="image-placeholder">
                                <i class="bi bi-file-earmark-image fs-1"></i>
                                <p class="mt-2 mb-0">No Image Uploaded</p>
                            </div>
                        </div>
                        <div class="tab-pane fade text-center" id="back-id-pane" role="tabpanel">
                            <div id="back-id-display">
                                <img src="" id="modalBackImg" class="id-image" style="display: none;">
                                <div class="image-placeholder">
                                    <i class="bi bi-file-earmark-image fs-1"></i>
                                    <p class="mt-2 mb-0">No Image Uploaded</p>
                                </div>
                                <div class="text-center my-3">
                                    <button class="btn btn-info btn-sm" id="scanQrBtn"><i class="bi bi-qr-code-scan"></i> Scan QR Code</button>
                                    <div id="qrResult" class="mt-2"></div>
                                </div>
                            </div>
                            <div id="cropper-container" class="mt-2" style="display: none;">
                                <p class="text-muted small">Drag the box to select the QR code, then click Confirm Crop.</p>
                                <div><img id="image-to-crop"></div>
                                <div class="text-center" id="cropper-actions">
                                    <button class="btn btn-success btn-sm me-2" id="confirm-crop-btn">
                                        <i class="bi bi-check-lg"></i> Confirm Crop & Scan
                                    </button>
                                    <button class="btn btn-secondary btn-sm" id="cancel-crop-btn">
                                        <i class="bi bi-x-lg"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade text-center" id="selfie-pane" role="tabpanel">
                            <img src="" id="modalSelfieImg" class="id-image" style="display: none;">
                            <div class="image-placeholder">
                                <i class="bi bi-camera fs-1"></i>
                                <p class="mt-2 mb-0">No Selfie Uploaded</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-3 mt-3" id="notes-section">
                        <label for="adminNotes" class="form-label"><b>Notes for User</b></label>
                        <textarea class="form-control" id="adminNotes" rows="3" placeholder="If rejecting, please provide a clear reason here."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="rejectBtn"><i class="bi bi-x-circle"></i> Reject</button>
                    <button type="button" class="btn btn-success" id="approveBtn"><i class="bi bi-check-circle"></i> Approve</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" id="messageModalHeader">
                    <h5 class="modal-title" id="messageModalLabel">Notification</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="messageModalBody">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <canvas id="qr-canvas"></canvas>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $(document).ready(function() {
            let cropper = null;
            let currentSubmissionId = null;
            let currentUserEmail = null;
            let currentUserName = null;
            const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
            const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));
            let currentPage = 1;
            let currentStatus = 'pending';
            let currentImagePaths = {
                front: null,
                back: null,
                selfie: null
            };

            function showMessageModal(title, message, isError = false) {
                const modalHeader = $('#messageModalHeader');
                $('#messageModalLabel').text(title);
                $('#messageModalBody').html(message);

                if (isError) {
                    modalHeader.removeClass('bg-success').addClass('bg-danger');
                } else {
                    modalHeader.removeClass('bg-danger').addClass('bg-success');
                }
                messageModal.show();
            }

            function loadStatusCounts() {
                $.getJSON('backend/admin_api.php?action=get_status_counts', function(response) {
                    if (response.status === 'success' && response.counts) {
                        $('#pending-count').text(response.counts.pending || 0);
                        $('#verified-count').text(response.counts.verified || 0);
                        $('#rejected-count').text(response.counts.rejected || 0);
                    }
                }).fail(function() {
                    console.error("Failed to load status counts.");
                });
            }

            function buildPagination(totalItems, limit) {
                const paginationControls = $('#pagination-controls');
                paginationControls.empty();
                if (!totalItems || totalItems <= limit) return;
                const totalPages = Math.ceil(totalItems / limit);
                const startItem = (currentPage - 1) * limit + 1;
                const endItem = Math.min(currentPage * limit, totalItems);
                let html = `
            <div>Showing ${startItem}-${endItem} of ${totalItems}</div>
            <nav>
                <ul class="pagination mb-0">
                    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a></li>
                    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${currentPage + 1}">Next</a></li>
                </ul>
            </nav>
        `;
                paginationControls.html(html);
            }

            function loadSubmissions(status, page = 1) {
                currentStatus = status;
                currentPage = page;
                const tableHeader = $('#table-header');
                const list = $('#submissions-list');
                const pagination = $('#pagination-controls');
                list.html('<tr><td colspan="5" class="text-center">Loading...</td></tr>');
                tableHeader.hide();
                pagination.empty();

                $.ajax({
                    url: `backend/admin_api.php?action=get_submissions&status=${status}&page=${page}`,
                    method: 'GET',
                    dataType: 'json',
                    timeout: 10000,
                    success: function(response) {
                        list.empty();
                        if (!response || response.status !== 'success' || !response.submissions) {
                            list.html(`<tr><td colspan="5" class="text-center alert alert-danger">Invalid server response</td></tr>`);
                            return;
                        }
                        if (response.submissions.length === 0) {
                            list.html(`<tr><td colspan="5" class="text-center text-muted">No ${status} submissions found.</td></tr>`);
                            return;
                        }
                        tableHeader.show();
                        if (status === 'pending') {
                            tableHeader.html(`<tr><th>Full Name</th><th>Email</th><th>Action</th></tr>`);
                        } else {
                            tableHeader.html(`<tr><th>Full Name</th><th>Email</th><th>Processed By</th><th>Date Processed</th><th>Action</th></tr>`);
                        }
                        response.submissions.forEach(sub => {
                            let row = `<tr id="row-${sub.id}">
                        <td>${sub.fullname || 'N/A'}</td>
                        <td>${sub.email || 'N/A'}</td>`;
                            if (status !== 'pending') {
                                row += `<td>${sub.admin_username || 'N/A'}</td>`;
                                row += `<td>${sub.verification_date ? new Date(sub.verification_date).toLocaleString() : 'N/A'}</td>`;
                            }
                            row += `<td><button class="btn btn-sm btn-primary view-btn" data-id="${sub.id}" data-status="${status}"><i class="fas fa-eye"></i></button></td></tr>`;
                            list.append(row);
                        });
                        buildPagination(response.total, 10);
                    },
                    error: function(xhr, status, error) {
                        let errorMessage = 'Failed to load submissions. Please check the console for details.';
                        list.html(`<tr><td colspan="5" class="text-center alert alert-danger">${errorMessage}</td></tr>`);
                        console.error("AJAX Error:", {
                            xhr,
                            status,
                            error
                        });
                    }
                });
            }

            function displayImage(imgSelector, placeholderSelector, path) {
                if (path) {
                    $(imgSelector).attr('src', path).show();
                    $(placeholderSelector).hide();
                } else {
                    $(imgSelector).hide().attr('src', '');
                    $(placeholderSelector).css('display', 'flex');
                }
            }

            function loadSubmissionDetails(submissionId) {
                currentSubmissionId = submissionId;
                $.getJSON(`backend/admin_api.php?action=get_submission_details&id=${submissionId}`, function(response) {
                    if (response.status === 'success') {
                        const data = response.data;
                        $('#info-id-type').text(data.id_type || '-');
                        $('#info-full-name').text(data.full_name || '-');
                        $('#info-gender').text(data.gender || '-');
                        $('#info-birthday').text(data.birthday || '-');
                        $('#info-address').text(`${data.address_barangay}, ${data.address_town_city}, ${data.address_province} ${data.address_postal_code}`);

                        currentImagePaths.front = data.front_image_path ? `backend/view_image.php?path=${encodeURIComponent(data.front_image_path)}` : null;
                        currentImagePaths.back = data.back_image_path ? `backend/view_image.php?path=${encodeURIComponent(data.back_image_path)}` : null;
                        currentImagePaths.selfie = data.selfie_image_path ? `backend/view_image.php?path=${encodeURIComponent(data.selfie_image_path)}` : null;

                        displayImage('#modalFrontImg', '#front-id-pane .image-placeholder', currentImagePaths.front);
                        displayImage('#modalBackImg', '#back-id-pane .image-placeholder', currentImagePaths.back);
                        displayImage('#modalSelfieImg', '#selfie-pane .image-placeholder', currentImagePaths.selfie);

                        currentUserEmail = data.email;
                        currentUserName = data.full_name;
                        $('#adminNotes').val(data.notes || '');
                    } else {
                        showMessageModal('Error', 'Error loading submission details: ' + response.message, true);
                    }
                }).fail(function() {
                    showMessageModal('Server Error', 'Failed to load submission details.', true);
                });
            }

            function updateStatus(status) {
                const notes = $('#adminNotes').val().trim();
                if (status === 'rejected' && notes === '') {
                    showMessageModal('Input Required', 'A note is required when rejecting a submission.', true);
                    $('#adminNotes').focus();
                    return;
                }
                const button = status === 'verified' ? $('#approveBtn') : $('#rejectBtn');
                const originalText = button.html();
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                $.post('backend/admin_api.php?action=update_status', {
                    submission_id: currentSubmissionId,
                    status: status,
                    notes: notes,
                    user_email: currentUserEmail,
                    user_name: currentUserName
                }, function(response) {
                    if (response.status === 'success') {
                        detailsModal.hide();
                        showMessageModal('Success!', `Submission has been <strong>${status}</strong>.`);
                        loadSubmissions(currentStatus, currentPage);
                        loadStatusCounts();
                    } else {
                        showMessageModal('Error', response.message, true);
                    }
                }, 'json').fail(function() {
                    showMessageModal('Server Error', 'An unexpected server error occurred.', true);
                }).always(function() {
                    button.prop('disabled', false).html(originalText);
                });
            }

            function scanWithAPI(blob, resultDiv, button) {
                const formData = new FormData();
                formData.append('file', blob, 'qrcode.jpg');
                resultDiv.html('<span class="text-muted fst-italic">Scanning...</span>');
                fetch('https://api.qrserver.com/v1/read-qr-code/', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        const qrText = data?.[0]?.symbol?.[0]?.data;
                        if (qrText) {
                            showSuccess(qrText, resultDiv, button);
                        } else {
                            showError("Could not detect QR code.", resultDiv, button);
                        }
                    })
                    .catch(error => {
                        console.error("Scan API Error:", error);
                        showError("An error occurred during the scan.", resultDiv, button);
                    });
            }

            function showSuccess(qrText, resultDiv, button) {
                const verificationUrl = `https://verify.philsys.gov.ph/?q=${encodeURIComponent(qrText)}`;
                resultDiv.html(`<div class="alert alert-success"><strong><i class="bi bi-check-circle-fill"></i> QR Code Found!</strong><br><a href="${verificationUrl}" target="_blank" rel="noopener noreferrer" class="alert-link">Click here to verify</a></div>`);
                resetButton(button);
                $('#cropper-container').hide();
                $('#back-id-display').show();
            }

            function showError(message, resultDiv, button) {
                resultDiv.html(`
            <div class="alert alert-danger">
                <strong><i class="bi bi-x-circle-fill"></i> Scan Failed</strong><br>
                ${message}
                <div class="mt-2">
                    <button class="btn btn-warning btn-sm" id="manual-crop-btn">Try Manual Crop</button>
                </div>
            </div>`);
                resetButton(button);
            }

            function resetButton(button) {
                if (button) button.prop('disabled', false).html('<i class="bi bi-qr-code-scan"></i> Scan QR Code');
            }

            $('#verificationTabs button').on('click', function() {
                $('#verificationTabs button').removeClass('active');
                $(this).addClass('active');
                loadSubmissions($(this).data('status'), 1);
            });

            $(document).on('click', '.page-link', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) loadSubmissions(currentStatus, page);
            });

            $(document).on('click', '.view-btn', function() {
                const status = $(this).data('status');
                if (status === 'pending') {
                    $('#approveBtn, #rejectBtn, #notes-section').show();
                } else {
                    $('#approveBtn, #rejectBtn, #notes-section').hide();
                }
                $('#adminNotes').val('');
                loadSubmissionDetails($(this).data('id'));
                $('#qrResult').html('');
                $('#cancel-crop-btn').click();
                const firstTab = new bootstrap.Tab(document.querySelector('#imageTab button[data-bs-target="#front-id-pane"]'));
                firstTab.show();
                detailsModal.show();
            });

            $('#scanQrBtn').on('click', function() {
                const button = $(this);
                const resultDiv = $('#qrResult');
                const imageSrc = currentImagePaths.back;
                if (!imageSrc) {
                    showError("No back image available to scan.", resultDiv, button);
                    return;
                }
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Scanning...');
                resultDiv.html('<span class="text-muted fst-italic">Attempting fast scan...</span>');
                fetch(imageSrc).then(res => res.blob()).then(blob => {
                    scanWithAPI(blob, resultDiv, button);
                }).catch(err => {
                    showError("Could not load image for scanning.", resultDiv, button);
                });
            });

            $(document).on('click', '#manual-crop-btn', function() {
                const imageSrc = currentImagePaths.back;
                if (!imageSrc) return;
                $('#back-id-display').hide();
                $('#cropper-container').show();
                const image = document.getElementById('image-to-crop');
                image.src = imageSrc;

                image.onload = function() {
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(image, {
                        viewMode: 1,
                        autoCropArea: 0.3,
                        background: false,
                        movable: true,
                        zoomable: true,
                        responsive: true,
                        restore: false,
                        guides: true,
                        center: true,
                        highlight: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                        minCropBoxHeight: 50,
                        minCropBoxWidth: 50
                    });
                };
            });

            $(document).on('click', '#cancel-crop-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
                $('#cropper-container').hide();
                $('#back-id-display').show();
                $('#qrResult').html('');
            });

            $(document).on('click', '#confirm-crop-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                if (!cropper) return;
                const resultDiv = $('#qrResult');
                const scanButton = $('#scanQrBtn');
                const button = $(this);
                button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Processing...');

                try {
                    const croppedCanvas = cropper.getCroppedCanvas({
                        width: 400,
                        height: 400,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high'
                    });

                    croppedCanvas.toBlob(function(blob) {
                        if (blob) {
                            scanWithAPI(blob, resultDiv, scanButton);
                        } else {
                            showError("Failed to process cropped image.", resultDiv, scanButton);
                        }
                        button.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Confirm Crop & Scan');
                    }, 'image/jpeg', 0.9);

                } catch (error) {
                    showError("Error processing cropped image.", resultDiv, scanButton);
                    button.prop('disabled', false).html('<i class="bi bi-check-lg"></i> Confirm Crop & Scan');
                }
            });

            $('#approveBtn').on('click', () => updateStatus('verified'));
            $('#rejectBtn').on('click', () => updateStatus('rejected'));

            $('#detailsModal').on('hidden.bs.modal', function() {
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            });

            loadStatusCounts();
            loadSubmissions('pending', 1);

            const specificIdToShow = <?php echo json_encode($specific_sub_id); ?>;
            if (specificIdToShow > 0) {
                loadSubmissionDetails(specificIdToShow);
                $('#approveBtn, #rejectBtn, #notes-section').show();
                $('#adminNotes').val('');
                detailsModal.show();
            }
        });
    </script>
</body>

</html>