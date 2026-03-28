<div class="modal fade" id="rateExperienceModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-3">
            <div class="modal-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="modal-title mb-0 text-center">Rate Your Experience</h5>
                </div>

                <form id="rateExperienceForm">
                    <input type="hidden" id="modal_shop_name" name="shop_name">
                    <input type="hidden" id="shop_id" name="shop_id">
                    <input type="hidden" id="rating" name="rating">

                    <div class="mb-3">
                        <label class="form-label">Select Rating:</label>
                        <div class="star-rating">
                            <i class="rating-star far fa-star" data-rating="1"
                                style="font-size: 20px; cursor: pointer;"></i>
                            <i class="rating-star far fa-star" data-rating="2"
                                style="font-size: 20px; cursor: pointer;"></i>
                            <i class="rating-star far fa-star" data-rating="3"
                                style="font-size: 20px; cursor: pointer;"></i>
                            <i class="rating-star far fa-star" data-rating="4"
                                style="font-size: 20px; cursor: pointer;"></i>
                            <i class="rating-star far fa-star" data-rating="5"
                                style="font-size: 20px; cursor: pointer;"></i>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">Your Review:</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-warning" id="submitReview"
                            style="height: 37px; padding: 0 12px;">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 380px;">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <div class="mb-3">
                    <i id="messageIcon" class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 id="messageTitle" class="mb-3">Success</h5>
                <p id="messageText" class="mb-4 mb-sm-3">Your review has been submitted successfully!</p>
                <button type="button" class="btn btn-primary w-50" data-bs-dismiss="modal"
                    id="messageModalClose">OK</button>
            </div>
        </div>
    </div>
</div>