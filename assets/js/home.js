window.closeOnboardingModal = function() {
    document.getElementById('onboardingAdModal').style.display = 'none';
    localStorage.setItem('onboardingModalShown', 'true');
}

window.closeRejectedModal = function() {
    document.getElementById('rejectedApplicationModal').style.display = 'none';
    localStorage.setItem('rejectedModalShown', 'true');
}

window.onload = function () {
    if (window.showApprovedModal && !localStorage.getItem('onboardingModalShown')) {
        document.getElementById('onboardingAdModal').style.display = 'block';
    }
    
    if (window.showRejectedModal && !localStorage.getItem('rejectedModalShown')) {
        document.getElementById('rejectedApplicationModal').style.display = 'block';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var modalElement = document.getElementById('newShopsModal');
    if (modalElement) {
        var newShopsModal = new bootstrap.Modal(modalElement);
        newShopsModal.show();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        const centerModal = new bootstrap.Modal(document.getElementById('centerModal'));
        centerModal.show();
    }, 1000);
});

document.addEventListener("DOMContentLoaded", function () {
    let scrollPosition = 0;
    let isScrollLocked = false;
    let activeModals = 0;
    
    function lockScroll() {
        if (isScrollLocked) {
            activeModals++;
            return;
        }
        
        scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${scrollPosition}px`;
        document.body.style.width = '100%';
        isScrollLocked = true;
        activeModals = 1;
    }
    
    function unlockScroll() {
        activeModals--;
        
        if (activeModals > 0 || !isScrollLocked) return;
        
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('position');
        document.body.style.removeProperty('top');
        document.body.style.removeProperty('width');
        
        window.scrollTo(0, scrollPosition);
        isScrollLocked = false;
        activeModals = 0;
    }
    
    function forceUnlockScroll() {
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('position');
        document.body.style.removeProperty('top');
        document.body.style.removeProperty('width');
        
        window.scrollTo(0, scrollPosition);
        isScrollLocked = false;
        activeModals = 0;
    }
    
    const rateButtons = document.querySelectorAll(".rate-experience-btn");
    rateButtons.forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            lockScroll();
            
            const shopName = this.getAttribute("data-shop-name");
            const shopId = this.getAttribute("data-shop-id");

            document.getElementById("modal_shop_name").value = shopName;
            document.getElementById("shop_id").value = shopId;
            document.getElementById("rating").value = "";
            document.getElementById("comment").value = "";
            
            const stars = document.querySelectorAll(".rating-star");
            stars.forEach(s => {
                s.classList.remove("fas", "text-warning");
                s.classList.add("far");
            });

            const modalElement = document.getElementById('rateExperienceModal');
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            modal.show();
        });
    });
    
    const rateExperienceModal = document.getElementById('rateExperienceModal');
    rateExperienceModal.addEventListener('hidden.bs.modal', function () {
        const messageModal = bootstrap.Modal.getInstance(document.getElementById('messageModal'));
        if (!messageModal || !messageModal._isShown) {
            setTimeout(unlockScroll, 50);
        }
    });
    
    const messageModalElement = document.getElementById('messageModal');
    messageModalElement.addEventListener('show.bs.modal', function() {
        if (!isScrollLocked) {
            lockScroll();
        }
    });
    
    messageModalElement.addEventListener('hidden.bs.modal', function () {
        setTimeout(forceUnlockScroll, 50);
    });

    document.querySelectorAll(".rating-star").forEach(function (star) {
        star.addEventListener("click", function () {
            const ratingValue = this.getAttribute("data-rating");
            document.getElementById("rating").value = ratingValue;

            const stars = document.querySelectorAll(".rating-star");
            stars.forEach((s, index) => {
                if (index < parseInt(ratingValue)) {
                    s.classList.remove("far");
                    s.classList.add("fas", "text-warning");
                } else {
                    s.classList.remove("fas", "text-warning");
                    s.classList.add("far");
                }
            });
        });
    });

    function showMessageModal(status, message) {
        const messageModal = document.getElementById('messageModal');
        const messageIcon = document.getElementById('messageIcon');
        const messageTitle = document.getElementById('messageTitle');
        const messageText = document.getElementById('messageText');
        const modalCloseBtn = document.getElementById('messageModalClose');

        if (status === 'success') {
            messageIcon.className = 'fas fa-check-circle text-success';
            messageTitle.textContent = 'Success';
            modalCloseBtn.className = 'btn btn-success';
        } else if (status === 'error') {
            messageIcon.className = 'fas fa-exclamation-triangle text-danger';
            messageTitle.textContent = 'Error';
            modalCloseBtn.className = 'btn btn-danger';
        }

        messageText.textContent = message;
        
        messageModal.style.display = 'block';
        messageModal.querySelector('.modal-dialog').style.maxWidth = 'fit-content';
        messageModal.querySelector('.modal-dialog').style.width = 'auto';

        const modal = new bootstrap.Modal(messageModal, {
            backdrop: 'static',
            keyboard: false
        });

        function handleModalClose() {
            const rateModal = bootstrap.Modal.getInstance(document.getElementById('rateExperienceModal'));
            if (rateModal && rateModal._isShown) {
                rateModal.hide();
            }
            
            document.getElementById("rating").value = "";
            document.getElementById("comment").value = "";
            
            const stars = document.querySelectorAll(".rating-star");
            stars.forEach(s => {
                s.classList.remove("fas", "text-warning");
                s.classList.add("far");
            });
        }

        messageModal.removeEventListener('hidden.bs.modal', handleModalClose);
        messageModal.addEventListener('hidden.bs.modal', handleModalClose, { once: true });
        
        modal.show();
    }

    document.getElementById("submitReview").addEventListener("click", function () {
        const ratingInput = document.getElementById("rating");
        const commentInput = document.getElementById("comment");

        if (!ratingInput.value) {
            showMessageModal('error', 'Please select a rating before submitting.');
            return;
        }

        if (!commentInput.value.trim()) {
            showMessageModal('error', 'Please enter a comment before submitting.');
            return;
        }

        const form = document.getElementById("rateExperienceForm");
        const formData = new FormData(form);
        formData.set("rating", ratingInput.value);

        const rateModal = bootstrap.Modal.getInstance(document.getElementById('rateExperienceModal'));
        if (rateModal) {
            rateModal.hide();
        }

        fetch("../account/rate-experience-backend/rate-experience.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showMessageModal(data.status, data.message);
        })
        .catch(error => {
            showMessageModal('error', 'An error occurred while submitting your review. Please try again.');
        });
    });
});