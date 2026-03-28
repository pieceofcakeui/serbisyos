function switchTab(event, tabName) {
    if (typeof hasUnsavedSettings === 'function' && hasUnsavedSettings()) {
        if (tabName !== 'settings') {
            pendingNavigation = () => {
                window.removeEventListener('beforeunload', beforeUnloadListener);
                
                const saveSettingsBtn = document.getElementById('saveSettingsBtn');
                if (saveSettingsBtn) saveSettingsBtn.disabled = true;

                performTabSwitch(event, tabName);
            };
            if (unsavedChangesModal) {
                unsavedChangesModal.show();
            }
            return;
        }
    }
    
    performTabSwitch(event, tabName);
}

function performTabSwitch(event, tabName) {
    document.querySelectorAll('.edit-shop-profile-tab-content').forEach(tab => {
        tab.classList.remove('edit-shop-profile-active');
    });

    document.querySelectorAll('.edit-shop-profile-tab-btn').forEach(btn => {
        btn.classList.remove('edit-shop-profile-active');
    });

    document.getElementById(tabName + 'Tab').classList.add('edit-shop-profile-active');

    const clickedButton = event.currentTarget;
    clickedButton.classList.add('edit-shop-profile-active');

    const indicator = document.getElementById('tabIndicator');
    const btnWidth = clickedButton.offsetWidth;
    const btnLeft = clickedButton.offsetLeft;
    
    indicator.style.width = btnWidth + 'px';
    indicator.style.left = btnLeft + 'px';
}

document.addEventListener('DOMContentLoaded', function() {
    const activeBtn = document.querySelector('.edit-shop-profile-tab-btn.edit-shop-profile-active');
    if (activeBtn) {
        const indicator = document.getElementById('tabIndicator');
        indicator.style.width = activeBtn.offsetWidth + 'px';
        indicator.style.left = activeBtn.offsetLeft + 'px';
    }
});