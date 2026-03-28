document.addEventListener('DOMContentLoaded', function() {
    const reviewsPerPage = 5;
    const reviewItems = document.querySelectorAll('.view-shop-review-item');
    const totalReviews = reviewItems.length;
    const totalPages = Math.ceil(totalReviews / reviewsPerPage);
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const currentPageNum = document.getElementById('currentPageNum');
    const remainingCount = document.getElementById('view-shop-remainingCount');
    
    let currentPage = 1;

    if (totalReviews > 0) {
        updatePagination();
    }

    function updatePagination() {
        reviewItems.forEach(item => {
            item.style.display = 'none';
        });

        const startIndex = (currentPage - 1) * reviewsPerPage;
        const endIndex = Math.min(startIndex + reviewsPerPage, totalReviews);
        
        for (let i = startIndex; i < endIndex; i++) {
            reviewItems[i].style.display = 'block';
        }

        currentPageNum.textContent = currentPage;
        
        if (totalPages > 1) {
            remainingCount.textContent = `of ${totalPages}`;
        } else {
            remainingCount.textContent = '';
        }

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
            }
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
            }
        });
    }
});