document.addEventListener('DOMContentLoaded', function() {
    const sortSelect = document.getElementById('sortReviews');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortReviews(this.value);
        });
    }

    function sortReviews(sortBy) {
        const reviewsList = document.getElementById('reviewsList');
        const reviews = Array.from(reviewsList.querySelectorAll('.review-card'));

        reviews.sort((a, b) => {
            if (sortBy === 'highest') {
                return parseInt(b.dataset.rating) - parseInt(a.dataset.rating);
            } else if (sortBy === 'lowest') {
                return parseInt(a.dataset.rating) - parseInt(b.dataset.rating);
            } else if (sortBy === 'oldest') {
                return parseInt(a.dataset.date) - parseInt(b.dataset.date);
            } else {
                return parseInt(b.dataset.date) - parseInt(a.dataset.date);
            }
        });

        reviewsList.innerHTML = '';
        if (reviews.length > 0) {
            reviews.forEach(review => reviewsList.appendChild(review));
        } else {
            reviewsList.innerHTML = '<div class="no-reviews"><p>No reviews yet for <?php echo htmlspecialchars($shop_name); ?>.</p></div>';
        }
    }

    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        let currentPage = 1;
        loadMoreBtn.addEventListener('click', function() {
            const spinner = this.querySelector('.spinner-border');
            const buttonText = this.querySelector('span:not(.spinner-border)');

            spinner.classList.remove('d-none');
            buttonText.textContent = 'Loading...';
            this.disabled = true;
            
            currentPage++;
            loadMoreReviews(currentPage);
        });
    }

    function showToast(message, type = 'success') {
        const toastContainer = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast show align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0`;
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
        
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }
});