const tabs = document.querySelectorAll('.view-shop-tab-button');
const panels = document.querySelectorAll('.view-shop-tab-panels > section');
function setActiveTab(index) {
    tabs.forEach((tab, i) => {
        const isActive = i === index;
        tab.classList.toggle('active', isActive);
        tab.setAttribute('aria-selected', isActive);
        tab.setAttribute('tabindex', isActive ? '0' : '-1');
        panels[i].hidden = !isActive;
        panels[i].setAttribute('aria-hidden', !isActive);
    });
}
tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => setActiveTab(index));
    tab.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
            let nextIndex = index + (e.key === 'ArrowRight' ? 1 : -1);
            if (nextIndex < 0) nextIndex = tabs.length - 1;
            if (nextIndex >= tabs.length) nextIndex = 0;
            tabs[nextIndex].focus();
        }
    });
});
setActiveTab(0);