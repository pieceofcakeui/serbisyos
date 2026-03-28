document.addEventListener('DOMContentLoaded', function () {
    const shopDataElement = document.querySelector('[data-shop-data]');
    const shopData = shopDataElement ? JSON.parse(shopDataElement.dataset.shopData) : null;
    const totalReviews = shopData ? shopData.total_reviews : 0;
    const shopId = shopData ? shopData.shop_id : 0;
    const shopOwnerId = shopData ? shopData.shop_owner_id : 0;
    const userId = shopData ? shopData.user_id : 0;
    const initialReviewsCount = 5;
    const perPage = 5;

    function showToast(message, type) {
        const toastContainer = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'error' ? 'danger' : 'success'} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        const bootstrapToast = new bootstrap.Toast(toast);
        bootstrapToast.show();

        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
        });
    }

    function showResponseModal(reviewId) {
        const modalHTML = `
    <div class="modal fade" id="responseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h5>Respond to Review</h5>
                    </div>
                    <form id="responseForm" class="d-flex flex-column gap-3">
                        <input type="hidden" name="review_id" value="${reviewId}">
                        <div class="mb-3">
                            <label for="responseText" class="form-label">Your Response</label>
                            <textarea class="form-control" id="responseText" name="response" rows="4" required></textarea>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning" style="height: 40px; padding: 0 10px;">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));
        responseModal.show();

        document.getElementById('responseForm').addEventListener('submit', function (e) {
            e.preventDefault();
            submitResponse(reviewId);
        });

        document.getElementById('responseModal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    }

    function submitResponse(reviewId) {
        const responseText = document.getElementById('responseText').value.trim();
        const submitBtn = document.querySelector('#responseForm button[type="submit"]');

        if (!responseText) {
            showToast('Please enter a response', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

        fetch('../account/backend/respond_to_review.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                review_id: reviewId,
                response: responseText
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('responseModal')).hide();
                    updateReviewCard(reviewId, data.response);
                    showToast(data.message, 'success');
                } else {
                    showToast(data.error || 'Failed to submit response', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while submitting your response', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Response';
            });
    }

    function updateReviewCard(reviewId, responseData) {
        const reviewCard = document.querySelector(`.review-card[data-review-id="${reviewId}"]`);

        if (reviewCard) {
            const responseHTML = `
                <div class="response-card p-2 mt-2" style="background-color: #f0f0f0; border-radius: 5px;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-reply me-1 text-dark"></i>
                        <strong>${responseData.shop_owner_name}</strong>
                        <span class="ms-1 text-muted">${new Date(responseData.created_at).toLocaleDateString('en-US', { month: 'numeric', day: 'numeric', year: '2-digit' })}</span>
                        <div class="ms-2 response-actions">
                            <button class="btn-edit-response btn-sm" data-review-id="${reviewId}">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                    </div>
                    <p class="mb-0 response-text">${responseData.response}</p>
                </div>
            `;

            const respondBtn = reviewCard.querySelector('.respond-btn');
            if (respondBtn) respondBtn.remove();

            const cardBody = reviewCard.querySelector('.card-body');
            cardBody.insertAdjacentHTML('beforeend', responseHTML);

            const editBtn = cardBody.querySelector('.btn-edit-response');
            if (editBtn) {
                editBtn.addEventListener('click', function () {
                    showEditResponseModal(reviewId, responseData.response);
                });
            }
        }
    }

    function showEditResponseModal(reviewId, currentText) {
        const modalHTML = `
    <div class="modal fade" id="editResponseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <h5>Edit Response</h5>
                    </div>
                    <form id="editResponseForm" class="d-flex flex-column gap-3">
                        <input type="hidden" name="review_id" value="${reviewId}">
                        <div class="mb-3">
                            <label for="editResponseText" class="form-label">Your Response</label>
                            <textarea class="form-control" id="editResponseText" name="response" rows="4" required>${currentText}</textarea>
                        </div>
                        <div class="d-flex justify-content-center gap-3 mt-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning" style="height: 40px; padding: 0 10px;">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>`;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        const editModal = new bootstrap.Modal(document.getElementById('editResponseModal'));
        editModal.show();

        document.getElementById('editResponseForm').addEventListener('submit', function (e) {
            e.preventDefault();
            submitEditedResponse(reviewId);
        });

        document.getElementById('editResponseModal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    }

    function submitEditedResponse(reviewId) {
        const responseText = document.getElementById('editResponseText').value.trim();
        const submitBtn = document.querySelector('#editResponseForm button[type="submit"]');

        if (!responseText) {
            showToast('Please enter a response', 'error');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';

        fetch('../account/backend/edit_response.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                review_id: reviewId,
                response: responseText
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editResponseModal')).hide();

                    const responseCard = document.querySelector(`.btn-edit-response[data-review-id="${reviewId}"]`).closest('.response-card');
                    if (responseCard) {
                        responseCard.querySelector('.response-text').textContent = responseText;
                    }

                    showToast(data.message, 'success');
                } else {
                    showToast(data.error || 'Failed to update response', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred while updating your response', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Changes';
            });
    }

    let currentPage = 1;
    let isLoading = false;
    let initialReviewsHTML = '';

    const sortSelect = document.getElementById('sortReviews');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const seeLessBtn = document.getElementById('seeLessBtn');
    const reviewsList = document.getElementById('reviewsList');
    const reviewsCounter = document.getElementById('reviewsCounter');

    if (reviewsList) {
        initialReviewsHTML = reviewsList.innerHTML;
    }

    function updateButtonVisibility() {
        const allReviews = reviewsList.querySelectorAll('.review-card');
        const loadedCount = allReviews.length;

        if (seeLessBtn) {
            seeLessBtn.classList.toggle('d-none', loadedCount <= initialReviewsCount);
        }

        if (loadMoreBtn) {
            loadMoreBtn.classList.toggle('d-none', loadedCount >= totalReviews);
        }

        updateReviewsCounter(loadedCount);
    }

    function updateReviewsCounter(loadedCount) {
        if (reviewsCounter) {
            reviewsCounter.textContent = `Showing ${loadedCount} of ${totalReviews} reviews`;
        }
    }

    function appendReviews(reviews) {
        reviews.forEach(review => {
            const reviewDate = new Date(review.created_at);
            const formattedDate = reviewDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });

            let responseHTML = '';
            if (review.owner_response) {
                const responseDate = new Date(review.response_date);
                const formattedResponseDate = responseDate.toLocaleDateString('en-US', {
                    month: 'numeric',
                    day: 'numeric',
                    year: '2-digit'
                });

                const canEdit = userId === shopOwnerId;

                responseHTML = `
                    <div class="response-card p-2 mt-2" style="background-color: #f0f0f0; border-radius: 5px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-reply me-1 text-dark"></i>
                            <strong>${review.owner_name}</strong>
                            <span class="ms-1 text-muted">${formattedResponseDate}</span>
                            ${canEdit ?
                        `<div class="ms-2 response-actions">
                                    <button class="btn-edit-response btn-sm" data-review-id="${review.id}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>` : ''}
                        </div>
                        <p class="mb-0 response-text">${review.owner_response}</p>
                    </div>
                `;
            } else if (userId === shopOwnerId) {
                responseHTML = `
                    <button class="respond-btn btn-sm mt-1 fs-7 custom-respond-btn" style="color: #333; border: 1px solid #333; data-review-id="${review.id}">
                        <i class="fas fa-reply" style="margin-right: 4px; color: #333;"></i>Respond to review
                    </button>
                `;
            }

            const starsHTML = Array(5).fill().map((_, i) =>
                i < review.rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>'
            ).join('');

            const profilePic = review.profile_picture ?
                `../assets/img/profile/${review.profile_picture}` :
                '../assets/img/profile/profile-user.png';

            const reviewHTML = `
                <div class="card review-card mb-1" data-rating="${review.rating}" 
                     data-date="${Math.floor(reviewDate.getTime() / 1000)}" 
                     data-review-id="${review.id}">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="d-flex">
                                <img src="${profilePic}" alt="User" class="user-avatar me-2">
                                <div>
                                    <h6 class="mb-0">${review.fullname}</h6>
                                    <span class="review-date">${formattedDate}</span>
                                </div>
                            </div>
                            <div class="stars">${starsHTML}</div>
                        </div>
                        <p class="mt-1 mb-1">${review.comment}</p>
                        ${responseHTML}
                    </div>
                </div>
            `;

            reviewsList.insertAdjacentHTML('beforeend', reviewHTML);
        });

        updateButtonVisibility();
    }

    function loadMoreReviews() {
        if (isLoading || !loadMoreBtn) return;

        isLoading = true;
        const spinner = loadMoreBtn.querySelector('.spinner-border');
        const buttonText = loadMoreBtn.querySelector('.btn-text');

        spinner?.classList.remove('d-none');
        buttonText.textContent = 'Loading...';
        loadMoreBtn.disabled = true;

        const sortBy = sortSelect ? sortSelect.value : 'recent';
        const nextPage = currentPage + 1;

        fetch(`../account/backend/load_more_reviews.php?shop_id=${shopId}&page=${nextPage}&sort=${sortBy}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.reviews.length > 0) {
                    appendReviews(data.reviews);
                    currentPage = nextPage;
                } else {
                    showToast('No more reviews to load', 'info');
                    if (loadMoreBtn) loadMoreBtn.classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error loading more reviews:', error);
                showToast('Failed to load more reviews', 'error');
            })
            .finally(() => {
                isLoading = false;
                const spinner = loadMoreBtn?.querySelector('.spinner-border');
                const buttonText = loadMoreBtn?.querySelector('.btn-text');

                spinner?.classList.add('d-none');
                if (buttonText) buttonText.textContent = 'Load More Reviews';
                if (loadMoreBtn) loadMoreBtn.disabled = false;
            });
    }

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('respond-btn') || e.target.closest('.respond-btn')) {
            const button = e.target.classList.contains('respond-btn') ? e.target : e.target.closest('.respond-btn');
            const reviewId = button.dataset.reviewId;
            showResponseModal(reviewId);
        }

        if (e.target.classList.contains('btn-edit-response') || e.target.closest('.btn-edit-response')) {
            const button = e.target.classList.contains('btn-edit-response') ? e.target : e.target.closest('.btn-edit-response');
            const reviewId = button.dataset.reviewId;
            const responseCard = button.closest('.response-card');
            const currentText = responseCard.querySelector('.response-text').textContent;

            showEditResponseModal(reviewId, currentText);
        }
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreReviews);
    }

    if (seeLessBtn) {
        seeLessBtn.addEventListener('click', function () {
            currentPage = 1;
            if (reviewsList) {
                reviewsList.innerHTML = initialReviewsHTML;
            }
            updateButtonVisibility();

            document.querySelectorAll('.respond-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const reviewId = this.dataset.reviewId;
                    showResponseModal(reviewId);
                });
            });

            document.querySelectorAll('.btn-edit-response').forEach(btn => {
                btn.addEventListener('click', function () {
                    const reviewId = this.dataset.reviewId;
                    const responseCard = this.closest('.response-card');
                    const currentText = responseCard.querySelector('.response-text').textContent;
                    showEditResponseModal(reviewId, currentText);
                });
            });

            document.querySelector('.reviews-section').scrollIntoView({ behavior: 'smooth' });
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            currentPage = 1;
            const sortBy = this.value;

            fetch(`../account/backend/load_more_reviews.php?shop_id=${shopId}&page=1&sort=${sortBy}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reviewsList.innerHTML = '';
                        if (data.reviews.length > 0) {
                            appendReviews(data.reviews);
                        } else {
                            reviewsList.innerHTML = '<div class="no-reviews"><p>No reviews yet.</p></div>';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error sorting reviews:', error);
                    showToast('Failed to sort reviews', 'error');
                });
        });
    }

    updateButtonVisibility();
});