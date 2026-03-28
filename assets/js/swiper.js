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
