document.addEventListener('DOMContentLoaded', function () {
  const stars = document.querySelectorAll('.rating-star');
  const ratingInput = document.getElementById('rating');
  const submitBtn = document.getElementById('submitReview');
  const form = document.getElementById('writeReviewForm');
  const commentInput = document.getElementById('comment');
  const reviewerNameInput = document.getElementById('reviewer_name');
  const currentRatingSpan = document.getElementById('current-rating');

  let ratingSelected = false;

  stars.forEach(star => {
    star.addEventListener('click', function () {
      const rating = parseInt(this.getAttribute('data-rating'));
      ratingInput.value = rating;
      ratingSelected = true;

      stars.forEach(s => {
        s.classList.remove('fas');
        s.classList.add('far');
      });

      for (let i = 0; i < rating; i++) {
        stars[i].classList.remove('far');
        stars[i].classList.add('fas');
      }

      currentRatingSpan.textContent = rating;
    });
  });

  submitBtn.addEventListener('click', function (e) {
    e.preventDefault();

    if (!ratingSelected || ratingInput.value < 1) {
      showToast('Please select a rating', 'error');
      return;
    }

    if (!commentInput.value.trim()) {
      showToast('Please write a review comment', 'error');
      return;
    }

    const formData = new FormData(form);
    formData.append('rating', ratingInput.value);

    fetch('../account/backend/submit_review.php', {
      method: 'POST',
      body: formData
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then(data => {
        if (data.success) {
          showToast(data.message, 'success');

          const modal = bootstrap.Modal.getInstance(document.getElementById('writeReview'));
          if (modal) modal.hide();

          form.reset();
          resetStarRating();

          if (data.shop_id) {
            updateReviewsSection(data.shop_id);
            updateRatingSummary(data.shop_id);
          }
        } else {
          showToast(data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error submitting review:', error);
        showToast('Failed to submit review. Please try again.', 'error');
      });
  });

  document.querySelectorAll('.write-review-btn').forEach(button => {
    button.addEventListener('click', function () {
      const shopName = this.getAttribute('data-shop-name');
      const shopId = this.getAttribute('data-shop-id');

      if (!shopId || !shopName) {
        showToast('Missing shop information', 'error');
        return;
      }

      fetchUserInfo()
        .then(userData => {
          if (userData && userData.name) {
            reviewerNameInput.value = userData.name;
          } else {
            reviewerNameInput.value = 'User';
          }

          document.getElementById('modal_shop_name').value = shopName;
          document.getElementById('shop_id').value = shopId;
          const modal = new bootstrap.Modal(document.getElementById('writeReview'));
          modal.show();
        })
        .catch(error => {
          console.error('Error fetching user info:', error);
          showToast('Failed to load user information', 'error');
        });
    });
  });

  function resetStarRating() {
    ratingSelected = false;
    ratingInput.value = '0';
    stars.forEach(s => {
      s.classList.remove('fas');
      s.classList.add('far');
    });
    currentRatingSpan.textContent = '0';
  }

  function fetchUserInfo() {
    return fetch('../account/backend/get_user_info.php')
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to fetch user info');
        }
        return response.json();
      });
  }

  function updateReviewsSection(shopId) {
    fetch(`../account/backend/get_reviews.php?shop_id=${shopId}`)
      .then(response => response.json())
      .then(reviews => {
        const reviewsContainer = document.getElementById('reviewsContainer');
        
        if (!reviews || reviews.length === 0) {
          reviewsContainer.innerHTML = '<p style="text-align: center; font-size: 14px; color: #6c757d;">No reviews yet. Be the first to leave a review!</p>';
          return;
        }

        let reviewsHTML = `
          <div class="review-card" style="margin-bottom: 15px; padding: 12px; border-radius: 8px; background: #f8f9fa;">
        `;

        reviews.forEach((review, index) => {
          const reviewCount = index + 1;
          const isHidden = reviewCount > 3 ? 'hidden-review' : '';
          const hrDisplay = (reviewCount >= 3) ? 'none' : 'block';
          
          reviewsHTML += `
            <div class="single-review ${isHidden}" style="margin-bottom: 15px;">
              <div class="reviewer-info" style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; flex-wrap: wrap;">
                <img src="../assets/img/profile/${review.profile_picture || 'profile-user.png'}"
                    alt="${review.fullname}"
                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                <div style="flex: 1; min-width: 120px;">
                  <h3 style="margin: 0; font-size: 16px; font-weight: 600;">${review.fullname}</h3>
                  <p style="margin: 0; font-size: 12px; color: #6c757d;">${new Date(review.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>
                <div class="stars" style="font-size: 14px; white-space: nowrap;">
                  ${'<i class="fas fa-star"></i>'.repeat(review.rating)}
                  ${'<i class="far fa-star"></i>'.repeat(5 - review.rating)}
                </div>
              </div>
              <p style="margin: 0; font-size: 14px; line-height: 1.4;">${review.comment}</p>
              ${index < reviews.length - 1 ? `<hr style="margin: 12px 0; display: ${hrDisplay};">` : ''}
            </div>
          `;
        });

        reviewsHTML += `</div>`;
        reviewsContainer.innerHTML = reviewsHTML;
      })
      .catch(error => {
        console.error('Error updating reviews:', error);
      });
  }

  function updateRatingSummary(shopId) {
    fetch(`../account/backend/get_rating_summary.php?shop_id=${shopId}`)
      .then(response => response.json())
      .then(data => {
        if (!data) return;

        const ratingNumber = document.querySelector('.rating-number');
        if (ratingNumber) {
          ratingNumber.textContent = data.average_rating || '0';
        }

        const allReviews = document.querySelector('.all-reviews p');
        if (allReviews) {
          allReviews.textContent = `Based on ${data.total_reviews || '0'} reviews`;
        }

        const starsContainer = document.querySelector('.stars');
        if (starsContainer) {
          starsContainer.innerHTML = '';
          const fullStars = Math.floor(data.average_rating);
          const hasHalfStar = (data.average_rating - fullStars) >= 0.5;

          for (let i = 1; i <= 5; i++) {
            const star = document.createElement('i');
            if (i <= fullStars) {
              star.className = 'fas fa-star';
            } else if (hasHalfStar && i === fullStars + 1) {
              star.className = 'fas fa-star-half-alt';
            } else {
              star.className = 'far fa-star';
            }
            starsContainer.appendChild(star);
          }
        }
      })
      .catch(error => {
        console.error('Error updating rating summary:', error);
      });
  }

  function showToast(message, type) {
  const toastContainer = document.getElementById('toast-container');
  if (!toastContainer) {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.style.position = 'fixed';
    container.style.top = '20px';
    container.style.left = '0';
    container.style.width = '100%';
    container.style.display = 'flex';
    container.style.flexDirection = 'column';
    container.style.alignItems = 'center';
    container.style.zIndex = '9999';
    container.style.pointerEvents = 'none';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast-${type}`;
  toast.style.backgroundColor = type === 'error' ? '#d32f2f' : '#388e3c';
  toast.style.color = 'white';
  toast.style.borderRadius = '10px';
  toast.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.3)';
  toast.style.padding = '12px 16px';
  toast.style.position = 'relative';
  toast.style.marginBottom = '10px';
  toast.style.textAlign = 'center';
  toast.style.minWidth = '250px';
  toast.style.maxWidth = '90%';
  toast.style.transition = 'opacity 0.3s ease';
  toast.style.pointerEvents = 'auto';

  const closeButton = document.createElement('button');
  closeButton.innerHTML = '&times;';
  closeButton.style.color = 'white';
  closeButton.style.background = 'transparent';
  closeButton.style.border = 'none';
  closeButton.style.fontWeight = 'bold';
  closeButton.style.fontSize = '18px';
  closeButton.style.position = 'absolute';
  closeButton.style.right = '8px';
  closeButton.style.top = '8px';
  closeButton.style.cursor = 'pointer';
  closeButton.style.padding = '0 5px';
  closeButton.style.lineHeight = '1';

  const messageElement = document.createElement('div');
  messageElement.textContent = message;
  messageElement.style.paddingRight = '25px';

  toast.appendChild(messageElement);
  toast.appendChild(closeButton);

  const container = document.getElementById('toast-container');
  container.appendChild(toast);

  const autoRemove = setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => {
      toast.remove();
      if (container.children.length === 0) {
        container.remove();
      }
    }, 300);
  }, 3000);

  closeButton.addEventListener('click', () => {
    clearTimeout(autoRemove);
    toast.style.opacity = '0';
    setTimeout(() => {
      toast.remove();
      if (container.children.length === 0) {
        container.remove();
      }
    }, 300);
  });
}
});