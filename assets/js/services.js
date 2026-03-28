document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-btn");
    const serviceCards = document.querySelectorAll(".service-card");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            const category = this.textContent.trim();

            filterButtons.forEach(btn => btn.classList.remove("active"));
            this.classList.add("active");

            serviceCards.forEach(card => {
                if (category === "All Services" || card.dataset.category === category) {
                    card.parentElement.style.display = "block";
                } else {
                    card.parentElement.style.display = "none";
                }
            });
        });
    });
});