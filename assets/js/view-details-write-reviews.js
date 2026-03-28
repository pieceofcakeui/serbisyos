document.addEventListener('DOMContentLoaded', function() {
    initReviewSystem();
});

function initReviewSystem() {
    const writeReviewModalEl = document.getElementById('writeReview');
    if (!writeReviewModalEl) return;

    // Use Bootstrap's 'show.bs.modal' event to populate data just before the modal opens.
    // This is the correct way to handle modal data without conflicting with other scripts.
    writeReviewModalEl.addEventListener('show.bs.modal', function(event) {
        // 'event.relatedTarget' is the button that triggered the modal
        const button = event.relatedTarget;
        if (!button) return; // Exit if modal was opened without a button click

        // Get data from the button's data-* attributes
        const shopName = button.getAttribute('data-shop-name');
        const shopId = button.getAttribute('data-shop-id');

        // Populate the modal's form fields
        document.getElementById('modal_shop_name').value = shopName;
        document.getElementById('shop_id').value = shopId;
        document.getElementById('reviewer_name').value = window.currentUser?.fullname || '';
    });

    initStarRating();
    initReviewForm();
}

function initStarRating() {
    const ratingStars = document.querySelectorAll('.rating-star');
    const currentRatingSpan = document.getElementById('current-rating');
    const ratingInput = document.getElementById('rating');

    if (!ratingStars.length || !currentRatingSpan || !ratingInput) return;

    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const ratingValue = parseInt(this.dataset.rating);
            ratingInput.value = ratingValue;
            updateStarRating(ratingValue);
        });

        star.addEventListener('mouseover', function() {
            const hoverRating = parseInt(this.dataset.rating);
            updateStarRating(hoverRating, true);
        });

        star.addEventListener('mouseout', function() {
            const currentSelectedRating = parseInt(ratingInput.value);
            updateStarRating(currentSelectedRating);
        });
    });

    // Reset to 0 stars when the modal is prepared
    updateStarRating(0);
}

function updateStarRating(selectedRating, isHover = false) {
    const ratingStars = document.querySelectorAll('.rating-star');
    const currentRatingSpan = document.getElementById('current-rating');

    ratingStars.forEach(star => {
        const starRating = parseInt(star.dataset.rating);
        if (starRating <= selectedRating) {
            star.classList.remove('far');
            star.classList.add('fas');
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
        }
    });

    if (!isHover && currentRatingSpan) {
        currentRatingSpan.textContent = selectedRating || '0';
    }
}

function initReviewForm() {
    const submitButton = document.getElementById('submitReview');
    const modalElement = document.getElementById('writeReview');

    if (!submitButton || !modalElement) return;

    submitButton.addEventListener('click', function() {
        const form = document.getElementById('writeReviewForm');
        if (!form) return;

        if (form.checkValidity() && document.getElementById('rating').value > 0) {
            submitReviewForm(form);
        } else {
            if (document.getElementById('rating').value == 0) {
                toastr.warning('Please select a star rating.');
            }
            form.classList.add('was-validated');
        }
    });

    modalElement.addEventListener('hidden.bs.modal', function() {
        resetReviewForm();
    });
}

function submitReviewForm(form) {
    const formData = new FormData(form);

    fetch(`${BASE_URL}/account/backend/submit_review.php`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success('Review submitted successfully!');
                const writeReviewModal = bootstrap.Modal.getInstance(document.getElementById('writeReview'));
                writeReviewModal.hide();
                // Optionally, reload the page to show the new review
                setTimeout(() => location.reload(), 1500);
            } else {
                toastr.error('Error submitting review: ' + (data.message || 'An unknown error occurred.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            toastr.error('An unexpected error occurred while submitting your review.');
        });
}

function resetReviewForm() {
    const form = document.getElementById('writeReviewForm');
    if (!form) return;

    form.reset();
    form.classList.remove('was-validated');

    const ratingInput = document.getElementById('rating');
    if (ratingInput) ratingInput.value = '0';

    updateStarRating(0);
}