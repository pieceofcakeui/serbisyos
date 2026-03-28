<?php
require_once '../functions/auth.php';
include 'backend/db_connection.php';
include 'backend/emergency-request.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Emergency Requests</title>
      <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#ffc107">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <link rel="apple-touch-icon" href="../assets/img/favicon.png">
    <link rel="shortcut icon" href="../assets/img/favicon.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/users/styles.css">
    <link rel="stylesheet" href="../assets/css/users/navbar.css">
    <link rel="stylesheet" href="../assets/css/users/emergency-request.css"> </head>

<body>
    <?php include 'include/navbar.php'; ?>
    <?php include 'include/modalForSignOut.php'; ?>
    <?php include 'include/offline-handler.php'; ?>

    <div id="main-content" class="main-content">
        <?php if ($user_profile['profile_type'] === 'owner'): ?>
            <div class="emergency-request-section" id="emergency-section">
                <div class="request-header">
                    <div class="request-category-title"><i class="bi bi-exclamation-triangle"></i><span>Manage Emergency Requests</span></div>
                </div>

                <div class="request-tabs">
                    <div class="request-tab active" data-status="all">All <span class="badge bg-secondary text-white rounded-pill"><?php echo $status_counts['all']; ?></span></div>
                    <div class="request-tab" data-status="pending">Pending <span class="badge bg-warning text-dark rounded-pill"><?php echo $status_counts['pending']; ?></span></div>
                    <div class="request-tab" data-status="accepted">Accepted <span class="badge bg-primary text-white rounded-pill"><?php echo $status_counts['accepted']; ?></span></div>
                    <div class="request-tab" data-status="completed">Completed <span class="badge bg-success text-white rounded-pill"><?php echo $status_counts['completed']; ?></span></div>
                    <div class="request-tab" data-status="rejected">Rejected <span class="badge bg-dark text-white rounded-pill"><?php echo $status_counts['rejected']; ?></span></div>
                    <div class="request-tab" data-status="cancelled">Cancelled <span class="badge bg-danger text-white rounded-pill"><?php echo $status_counts['cancelled']; ?></span></div>
                </div>

                <div class="request-content-wrapper">
                    <div class="emergency-empty-state emergency-empty-state-all" style="display: none;"><i class="fas fa-inbox"></i><h4>No Emergency Requests</h4><p>When a user sends a request, it will appear here.</p></div>
                    <div class="emergency-empty-state emergency-empty-state-pending" style="display: none;"><i class="fas fa-clock"></i><h4>No Pending Requests</h4></div>
                    <div class="emergency-empty-state emergency-empty-state-accepted" style="display: none;"><i class="fas fa-check-circle"></i><h4>No Accepted Requests</h4></div>
                    <div class="emergency-empty-state emergency-empty-state-completed" style="display: none;"><i class="fas fa-flag-checkered"></i><h4>No Completed Requests</h4></div>
                    <div class="emergency-empty-state emergency-empty-state-rejected" style="display: none;"><i class="fas fa-times-circle"></i><h4>No Rejected Requests</h4></div>
                    <div class="emergency-empty-state emergency-empty-state-cancelled" style="display: none;"><i class="fas fa-ban"></i><h4>No Cancelled Requests</h4></div>

                    <?php if ($emergency_result && $emergency_result->num_rows > 0): ?>
                        <div id="emergency-list-container">
                            <?php mysqli_data_seek($emergency_result, 0); ?>
                            <?php while ($request = $emergency_result->fetch_assoc()):

                                $decrypted_lat = decryptData($request['latitude']);
                                $decrypted_long = decryptData($request['longitude']);
                                $decrypted_address = decryptData($request['full_address']);
                                $status = !empty($request['status']) ? strtolower(trim($request['status'])) : 'pending';
                                $profile_pic_path = getProfilePicturePath($request['profile_picture']);
                                $display_text = ucfirst($status);

                                $badge_class = '';
                                switch ($status) {
                                    case 'pending': $badge_class = 'bg-warning text-dark'; break;
                                    case 'accepted': $badge_class = 'bg-primary'; break;
                                    case 'completed': $badge_class = 'bg-success'; break;
                                    case 'rejected': $badge_class = 'bg-secondary'; break;
                                    case 'cancelled': $badge_class = 'bg-danger'; break;
                                    default: $badge_class = 'bg-dark'; break;
                                }
                            ?>
                            
                            <div class="emergency-list-item" data-status="<?php echo $status; ?>">
                                <div class="customer-info">
                                    <img src="<?php echo htmlspecialchars($profile_pic_path); ?>" alt="avatar">
                                    <span><?php echo htmlspecialchars($request['requester_name']); ?></span>
                                </div>
                                <div class="vehicle-info"><?php echo htmlspecialchars($request['vehicle_type'] . ' ' . $request['vehicle_model']); ?></div>
                                <div class="request-date"><?php echo date('M j, Y, g:i A', strtotime($request['created_at'])); ?></div>
                                <div class="status-badge"><span class="badge <?php echo $badge_class; ?>"><?php echo $display_text; ?></span></div>
                                <div class="action-buttons">
                                    <button class="action-btn view-details" title="View Details"
                                        data-id="<?php echo htmlspecialchars($request['id']); ?>"
                                        data-lat="<?php echo htmlspecialchars($decrypted_lat); ?>"
                                        data-long="<?php echo htmlspecialchars($decrypted_long); ?>"
                                        data-address="<?php echo htmlspecialchars($decrypted_address); ?>">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo $decrypted_lat; ?>,<?php echo $decrypted_long; ?>" target="_blank" class="action-btn" title="Directions"><i class="bi bi-geo-alt"></i></a>
                                    
                                    <button class="action-btn message-icon-container" title="Message Customer"
                                        data-user-id="<?php echo $request['user_id']; ?>"
                                        data-customer-name="<?php echo htmlspecialchars($request['requester_name']); ?>"
                                        data-profile-picture="<?php echo htmlspecialchars($profile_pic_path); ?>">
                                        <i class="bi bi-chat-dots"></i>
                                    </button>

                                    <div class="dropdown">
                                        <button class="action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions"><i class="bi bi-three-dots-vertical"></i></button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if ($status === 'pending'): ?>
                                                <li><a class="dropdown-item change-status" href="#" data-status="accepted" data-id="<?php echo htmlspecialchars($request['id']); ?>"><i class="fas fa-check-circle text-success me-2"></i> Accept</a></li>
                                                <li><a class="dropdown-item change-status text-danger" href="#" data-status="rejected" data-id="<?php echo htmlspecialchars($request['id']); ?>"><i class="fas fa-times-circle me-2"></i> Reject</a></li>
                                            <?php elseif ($status === 'accepted'): ?>
                                                <li><a class="dropdown-item change-status" href="#" data-status="completed" data-id="<?php echo htmlspecialchars($request['id']); ?>"><i class="fas fa-flag-checkered text-info me-2"></i> Mark Complete</a></li>
                                            <?php endif; ?>
                                            <?php if (in_array($status, ['completed', 'rejected', 'cancelled'])): ?>
                                                <li><a class="dropdown-item change-status text-danger" href="#" data-status="delete" data-id="<?php echo htmlspecialchars($request['id']); ?>"><i class="fas fa-trash-alt me-2"></i> Delete</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <?php endwhile; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="emergencyModal" tabindex="-1" aria-labelledby="emergencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 800px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="emergencyModalLabel">Emergency Request Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3" style="overflow-y: auto; max-height: 70vh;">
                    <div id="emergencyDetails" class="emergency-details-container"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <h5 id="confirmStatusModalTitle"></h5>
                    <div id="confirmStatusModalBody"></div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" class="btn btn-secondary flex-grow-1" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary flex-grow-1" id="confirmStatusChange" style="height: 39px; padding: 0 10px;">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content message-modal-content">
                <div class="message-modal-header">
                    <div class="message-recipient-info">
                        <img src="" class="message-recipient-avatar" id="messageRecipientAvatar">
                        <h5 id="messageRecipientName"></h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="message-modal-body" id="messageModalBody">
                    <div class="message-container" id="messageContainer"></div>
                </div>
                <div class="message-modal-footer">
                    <div class="message-input-container">
                        <label for="messageAttachment" class="message-attachment-btn"><i class="fas fa-paperclip"></i></label>
                        <input type="file" id="messageAttachment" accept="image/*" style="display: none;">
                        <input type="text" class="message-input" id="messageInput" placeholder="Type a message...">
                        <button class="message-send-btn" id="messageSendBtn"><i class="fas fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100"></div>

    <?php include 'include/emergency-modal.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/emergency-request.js"></script>
    <script src="../assets/js/navbar.js"></script>
    <script src="../assets/js/emergency-request-message.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVb7yD7Ea-WHFxelMsDJAfG1j2mLBSMsE&libraries=places" async defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.request-tab');
            const listContainer = document.getElementById('emergency-list-container');

            function filterEmergencyRequests(status) {
                const listItems = listContainer ? listContainer.querySelectorAll('.emergency-list-item') : [];
                const emptyStates = document.querySelectorAll('.emergency-empty-state');
                let hasVisibleItems = false;

                emptyStates.forEach(state => state.style.display = 'none');

                listItems.forEach(item => {
                    const isVisible = (status === 'all' || item.dataset.status === status);
                    item.style.display = isVisible ? 'grid' : 'none';
                    if (isVisible) hasVisibleItems = true;
                });

                if (!hasVisibleItems) {
                    const emptyStateToShow = document.querySelector(`.emergency-empty-state-${status}`);
                    if(emptyStateToShow) emptyStateToShow.style.display = 'block';
                }
            }

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    tabs.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    filterEmergencyRequests(this.dataset.status);
                });
            });

            const activeTab = document.querySelector('.request-tab.active');
            if(activeTab) {
                filterEmergencyRequests(activeTab.dataset.status);
            }

            document.body.addEventListener('click', function(e) {
                const messageTrigger = e.target.closest('.message-icon-container');
                if (messageTrigger) {
                    e.preventDefault();
                    const userId = messageTrigger.dataset.userId;
                    const customerName = messageTrigger.dataset.customerName;
                    const profilePicture = messageTrigger.dataset.profilePicture;
                    openMessageWindow(userId, customerName, profilePicture);
                }
            });
        });
    </script>
</body>
</html>