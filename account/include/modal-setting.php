<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative border-0">
            <button type="button"
                class="btn-close position-absolute"
                style="top: 10px; right: 10px; border-radius: 50%; padding: 8px; z-index: 1051;"
                data-bs-dismiss="modal"
                aria-label="Close"></button>

            <form id="deleteAccountForm" action="./backend/delete_account.php" method="POST">
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle text-black fa-2x mb-3"></i>
                    <h5 class="text-black mb-3">Confirm Account Deletion</h5>

                    <p>Are you sure you want to delete your account? This action is permanent and will:</p>
                    <ul class="text-start">
                        <li>Delete all your personal information</li>
                        <li>Remove your service history</li>
                        <li>Cancel any pending appointments</li>
                    </ul>
                    <p class="fw-bold text-danger">This cannot be undone.</p>

                    <div class="mt-4 text-start">
                        <label for="deleteVerification" class="form-label">
                            To verify, please type <strong>DELETE MY ACCOUNT</strong> below:
                        </label>
                        <input type="text" id="deleteVerification" name="deleteVerification" class="form-control" required>
                        <div id="verificationError" class="text-danger mt-2" style="display:none;">
                            Text does not match. Please type exactly as shown.
                        </div>
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    </div>

                    <button id="confirmDeleteBtn" type="submit" class="btn btn-danger mt-4" disabled>
                        <i class="fas fa-trash me-2" style="color: #fff;"></i>Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="setPasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body position-relative px-4 py-4">

                <button type="button" class="btn-close position-absolute"
                    style="top: 10px; right: 10px; outline: none; box-shadow: none;" data-bs-dismiss="modal"
                    aria-label="Close">
                </button>


                <div class="text-center mb-4">
                    <i class="fas fa-lock fa-3x mb-3 text-secondary"></i>
                    <p class="text-muted mb-0">Create a secure password for your account</p>
                </div>

                <form id="setPasswordForm" method="POST">
                    <div class="mb-4">
                        <label class="form-label">New Password</label>
                        <div class="password-input-group position-relative">
                            <input type="password" name="new_password" id="modalNewPassword" class="form-control pe-5"
                                required>
                            <button type="button"
                                class="password-toggle position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent"
                                id="modalToggleNewPassword">
                                <i class="far fa-eye text-dark"></i>
                            </button>
                        </div>
                        <div class="password-strength mt-2" id="modalPasswordStrength"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Confirm Password</label>
                        <div class="password-input-group position-relative">
                            <input type="password" name="confirm_password" id="modalConfirmPassword"
                                class="form-control pe-5" required>
                            <button type="button"
                                class="password-toggle position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent"
                                id="modalToggleConfirmPassword">
                                <i class="far fa-eye text-dark"></i>
                            </button>
                        </div>
                        <div class="password-match mt-2" id="modalPasswordMatch"></div>
                    </div>

                    <div class="text-center">
                        <button type="submit" name="set_password" class="btn btn-primary btn-lg">
                            <i class="fas fa-key me-2"></i> Set Password
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<!-- Data Request Success Modal -->
<div class="modal fade" id="dataRequestSuccessModal" tabindex="-1" aria-labelledby="dataRequestSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-sm border-0">
            <div class="modal-body text-center px-3 py-4">

                <div class="mb-3">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#d1f7d6"/>
                        <path d="M9 12.5l2 2 4-4" stroke="#28a745" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h6 class="text-success fw-semibold mb-2">Data Request Completed</h6>

                <p class="text-muted small mb-4">
                    Your data has been processed successfully and is ready for download. Check it in the "Your Data Requests" section below.
                </p>

                <button type="button" class="btn btn-sm btn-success rounded-pill px-4" data-bs-dismiss="modal">
                    Got it
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Data Request Error Modal -->
<div class="modal fade" id="dataRequestErrorModal" tabindex="-1" aria-labelledby="dataRequestErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-sm border-0">
            <div class="modal-body text-center px-3 py-4">

                <div class="mb-3">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#fddede"/>
                        <line x1="15" y1="9" x2="9" y2="15" stroke="#dc3545" stroke-width="2" stroke-linecap="round"/>
                        <line x1="9" y1="9" x2="15" y2="15" stroke="#dc3545" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>

                <h6 class="text-danger fw-semibold mb-2">Request Failed</h6>

                <p class="text-muted small mb-4">
                    There was an error processing your data request. Please try again. If the problem persists, contact support.
                </p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="button" class="btn btn-sm btn-warning rounded-pill px-3"
                        data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#requestDataModal">
                        🔄 Try Again
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>


<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-sm">
            <div class="modal-body text-center px-3 py-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 1.8rem;"></i>
                </div>
                <h6 class="fw-semibold mb-2">Delete Request?</h6>
                <p class="text-muted small mb-3">This action can't be undone. Are you sure?</p>

                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal" style="height: 40px; padding: 0 10px;">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm rounded-pill px-3" id="confirmDeleteRequest" style="height: 40px; padding: 0 10px;">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Request Deleted Modal -->
<div class="modal fade" id="requestDeletedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 shadow-sm border-0">
            <div class="modal-body text-center px-3 py-4">

                <div class="mb-3">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" fill="#d1f7d6"/>
                        <path d="M9.5 12.5l2 2 4-4" stroke="#28a745" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <h6 class="mb-3 text-success fw-semibold">Request Deleted</h6>

                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-4" data-bs-dismiss="modal">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>



<!-- Logout Device Confirmation Modal -->
<div class="modal fade" id="logoutDeviceModal" tabindex="-1" aria-labelledby="logoutDeviceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body position-relative px-4 pt-4">

                <p class="text-center fw-bold">Are you sure you want to log out this device?</p>
                <p class="text-center small text-muted">This will immediately terminate the session on the selected
                    device.</p>

                <div class="d-flex justify-content-center gap-2 mt-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmLogoutDevice" style="height: 39px; padding: 0 10px;">Log Out</button>
                </div>

            </div>
        </div>
    </div>
</div>