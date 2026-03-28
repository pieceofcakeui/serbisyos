document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.like-button').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const reviewOwnerId = this.getAttribute('data-review-owner-id');
            const icon = this.querySelector('i');
            const likeCountSpan = this.querySelector('.like-count');
            let likeCount = parseInt(likeCountSpan.textContent);
            const isLiked = icon.classList.contains('fas');
            
            fetch('../account/backend/review_likes.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    review_id: reviewId,
                    review_owner_id: reviewOwnerId,
                    action: isLiked ? 'unlike' : 'like'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.action === 'like') {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.style.color = '#0d6efd';
                        likeCount++;
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.style.color = '#6c757d';
                        likeCount--;
                    }
                    
                    likeCountSpan.textContent = likeCount;
                    const likeText = likeCount === 1 ? 'Like' : 'Likes';
                    this.innerHTML = `<i class="${icon.className} fa-thumbs-up"></i> <span class="like-count">${likeCount}</span> ${likeText}`;
                    
                    console.log(`Total likes for this user's reviews: ${data.total_user_likes}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });

    const reviewsContainer = document.getElementById("reviewsContainer");
    const prevBtn = document.getElementById("prevBtn");
    const nextBtn = document.getElementById("nextBtn");
    const currentPageNum = document.getElementById("currentPageNum");
    const remainingCount = document.getElementById("remainingCount");

    const REVIEWS_PER_PAGE = 3;
    let currentPage = 1;
    let totalReviews = 0;

    function getSingleReviews() {
        return Array.from(document.querySelectorAll(".single-review"));
    }

    function getTotalPages() {
        return Math.ceil(totalReviews / REVIEWS_PER_PAGE);
    }

    function initializeReviews() {
        const singleReviews = getSingleReviews();
        totalReviews = singleReviews.length;
        
        updateReviewVisibility();
        updatePaginationControls();
    }

    function updateReviewVisibility() {
        const singleReviews = getSingleReviews();
        const startIndex = (currentPage - 1) * REVIEWS_PER_PAGE;
        const endIndex = startIndex + REVIEWS_PER_PAGE;

        singleReviews.forEach((review, index) => {
            const isVisible = index >= startIndex && index < endIndex;
            review.classList.toggle("hidden-review", !isVisible);
            const hr = review.querySelector("hr") || review.nextElementSibling;
            if (hr && hr.tagName === 'HR') {
                const isLastInPage = index === endIndex - 1 || index === singleReviews.length - 1;
                hr.style.display = isVisible && !isLastInPage ? 'block' : 'none';
            }
        });
    }

    function updatePaginationControls() {
        const totalPages = getTotalPages();
        const remainingReviews = totalReviews - (currentPage * REVIEWS_PER_PAGE);
        if (currentPageNum) {
            currentPageNum.textContent = currentPage;
        }
        if (remainingCount) {
            if (remainingReviews > 0) {
                remainingCount.textContent = `+${remainingReviews}`;
                remainingCount.style.display = 'flex';
            } else {
                remainingCount.style.display = 'none';
            }
        }

        if (prevBtn) {
            prevBtn.disabled = currentPage <= 1;
        }
        
        if (nextBtn) {
            nextBtn.disabled = currentPage >= totalPages;
        }
    }

    function goToNextPage() {
        const totalPages = getTotalPages();
        if (currentPage < totalPages) {
            currentPage++;
            updateReviewVisibility();
            updatePaginationControls();
            setTimeout(() => {
                reviewsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }

    function goToPrevPage() {
        if (currentPage > 1) {
            currentPage--;
            updateReviewVisibility();
            updatePaginationControls();

            setTimeout(() => {
                reviewsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 100);
        }
    }

    initializeReviews();

    if (nextBtn) nextBtn.addEventListener("click", goToNextPage);
    if (prevBtn) prevBtn.addEventListener("click", goToPrevPage);

    window.refreshReviews = function() {
        currentPage = 1;
        initializeReviews();
    };
});