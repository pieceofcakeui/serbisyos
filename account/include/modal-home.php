<?php
include './backend/security_helper.php';
?>

<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Sending Message...</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="modalMessage">Please wait...</p>
      </div>
      <div class="modal-footer">
        <button id="modalOkBtn" type="button" class="btn btn-primary" data-bs-dismiss="modal"
          style="display: none;">OK</button>
      </div>
    </div>
  </div>
</div>


<?php
require './backend/db_connection.php';

$showApprovedModal = false;
$showRejectedModal = false;
$showPushNotifModal = false;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT profile_type, push_notif_modal FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {

        if ($user['profile_type'] === 'owner') {

            $stmt = $conn->prepare("SELECT seen_toggle_onboarding
                                    FROM shop_applications
                                    WHERE user_id = ? AND status = 'Approved'
                                    ORDER BY id DESC
                                    LIMIT 1");

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $shop = $result->fetch_assoc();

            if ($shop && $shop['seen_toggle_onboarding'] == 0) {
                $showApprovedModal = true;
                $conn->query("UPDATE shop_applications SET seen_toggle_onboarding = 1 WHERE user_id = $user_id AND status = 'Approved'");
            }

        } else {

            $stmt = $conn->prepare("SELECT seen_rejected_notification
                                    FROM shop_applications
                                    WHERE user_id = ? AND status = 'Rejected'
                                    ORDER BY id DESC
                                    LIMIT 1");

            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $shop = $result->fetch_assoc();

            if ($shop && $shop['seen_rejected_notification'] == 0) {
                $showRejectedModal = true;
                $conn->query("UPDATE shop_applications SET seen_rejected_notification = 1 WHERE user_id = $user_id AND status = 'Rejected'");
            }
        }

        if ($user['push_notif_modal'] == 0 && !$showApprovedModal && !$showRejectedModal) {
            $showPushNotifModal = true;
            $conn->query("UPDATE users SET push_notif_modal = 1 WHERE id = $user_id");
            $_SESSION['push_notif_modal'] = 1;
        }

    }
}
?>

<?php if ($showApprovedModal): ?>
  <div id="onboardingAdModal" class="modal fade" tabindex="-1" aria-labelledby="onboardingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content p-3">
        <div class="modal-body text-center p-2">
          <div class="modal-title mb-3">
            <div class="check-icon mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                <path fill="green" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
              </svg>
            </div>
            <h5 class="mb-3">Application Approved!</h5>
          </div>

          <div class="feature-list text-start mb-3" style="font-size: 0.9rem;">
            <div class="feature-item d-flex align-items-start mb-2">
              <div class="feature-icon me-2">✏️</div>
              <div><strong>Complete Your Shop Profile</strong> – Add details and services.</div>
            </div>
            <div class="feature-item d-flex align-items-start mb-2">
              <div class="feature-icon me-2">📅</div>
              <div><strong>Enable Booking System</strong> – Accept appointments online.</div>
            </div>
            <div class="feature-item d-flex align-items-start mb-2">
              <div class="feature-icon me-2">🚨</div>
              <div><strong>Emergency Settings</strong> – Handle urgent service requests.</div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 d-flex justify-content-center gap-2">
          <a href="manage-shop.php" class="btn btn-success btn-sm px-3">Complete Profile</a>
          <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Do It Later</button>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if ($showRejectedModal): ?>
  <div id="rejectedApplicationModal" class="modal fade" tabindex="-1" aria-labelledby="rejectedModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content p-3 position-relative">
        
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" data-bs-dismiss="modal" aria-label="Close"></button>
        
        <div class="modal-body text-center p-2">
          <div class="modal-title mb-3">
            <div class="warning-icon mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                <path fill="red" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
              </svg>
            </div>
            <h5 class="mb-3">Application Rejected</h5>
          </div>

          <div class="rejection-message text-start mb-3" style="font-size: 0.9rem;">
            <p>We're sorry, your shop application has been rejected. This could be due to:</p>
            <ul class="mb-2 ps-3">
              <li>Incomplete or inaccurate information</li>
              <li>Not meeting our requirements</li>
              <li>Other policy reasons</li>
            </ul>
            <p class="mb-0">You may reapply after addressing the issues. For help, contact our support team.</p>
          </div>
        </div>

        <div class="modal-footer border-0 d-flex justify-content-center gap-2">
          <a href="mailto:support@serbisyos.com" class="btn btn-primary btn-sm">Contact Support</a>
          <a href="<?php echo BASE_URL ?? ''; ?>/account/become-a-partner" class="btn btn-warning btn-sm">Reapply</a>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>


<?php if ($showPushNotifModal): ?>
<div id="pushNotifModal" class="modal fade" tabindex="-1" aria-labelledby="pushNotifModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
    <div class="modal-content p-3">
      <div class="modal-body text-center p-2">
        <div class="modal-title mb-3">
          <div class="check-icon mb-2" style="color: #0d6efd;"> <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-bell-fill" viewBox="0 0 16 16">
              <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zm.995-14.901a1 1 0 1 0-1.99 0A5.002 5.002 0 0 0 3 6c0 1.098-.5 6-2 7h14c-1.5-1-2-5.902-2-7 0-2.42-1.72-4.44-4.005-4.901z"/>
            </svg>
          </div>
          <h5 class="mb-3">Real-time Push Notifications</h5>
        </div>
        <p style="font-size: 0.9rem; margin-bottom: 20px;">Enable browser push notifications to get instant alerts on your device, even when the Serbisyos website is closed.</p>
      </div>
      <div class="modal-footer border-0 d-flex justify-content-center gap-2">
        <a href="<?php echo BASE_URL ?? ''; ?>/account/settings-and-privacy" class="btn btn-primary btn-sm px-3">Enable in Settings</a>
        <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Maybe Later</button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>
<?php
if (isset($conn)) {
  $conn->close();
}
?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    
    <?php if ($showApprovedModal): ?>
      var onboardingModalElement = document.getElementById('onboardingAdModal');
      if (onboardingModalElement) {
        var onboardingModal = new bootstrap.Modal(onboardingModalElement);
        onboardingModal.show();
      }
    <?php endif; ?>

    <?php if ($showRejectedModal): ?>
      var rejectedModalElement = document.getElementById('rejectedApplicationModal');
      if (rejectedModalElement) {
        var rejectedModal = new bootstrap.Modal(rejectedModalElement);
        rejectedModal.show();
      }
    <?php endif; ?>
    
    <?php if ($showPushNotifModal): ?>
      var pushModalElement = document.getElementById('pushNotifModal');
      if (pushModalElement) {
        var pushModal = new bootstrap.Modal(pushModalElement);
        pushModal.show();
      }
    <?php endif; ?>
    });
</script>