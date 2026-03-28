document.addEventListener('DOMContentLoaded', function() {
  const userDropdownTrigger = document.getElementById('userDropdownTrigger');
  const userDropdownContent = document.querySelector('.user-dropdown-content');
  
  if (userDropdownTrigger && userDropdownContent) {
    userDropdownTrigger.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      document.querySelectorAll('.dropdown-content').forEach(dropdown => {
        if (dropdown !== userDropdownContent) {
          dropdown.classList.remove('show');
          dropdown.style.display = 'none';
        }
      });

      if (userDropdownContent.classList.contains('show')) {
        userDropdownContent.classList.remove('show');
        userDropdownContent.style.display = 'none';
      } else {
        userDropdownContent.style.display = 'block';
        userDropdownContent.classList.add('show');
      }
    });
  }

  document.addEventListener('click', function(e) {
    if (!e.target.closest('.user-dropdown')) {
      const userDropdown = document.querySelector('.user-dropdown-content');
      if (userDropdown) {
        userDropdown.classList.remove('show');
        userDropdown.style.display = 'none';
      }
    }
  });
});


function selectOption(value) {
  document.querySelector(".dropdown-btn").textContent = value;
}


document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".remove-shop").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      var removeModal = new bootstrap.Modal(
        document.getElementById("removeShopModal")
      );
      removeModal.show();
    });
  });


  document.querySelectorAll(".share-shop").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      var shareModal = new bootstrap.Modal(
        document.getElementById("shareShopModal")
      );
      shareModal.show();
    });
  });
});


document.querySelectorAll(".faq-question").forEach((item) => {
  item.addEventListener("click", () => {
    const parent = item.parentElement;
    parent.classList.toggle("open");
  });
});


document.addEventListener("DOMContentLoaded", function () {
  const handleStarRating = (stars) => {
    stars.forEach((star) => {
      star.addEventListener("click", function () {
        const rating = this.dataset.rating;
        const category = this.dataset.category;
        const parentDiv = this.closest("div");
        const allStars = parentDiv.querySelectorAll(".rating-star");

        allStars.forEach((s, index) => {
          if (index < rating) {
            s.classList.remove("far");
            s.classList.add("fas");
          } else {
            s.classList.remove("fas");
            s.classList.add("far");
          }
        });
      });


      star.addEventListener("mouseover", function () {
        const rating = this.dataset.rating;
        const parentDiv = this.closest("div");
        const allStars = parentDiv.querySelectorAll(".rating-star");

        allStars.forEach((s, index) => {
          if (index < rating) {
            s.classList.add("hover");
          }
        });
      });

      star.addEventListener("mouseout", function () {
        const parentDiv = this.closest("div");
        const allStars = parentDiv.querySelectorAll(".rating-star");
        allStars.forEach((s) => s.classList.remove("hover"));
      });
    });
  };

  const allStars = document.querySelectorAll(".rating-star");
  handleStarRating(allStars);

  document
    .getElementById("submitReview")
    .addEventListener("click", function () {
      const formData = {
        overallRating:
          document.querySelector(".rating-stars .fas")?.dataset.rating || 0,
        serviceQuality:
          document.querySelector('[data-category="service"].fas')?.dataset
            .rating || 0,
        priceFairness:
          document.querySelector('[data-category="price"].fas')?.dataset
            .rating || 0,
        responseTime:
          document.querySelector('[data-category="response"].fas')?.dataset
            .rating || 0,
        professionalism:
          document.querySelector('[data-category="professional"].fas')?.dataset
            .rating || 0,
        serviceUsed: document.getElementById("serviceUsed").value,
        review: document.getElementById("reviewText").value,
      };

      console.log("Review Data:", formData);

      const modal = bootstrap.Modal.getInstance(
        document.getElementById("rateExperienceModal")
      );
      modal.hide();

      document.getElementById("rateExperienceForm").reset();
      document.querySelectorAll(".rating-star").forEach((star) => {
        star.classList.remove("fas");
        star.classList.add("far");
      });
    });
});


document.addEventListener("DOMContentLoaded", function () {
  const rateButtons = document.querySelectorAll(
    '[data-bs-target="#rateExperienceModal"]'
  );

  rateButtons.forEach((button) => {
    button.addEventListener("click", function () {
      rateButtons.forEach((btn) => btn.classList.remove("clicked"));

      this.classList.add("clicked");

      const rateModal = document.getElementById("rateExperienceModal");
      rateModal.addEventListener(
        "hidden.bs.modal",
        function () {
          button.classList.remove("clicked");
        },
        { once: true }
      );
    });
  });
});


function toggleAnswer(answerId) {
  var answerElement = document.getElementById(answerId);
  var icon = answerElement.previousElementSibling.querySelector("i");

  if (answerElement.style.display === "none") {
    answerElement.style.display = "block";
    icon.classList.remove("fa-chevron-down");
    icon.classList.add("fa-chevron-up");
  } else {
    answerElement.style.display = "none";
    icon.classList.remove("fa-chevron-up");
    icon.classList.add("fa-chevron-down");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  var answers = document.querySelectorAll(".faq-answer");
  answers.forEach(function (answer) {
    answer.style.display = "none";
  });
});

document.getElementById('file-input').addEventListener('change', function (event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      document.getElementById('profile-pic-preview').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector(".service-carousel");
  const prevBtn = document.querySelector(".prev-btn");
  const nextBtn = document.querySelector(".next-btn");
  const dotsContainer = document.querySelector(".carousel-dots");

  let services = document.querySelectorAll(".service-card");
  let index = 0;
  let serviceWidth = services[0].clientWidth + 15;
  let visibleCards = Math.floor(carousel.clientWidth / serviceWidth);

  let totalPages = Math.ceil(services.length / visibleCards);
  for (let i = 0; i < totalPages; i++) {
    let dot = document.createElement("span");
    dot.classList.add("dot");
    if (i === 0) dot.classList.add("active");
    dotsContainer.appendChild(dot);
  }
  const dots = document.querySelectorAll(".dot");

  function scrollToIndex() {
    carousel.scrollTo({
      left: index * serviceWidth * visibleCards,
      behavior: "smooth",
    });
    dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
  }

  prevBtn.addEventListener("click", function () {
    if (index > 0) {
      index--;
      scrollToIndex();
    }
  });

  nextBtn.addEventListener("click", function () {
    if (index < totalPages - 1) {
      index++;
      scrollToIndex();
    }
  });

  dots.forEach((dot, i) => {
    dot.addEventListener("click", function () {
      index = i;
      scrollToIndex();
    });
  });

  let startX, scrollLeft;
  carousel.addEventListener("touchstart", (e) => {
    startX = e.touches[0].pageX;
    scrollLeft = carousel.scrollLeft;
  });

  carousel.addEventListener("touchmove", (e) => {
    let touchMove = e.touches[0].pageX - startX;
    carousel.scrollLeft = scrollLeft - touchMove;
  });

  carousel.addEventListener("touchend", () => {
    let newIndex = Math.round(carousel.scrollLeft / (serviceWidth * visibleCards));
    index = newIndex;
    scrollToIndex();
  });

  window.addEventListener("resize", () => {
    serviceWidth = services[0].clientWidth + 15;
    visibleCards = Math.floor(carousel.clientWidth / serviceWidth);
    totalPages = Math.ceil(services.length / visibleCards);
    dotsContainer.innerHTML = "";
    for (let i = 0; i < totalPages; i++) {
      let dot = document.createElement("span");
      dot.classList.add("dot");
      if (i === 0) dot.classList.add("active");
      dotsContainer.appendChild(dot);
    }
  });
});
