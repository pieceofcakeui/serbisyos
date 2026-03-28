document.addEventListener('DOMContentLoaded', function() {
    initializeReviewLikes();
});

function initializeReviewLikes() {
    const likeButtons = document.querySelectorAll('.view-shop-like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            handleLikeClick(this);
        });
    });
}

function handleLikeClick(button) {
    const reviewId = button.getAttribute('data-review-id');
    const reviewOwnerId = button.getAttribute('data-review-owner-id');

    if (!window.currentUser?.id) {
        toastr.warning('Please login to like reviews');
        return;
    }

    const icon = button.querySelector('i');
    const likeCountSpan = button.querySelector('.like-count');
    const isLiked = icon.classList.contains('fas');
    
    const data = {
        review_id: reviewId,
        review_owner_id: reviewOwnerId,
        action: isLiked ? 'unlike' : 'like'
    };

    processLikeRequest(data, icon, likeCountSpan, button);
}

function processLikeRequest(data, icon, likeCountSpan, button) {
    fetch(`${BASE_URL}/account/backend/review_likes.php`, {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(handleResponse)
    .then(data => {
        if (data.success) {
            updateLikeUI(data, icon, likeCountSpan, button);
        } else {
            toastr.error(data.message || 'Error processing your like');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        toastr.error('An error occurred while processing your like');
    });
}

function handleResponse(response) {
    if (!response.ok) {
        throw new Error('Network response was not ok');
    }
    return response.json();
}

function updateLikeUI(data, icon, likeCountSpan, button) {
    icon.classList.toggle('fas');
    icon.classList.toggle('far');

    if (likeCountSpan) {
        likeCountSpan.textContent = data.review_like_count;
    }

    const likeTextNodes = Array.from(button.childNodes).filter(node => 
        node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== ''
    );
    
    if (likeTextNodes.length > 0) {
        likeTextNodes[0].textContent = data.review_like_count !== 1 ? ' Likes' : ' Like';
    }
}