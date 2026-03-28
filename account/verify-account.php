<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: ../home");
    exit();
}

$userId = $_SESSION['user_id'];

$userQuery = $conn->prepare("SELECT profile_type FROM users WHERE id = ?");
$userQuery->bind_param("i", $userId);
$userQuery->execute();
$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    session_destroy();
    header("Location: ../home");
    exit();
}

$user = $userResult->fetch_assoc();

if ($user['profile_type'] !== 'user') {
    header("Location: error.php?code=403");
    exit();
}

$submissionStatus = null;

$stmt = $conn->prepare("SELECT status, submission_date, notes FROM verification_submissions WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $submissionStatus = $result->fetch_assoc();
}
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Account</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .verify-account-section {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 15px;
            width: 100%;
            min-height: calc(100vh - 76px);
        }

        .main-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
        }

        .header {
            color: #1a1a1a;
            padding: 30px;
            text-align: center;
            margin-bottom: -60px;
        }

        .header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 600;
        }

        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1em;
        }

        .container {
            padding: 30px;
        }

        .section-title {
            color: #2c3e50;
            margin: 0 0 20px 0;
            font-size: 1.4em;
            font-weight: 600;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }

        #uploaderFront,
        #uploaderBack {
            margin-bottom: 15px;
            padding: 30px 15px;
            border: 2px dashed #bdc3c7;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .image-preview-container {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }

        .submit-btn,
        .next-btn {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
        }

        .philsys-upload-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        #webcam-feed {
            width: 100%;
            max-width: 400px;
            border-radius: 8px;
            border: 2px solid #ddd;
            background-color: #333;
        }

        #selfie-preview {
            max-width: 100%;
            max-height: 250px;
            border-radius: 8px;
            border: 2px solid #28a745;
        }

        #selfie-canvas {
            display: none;
        }

        .submit-btn:disabled,
        .next-btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .status-box {
            padding: 2rem;
            text-align: center;
            border-radius: 8px;
        }

        .status-box .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .status-box h4 {
            font-weight: 600;
        }

        #processingModal .progress-bar {
            transition: width 0.3s ease-in-out;
        }

        #processingModal .percentage-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
        }

        #processingModal .status-text {
            font-size: 1rem;
            color: #555;
        }

        @media (max-width: 768px) {
            .philsys-upload-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="bg-light-subtle">
    <div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100;"></div>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <div class="header">
            <h1>Verify Your Account</h1>
            <p>
                <?php echo ($submissionStatus && $submissionStatus['status'] !== 'rejected') ? "Check the status of your submission below." : "Verify your personal information to finish setting up your account.<br>This will allow you to book services in advance and write a review from shops."; ?>
            </p>
        </div>
        <div class="verify-account-section">
            <div class="main-container">
                <div class="container" id="stepsContainer">

                    <?php
                    if ($submissionStatus && $submissionStatus['status'] !== 'rejected'): ?>

                        <h3 class="section-title">Submission Status</h3>

                        <?php if ($submissionStatus['status'] === 'pending'): ?>
                            <div class="status-box bg-warning-subtle text-warning-emphasis">
                                <div class="icon"><i class="bi bi-hourglass-split"></i></div>
                                <h4>Under Review</h4>
                                <p class="lead">Your ID has been submitted and is currently under review.</p>
                                <p>Submitted on: <?php echo date("F j, Y, g:i a", strtotime($submissionStatus['submission_date'])); ?></p>
                            </div>
                        <?php elseif ($submissionStatus['status'] === 'verified'): ?>
                            <div class="status-box bg-success-subtle text-success-emphasis">
                                <div class="icon"><i class="bi bi-patch-check-fill"></i></div>
                                <h4>Account Verified!</h4>
                                <p class="lead">Congratulations! Your account has been successfully verified.</p>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>

                        <?php if ($submissionStatus && $submissionStatus['status'] === 'rejected'): ?>
                            <div class="alert alert-danger">
                                <h4>Your Previous Submission Was Rejected</h4>
                                <?php if (!empty($submissionStatus['notes'])): ?>
                                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($submissionStatus['notes']); ?></p>
                                <?php endif; ?>
                                <p class="mb-0">Please try again by following the steps below.</p>
                            </div>
                        <?php endif; ?>

                        <form id="verificationForm" enctype="multipart/form-data">
                            <div id="step-1-container">
                                <h3 class="section-title">Step 1: Personal Information</h3>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="id_type" class="form-label">Type of ID to Submit</label>
                                        <select id="id_type" name="id_type" class="form-select" required>
                                            <option value="" selected disabled>Choose...</option>
                                            <option value="National ID (PhilSys)">National ID (PhilSys)</option>
                                            <option value="Driver's License">Driver's License</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="full_name" class="form-label">Full Name (as it appears on your ID)</label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select id="gender" name="gender" class="form-select" required>
                                            <option value="" selected disabled>Choose...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Prefer not to say">Prefer not to say</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="birthday" class="form-label">Birthday</label>
                                        <input type="date" class="form-control" id="birthday" name="birthday" required>
                                    </div>
                                    <div class="col-12 mt-4">
                                        <label class="form-label">Permanent Address</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="address_barangay" name="address_barangay" placeholder="Barangay" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="address_town_city" name="address_town_city" placeholder="City" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="address_province" name="address_province" placeholder="Province" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" id="address_postal_code" name="address_postal_code" placeholder="Postal Code" required>
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="button" class="btn btn-primary px-5" id="next-to-step-2-btn">Next</button>
                                </div>
                            </div>

                            <div id="step-2-container" style="display: none;">
                                <h3 class="section-title" id="id-upload-title">Step 2: Upload ID Photos</h3>
                                <div class="philsys-upload-container">
                                    <div>
                                        <div id="uploaderFront">
                                            <div class="upload-text"><strong>Upload Front Side</strong></div>
                                        </div>
                                        <div class="image-preview-container" id="previewContainerFront" style="display:none;"><img src="" alt="Front Preview" class="preview-image" id="previewImageFront"></div>
                                        <input type="file" id="fileInputFront" name="frontImage" accept="image/*" style="display: none;" required>
                                    </div>
                                    <div>
                                        <div id="uploaderBack">
                                            <div class="upload-text"><strong>Upload Back Side</strong></div>
                                        </div>
                                        <div class="image-preview-container" id="previewContainerBack" style="display:none;"><img src="" alt="Back Preview" class="preview-image" id="previewImageBack"></div>
                                        <input type="file" id="fileInputBack" name="backImage" accept="image/*" style="display: none;" required>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary px-5" id="back-to-step-1-btn">Back</button>
                                    <button type="button" class="btn btn-primary px-5" id="next-to-step-3-btn" disabled>Next</button>
                                </div>
                            </div>

                            <div id="step-3-container" style="display: none;">
                                <h3 class="section-title">Step 3: Take a Live Selfie</h3>
                                <div class="selfie-container text-center">
                                    <p class="text-muted">Allow camera access, position your face in the frame, and take a clear photo.</p>
                                    <div id="selfie-capture-box">
                                        <video id="webcam-feed" autoplay playsinline muted></video>
                                        <canvas id="selfie-canvas"></canvas>
                                    </div>
                                    <div id="selfie-preview-box" style="display:none;">
                                        <img id="selfie-preview" src="" alt="Selfie Preview">
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-secondary" id="start-camera-btn"><i class="bi bi-camera-video"></i> Start Camera</button>
                                        <button type="button" class="btn btn-primary" id="capture-btn" style="display:none;"><i class="bi bi-camera-fill"></i> Take Photo</button>
                                        <button type="button" class="btn btn-link" id="retake-btn" style="display:none;">Retake Photo</button>
                                    </div>
                                </div>
                                <div class="d-flex mt-4 gap-3">
                                    <button type="button" class="btn btn-secondary flex-fill" id="back-to-step-2-btn">Back</button>
                                    <button type="submit" class="submit-btn flex-fill" id="submitBtn" disabled>Submit</button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

   <div class="modal fade" id="processingModal" tabindex="-1" aria-labelledby="processingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="processingModalLabel">Submitting Verification</h5>
            </div>

            <div class="modal-body text-center">
                <p class="status-text mb-1" id="processingStatusText">
                    Please wait, we are processing your submission...
                </p>

                <p class="text-muted mb-2" style="font-size: 14px;">
                    This may take 2–3 minutes.
                </p>

                <div class="progress mb-2" style="height: 25px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                         id="processingProgressBar" role="progressbar"
                         style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="percentage-text" id="processingPercentage">0%</div>
            </div>

            <div class="modal-footer border-0" id="processingModalFooter" style="display: none;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <?php include 'include/toast.php'; ?>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/navbar.js"></script>

    <script>
        $(document).ready(function() {
            let selfieBlob = null;
            const video = document.getElementById('webcam-feed');
            const canvas = document.getElementById('selfie-canvas');
            const preview = document.getElementById('selfie-preview');

            $('#next-to-step-2-btn').on('click', function() {
                let isValid = true;
                $('#step-1-container input, #step-1-container select').each(function() {
                    if (!this.checkValidity()) {
                        isValid = false;
                        $(this).addClass('is-invalid');
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                if (isValid) {
                    const idType = $('#id_type').val();
                    $('#id-upload-title').text(`Step 2: Upload ${idType} Photos`);
                    $('.header').hide();
                    $('#step-1-container').hide();
                    $('#step-2-container').show();
                } else {
                    toastr.warning('Please fill out all fields correctly.');
                }
            });

            $('#back-to-step-1-btn').on('click', function() {
                $('.header').show();
                $('#step-2-container').hide();
                $('#step-1-container').show();
            });

            $('#next-to-step-3-btn').on('click', function() {
                $('#step-2-container').hide();
                $('#step-3-container').show();
                if (!video.srcObject) {
                    $('#start-camera-btn').click();
                }
            });

            $('#back-to-step-2-btn').on('click', function() {
                $('#step-3-container').hide();
                $('#step-2-container').show();
            });

            function checkStep2Completion() {
                const frontFile = $('#fileInputFront')[0].files.length > 0;
                const backFile = $('#fileInputBack')[0].files.length > 0;
                $('#next-to-step-3-btn').prop('disabled', !(frontFile && backFile));
            }

            function setupUploader(uploaderId, inputId, previewContainerId, previewImgId) {
                $('#' + uploaderId).on('click', function() {
                    $('#' + inputId).click();
                });
                $('#' + inputId).on('change', function(e) {
                    if (e.target.files.length > 0) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            $('#' + previewImgId).attr('src', event.target.result);
                            $('#' + uploaderId).hide();
                            $('#' + previewContainerId).show();
                            checkStep2Completion();
                        }
                        reader.readAsDataURL(e.target.files[0]);
                    }
                });
            }
            setupUploader('uploaderFront', 'fileInputFront', 'previewContainerFront', 'previewImageFront');
            setupUploader('uploaderBack', 'fileInputBack', 'previewContainerBack', 'previewImageBack');

            $('#start-camera-btn').on('click', function() {
                $(this).prop('disabled', true).text('Starting Camera...');
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'user'
                        }
                    })
                    .then(stream => {
                        video.srcObject = stream;
                        $('#capture-btn').show();
                        $(this).hide();
                    })
                    .catch(err => {
                        toastr.error("Camera access denied. Please allow camera access in your browser settings.");
                        $(this).prop('disabled', false).text('Start Camera');
                    });
            });

            $('#capture-btn').on('click', function() {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                preview.src = canvas.toDataURL('image/jpeg');
                video.srcObject.getTracks().forEach(track => track.stop());

                canvas.toBlob(blob => {
                    selfieBlob = blob;
                    $('#submitBtn').prop('disabled', false);
                }, 'image/jpeg', 0.9);

                $('#selfie-capture-box').hide();
                $('#selfie-preview-box').show();
                $(this).hide();
                $('#retake-btn').show();
            });

            $('#retake-btn').on('click', function() {
                selfieBlob = null;
                $('#submitBtn').prop('disabled', true);
                $('#selfie-preview-box').hide();
                $('#selfie-capture-box').show();
                $(this).hide();
                $('#start-camera-btn').show().prop('disabled', false).text('Start Camera').click();
            });

            $('#verificationForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                formData.append('frontImage', $('#fileInputFront')[0].files[0]);
                formData.append('backImage', $('#fileInputBack')[0].files[0]);

                if (selfieBlob) {
                    formData.append('selfieImage', selfieBlob, 'selfie.jpg');
                } else {
                    toastr.error('A selfie is required to submit.');
                    return;
                }

                const submitBtn = $('#submitBtn');
                const processingModalEl = document.getElementById('processingModal');
                const processingModal = new bootstrap.Modal(processingModalEl);
                const progressBar = document.getElementById('processingProgressBar');
                const percentageText = document.getElementById('processingPercentage');
                const statusText = document.getElementById('processingStatusText');
                const modalFooter = document.getElementById('processingModalFooter');

                progressBar.style.width = '0%';
                progressBar.classList.remove('bg-success', 'bg-danger');
                progressBar.classList.add('bg-warning');
                percentageText.textContent = '0%';
                statusText.textContent = 'Please wait, we are processing your submission...';
                modalFooter.style.display = 'none';
                submitBtn.prop('disabled', true);
                processingModal.show();

                let progress = 0;
                let fakeProgressInterval = setInterval(() => {
                    if (progress < 95) {
                        progress += Math.floor(Math.random() * 5) + 1;
                        progress = Math.min(progress, 95);
                        progressBar.style.width = `${progress}%`;
                        percentageText.textContent = `${progress}%`;
                    }
                }, 250);

                $.ajax({
                    url: 'submit_for_verification.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        clearInterval(fakeProgressInterval);
                        progressBar.style.width = '100%';
                        percentageText.textContent = '100%';

                        if (response.status === 'success') {
                            progressBar.classList.remove('bg-warning');
                            progressBar.classList.add('bg-success');
                            statusText.textContent = 'Submission Successful! Reloading...';
                            toastr.success(response.message);
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            progressBar.classList.remove('bg-warning');
                            progressBar.classList.add('bg-danger');
                            statusText.textContent = `Error: ${response.message || 'An unknown error occurred.'}`;
                            modalFooter.style.display = 'block';
                            submitBtn.prop('disabled', false).text('Submit');
                            toastr.error(response.message || 'An unknown error occurred.');
                            processingModal.hide();
                        }
                    },
                    error: function() {
                        clearInterval(fakeProgressInterval);
                        progressBar.style.width = '100%';
                        percentageText.textContent = 'Error';
                        progressBar.classList.remove('bg-warning');
                        progressBar.classList.add('bg-danger');
                        statusText.textContent = 'A server error occurred. Please try again.';
                        modalFooter.style.display = 'block';
                        submitBtn.prop('disabled', false).text('Submit');
                        toastr.error('A server error occurred. Please try again later.');
                        processingModal.hide();
                    }
                });
            });
        });
    </script>
</body>

</html>