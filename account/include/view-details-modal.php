<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Gallery Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</div>


<div id="floatingImageViewer" class="d-none position-fixed top-0 start-0 w-100 h-100"
    style="z-index: 1060; background-color: rgba(0, 0, 0, 0.9);">
    <div class="position-absolute d-flex align-items-center gap-2" style="top: 20px; right: 20px; z-index: 1070;">
        <button id="closeFloatingViewer" class="btn rounded-circle p-0 d-flex align-items-center justify-content-center"
            style="width: 40px; height: 40px; background-color: rgba(255, 255, 255, 0.2); color: white; border: none; font-weight: bold;">
            &times;
        </button>
    </div>
    <div class="d-flex justify-content-center align-items-center h-100 position-relative">
        <img id="floatingViewImage" src="" class="img-fluid"
            style="max-height: 90vh; max-width: 90vw; object-fit: contain;">
    </div>
</div>


<div class="modal fade" id="writeReview" tabindex="-1" aria-labelledby="writeReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fs-5 fw-bold" id="writeReviewLabel">Write a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-3 pt-0">
                <form id="writeReviewForm">
                    <input type="hidden" id="modal_shop_name" name="shop_name">
                    <input type="hidden" id="shop_id" name="shop_id">
                    <input type="hidden" id="reviewer_name" name="reviewer_name">
                    <input type="hidden" id="rating" name="rating" value="0">
                    <div class="mb-3">
                        <label class="form-label fw-medium">Select Rating:</label>
                        <div class="star-rating mb-2">
                            <i class="rating-star far fa-star" data-rating="1"></i>
                            <i class="rating-star far fa-star" data-rating="2"></i>
                            <i class="rating-star far fa-star" data-rating="3"></i>
                            <i class="rating-star far fa-star" data-rating="4"></i>
                            <i class="rating-star far fa-star" data-rating="5"></i>
                        </div>
                        <div id="rating-feedback" class="text-muted small">Current rating: <span id="current-rating" class="fw-medium">0</span>/5</div>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label fw-medium">Your Review:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required placeholder="Share your experience..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-top-0 pt-0 px-3 pb-3">
                <button type="button" class="btn btn-primary px-3" id="submitReview">Submit Review</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="emergencyClosedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <h5 class="text-danger mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Shop Closed</h5>
                <p>We're sorry, but we're closed for emergency services.</p>
                <p>Please try another shop that offers emergency assistance 24/7.</p>

                <?php if (!empty($emergency_hours_array)): ?>
                    <div class="mt-4 text-start">
                        <h6>Emergency Service Hours:</h6>
                        <ul class="list-group">
                            <?php foreach ($emergency_hours_array as $time_slot): ?>
                                <?php
                                $formatted_time_slot = $time_slot;
                                $parts = explode(', ', $time_slot, 2);
                                if (count($parts) === 2) {
                                    $day = trim($parts[0]);
                                    $time_range = trim($parts[1]);
                                    $times = explode(' - ', $time_range, 2);
                                    if (count($times) === 2) {
                                        $start_time = trim($times[0]);
                                        $end_time = trim($times[1]);
                                        $has_ampm = (stripos($time_range, 'AM') !== false || stripos($time_range, 'PM') !== false);
                                        if (!$has_ampm) {
                                            $today = date('Y-m-d');
                                            $start_timestamp = strtotime($today . ' ' . $start_time);
                                            $end_timestamp = strtotime($today . ' ' . $end_time);
                                            if ($start_timestamp !== false && $end_timestamp !== false) {
                                                $start_time_formatted = date('g:i A', $start_timestamp);
                                                $end_time_formatted = date('g:i A', $end_timestamp);
                                                $formatted_time_slot = $day . ', ' . $start_time_formatted . ' - ' . $end_time_formatted;
                                            }
                                        }
                                    }
                                }
                                ?>
                                <li class="list-group-item text-center"><?php echo htmlspecialchars($formatted_time_slot); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="mt-4 d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="<?php echo BASE_URL; ?>/account/emergency-provider?service=Emergency" class="btn btn-primary" style="height: 40px; padding: 10 10px;">Find Other Shops</a>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    function showEmergencyClosedModal() {
        const modal = new bootstrap.Modal(document.getElementById('emergencyClosedModal'));
        modal.show();
    }
</script>

<div class="modal fade" id="ownerRestrictionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle text-warning fa-2x mb-3"></i>
                <p id="restrictionMessage" class="mb-3"></p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showOwnerRestrictionModal(actionType) {
        const modal = new bootstrap.Modal(document.getElementById('ownerRestrictionModal'));
        const messageEl = document.getElementById('restrictionMessage');

        if (actionType === 'book') {
            messageEl.textContent = 'You cannot book an appointment for your own shop. This feature is for customers only.';
        } else {
            messageEl.textContent = 'You cannot request emergency service for your own shop. This feature is for customers only.';
        }

        modal.show();
    }
</script>



<div class="modal fade" id="generalOwnerRestrictionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-3" style="max-width: 400px; margin: auto;">
            <div class="modal-body text-center">
                <i class="fas fa-exclamation-circle text-danger fa-2x mb-3"></i>
                <p id="generalRestrictionMessage" class="mb-3"></p>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showGeneralOwnerRestrictionModal(actionType) {
        const modal = new bootstrap.Modal(document.getElementById('generalOwnerRestrictionModal'));
        const messageEl = document.getElementById('generalRestrictionMessage');

        if (actionType === 'book') {
            messageEl.textContent = 'As a shop owner, you are not permitted to book appointments. This feature is intended for customers only.';
        } else {
            messageEl.textContent = 'As a shop owner, you are not permitted to request emergency services. This feature is intended for customers only.';
        }

        modal.show();
    }
</script>


<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 450px;">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header" style="border-bottom: 1px solid #dee2e6;">
                <h5 class="modal-title" id="scheduleModalLabel" style="font-weight: 600;">
                    <i class="fas fa-calendar-alt me-2" style="text-align: center;"></i> Shop Schedule
                </h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close"
                    style="background: none; border: none; font-size: 1.5rem; color: #555; cursor: pointer; line-height: 1;">
                    &times;
                </button>
            </div>

            <div class="modal-body" style="padding: 1rem 1.2rem;">
                <ul class="list-group list-group-flush">
                <?php
                if (isset($schedule) && is_array($schedule)):
                    foreach ($schedule as $day => $times):
                        $is_today_in_loop = ($day === $current_day);
                        $formatted_time = '<span class="text-danger">Closed</span>';
                        if (!empty($times['open_am'])) {
                            $formatted_open_am = date('g:i A', strtotime($times['open_am']));
                            $formatted_close_am = date('g:i A', strtotime($times['close_am']));
                            $formatted_time = htmlspecialchars($formatted_open_am) . " - " . htmlspecialchars($formatted_close_am);

                            if (!empty($times['open_pm'])) {
                                $formatted_open_pm = date('g:i A', strtotime($times['open_pm']));
                                $formatted_close_pm = date('g:i A', strtotime($times['close_pm']));
                                $formatted_time .= " / " . htmlspecialchars($formatted_open_pm) . " - " . htmlspecialchars($formatted_close_pm);
                            }
                        }
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center <?= $is_today_in_loop ? 'bg-light fw-bold' : '' ?>" 
                        style="padding-left: 0.5rem; padding-right: 0.5rem;">
                        <span style="min-width: 80px;"><?= htmlspecialchars($day) ?></span>
                        <span class="text-end">
                            <?= $formatted_time ?>
                            <?php if ($is_today_in_loop && $shop_status == 'open'): ?>
                                <span class="badge <?= $is_open ? 'bg-success' : 'bg-danger' ?> ms-2">
                                    <?= $is_open ? 'Open Now' : 'Closed' ?>
                                </span>
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php endforeach;
                else: ?>
                    <li class="list-group-item text-center text-muted">Schedule information unavailable.</li>
                <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>