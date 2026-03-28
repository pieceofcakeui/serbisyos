document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.view-shop-tab-button');
    const panels = document.querySelectorAll('.view-shop-tab-panels > section');
    const setActiveTab = (index) => {
        tabs.forEach((tab, i) => {
            const isActive = i === index;
            tab.classList.toggle('active', isActive);
            tab.setAttribute('aria-selected', isActive.toString());
            panels[i].hidden = !isActive;
        });
    };
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => setActiveTab(index));
    });
    setActiveTab(0);
});
