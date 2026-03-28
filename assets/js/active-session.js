document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('active-sessions-container');
    const logoutAllBtn = document.getElementById('logout-all-btn');
    const logoutAllSpinner = document.getElementById('logout-all-spinner');
    const sessionsCount = document.getElementById('active-sessions-count');
    const MAX_VISIBLE_SESSIONS = 3;
    let allSessions = [];

    createConfirmationModals();

    loadSessions();

   function createConfirmationModals() {
    const logoutAllModalHTML = `
        <div class="modal fade" id="logoutAllModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <p class="fw-bold">Are you sure you want to log out ALL devices?</p>
                        <p class="small text-muted">You will be redirected to the login page.</p>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmLogoutAll" style="height: 40px; padding: 0 10px;">Log Out All</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    const logoutDeviceModalHTML = `
        <div class="modal fade" id="logoutDeviceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <p class="fw-bold">Are you sure you want to log out this device?</p>
                        <p class="small text-muted">This will immediately terminate the session on the selected device.</p>
                        <div class="d-flex justify-content-center gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmLogoutDevice">Log Out</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    if (!document.getElementById('logoutAllModal')) {
        document.body.insertAdjacentHTML('beforeend', logoutAllModalHTML);
    }
    if (!document.getElementById('logoutDeviceModal')) {
        document.body.insertAdjacentHTML('beforeend', logoutDeviceModalHTML);
    }
}

    function loadSessions() {
        container.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading sessions...</span>
                </div>
            </div>
        `;

        fetch('../account/backend/get_active_sessions.php')
            .then(response => response.json())
            .then(sessions => {
                allSessions = sessions;
                renderSessions();
            })
            .catch(() => {
                container.innerHTML = '<div class="text-danger py-2">Failed to load active sessions.</div>';
            });
    }

    function renderSessions(showAll = false) {
        container.innerHTML = '';

        if (allSessions.length === 0) {
            container.innerHTML = '<div class="text-muted py-2">No active sessions found.</div>';
            logoutAllBtn.disabled = true;
            sessionsCount.textContent = '0 active sessions';
            return;
        }

        sessionsCount.textContent = `${allSessions.length} active session${allSessions.length !== 1 ? 's' : ''}`;
        logoutAllBtn.disabled = false;

        const sessionsToShow = showAll ? allSessions : allSessions.slice(0, MAX_VISIBLE_SESSIONS);

        sessionsToShow.forEach(session => {
            const sessionElement = createSessionElement(session);
            container.appendChild(sessionElement);
        });

        if (!showAll && allSessions.length > MAX_VISIBLE_SESSIONS) {
            const seeMoreBtn = document.createElement('button');
            seeMoreBtn.className = 'btn btn-sm btn-link text-primary';
            seeMoreBtn.innerHTML = '<i class="fas fa-chevron-down"></i> See more devices';
            seeMoreBtn.addEventListener('click', () => renderSessions(true));

            const seeMoreContainer = document.createElement('div');
            seeMoreContainer.className = 'text-center py-2';
            seeMoreContainer.appendChild(seeMoreBtn);
            container.appendChild(seeMoreContainer);
        }
        else if (showAll && allSessions.length > MAX_VISIBLE_SESSIONS) {
            const seeLessBtn = document.createElement('button');
            seeLessBtn.className = 'btn btn-sm btn-link text-primary';
            seeLessBtn.innerHTML = '<i class="fas fa-chevron-up"></i> See less';
            seeLessBtn.addEventListener('click', () => renderSessions(false));

            const seeLessContainer = document.createElement('div');
            seeLessContainer.className = 'text-center py-2';
            seeLessContainer.appendChild(seeLessBtn);
            container.appendChild(seeLessContainer);
        }

        document.querySelectorAll('.terminate-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const sessionId = this.getAttribute('data-session-id');
                const sessionElement = this.closest('.list-group-item');

                const modal = new bootstrap.Modal(document.getElementById('logoutDeviceModal'));

                document.getElementById('confirmLogoutDevice').onclick = function () {
                    modal.hide();
                    terminateSession(sessionId, sessionElement);
                };

                modal.show();
            });
        });
    }

  function createSessionElement(session) {
    const sessionElement = document.createElement('div');
    sessionElement.className = 'list-group-item';

    let deviceTypeIcon = 'fas fa-laptop';
    if (session.device_type === 'mobile') {
        deviceTypeIcon = 'fas fa-mobile-alt';
    }

    sessionElement.innerHTML = `
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div class="d-flex flex-column flex-sm-row align-items-start gap-3 w-100">
                <div class="device-icon">
                    <i class="${session.os_icon} fa-2x text-primary"></i>
                </div>
                <div class="w-100">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <i class="${session.browser_icon}"></i>
                        <strong>${session.browser} on ${session.os}</strong>
                        ${session.is_current ? '<span class="badge bg-primary">This device</span>' : ''}
                    </div>
                    <div class="d-flex flex-wrap gap-2 text-muted small">
                        <span><i class="fas fa-network-wired"></i> ${session.ip}</span>
                        <span><i class="fas fa-sign-in-alt"></i> ${session.login_time}</span>
                        <span><i class="far fa-clock"></i> Last active: ${session.last_active}</span>
                        <span><i class="${deviceTypeIcon}"></i> ${session.device_type === 'mobile' ? 'Mobile' : 'Desktop'}</span>
                    </div>
                </div>
            </div>
            <div class="ms-md-auto">
                <button class="btn btn-sm btn-outline-danger terminate-btn" data-session-id="${session.session_id}">
                    <span class="spinner-border spinner-border-sm d-none" id="spinner-${session.session_id}"></span>
                    <i class="fas fa-sign-out-alt"></i> Log out
                </button>
            </div>
        </div>
    `;

    return sessionElement;
}

    logoutAllBtn.addEventListener('click', function () {
        const modal = new bootstrap.Modal(document.getElementById('logoutAllModal'));
        document.getElementById('confirmLogoutAll').onclick = function () {
            modal.hide();
            logoutAllBtn.disabled = true;
            logoutAllSpinner.classList.remove('d-none');

            fetch('../account/backend/logout_all_sessions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                credentials: 'include'
            })
                .then(async response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        const text = await response.text();
                        throw new Error(text || 'Invalid response from server');
                    }
                })
                .then(data => {
                    window.location.href = '../login.php';
                })
                .catch(error => {
                    console.error('Logout error:', error);
                    logoutAllSpinner.classList.add('d-none');
                    showToast('Error logging out devices: ' + (error.message || 'Unknown error'), 'danger');
                    logoutAllBtn.disabled = false;
                });
        };

        modal.show();
    });

    function terminateSession(sessionId, sessionElement) {
        const btn = sessionElement.querySelector('.terminate-btn');
        const spinner = sessionElement.querySelector(`#spinner-${sessionId}`);

        btn.disabled = true;
        spinner.classList.remove('d-none');

        fetch('../account/backend/terminate_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `session_id=${encodeURIComponent(sessionId)}`,
            credentials: 'include'
        })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                } else {
                    const text = await response.text();
                    throw new Error(text || 'Invalid response from server');
                }
            })
            .then(data => {
                if (data.success) {
                    if (data.is_current_session) {
                        window.location.href = '../login.php';
                        return;
                    }

                    sessionElement.remove();
                    allSessions = allSessions.filter(s => s.session_id !== sessionId);
                    sessionsCount.textContent = `${allSessions.length} active session${allSessions.length !== 1 ? 's' : ''}`;
                    showToast('Device logged out successfully', 'success');
                } else {
                    throw new Error(data.error || 'Failed to log out device');
                }
            })
            .catch(error => {
                console.error('Terminate session error:', error);
                showToast('Error logging out device: ' + error.message, 'danger');
                btn.disabled = false;
                spinner.classList.add('d-none');
            });
    }

  function showToast(message, type) {
        const toast = document.createElement('div');
        
        toast.className = `toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 start-50 translate-middle-x mb-3`;
        
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        document.body.appendChild(toast);

        const toastOptions = {
            autohide: true,
            delay: 2000
        };
        const bsToast = new bootstrap.Toast(toast, toastOptions);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', function () {
            toast.remove();
        });
    }
});