
<audio id="emergencySound">
  <source src="../assets/sound/emergency.mp3" type="audio/mpeg">
  Your browser does not support the audio element.
</audio>

<div id="emergencyModalsContainer"></div>

<script>
function showEmergencyModal(emergency) {
    const modalId = 'emergencyModal_' + emergency.id;

    const modalHTML = `
        <div id="${modalId}" class="emergency-modal show">
            <div class="emergency-modal-content">
                <span class="close-btn" onclick="closeEmergencyModal('${modalId}')">&times;</span>
                <h4>🚨 New Emergency Request</h4>
                <p><strong>Customer:</strong> ${escapeHtml(emergency.fullname)}</p>
                <p><strong>Issue:</strong> ${escapeHtml(emergency.issue_description)}</p>
                <p><strong>Received:</strong> ${formatDateTime(emergency.created_at)}</p>
                <button onclick="window.location.href='emergency-request.php'">View Requests</button>
            </div>
        </div>
    `;

    document.getElementById('emergencyModalsContainer').insertAdjacentHTML('beforeend', modalHTML);

    const sound = document.getElementById('emergencySound');
    if (sound) {
        try {
            sound.currentTime = 0;
            sound.play().catch(e => console.log('Audio play failed:', e));
        } catch (e) {
            console.log('Audio error:', e);
        }
    }
}

function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
    });
}

function closeEmergencyModal(modalId) {
    const sound = document.getElementById('emergencySound');
    if (sound) {
        sound.pause();
        sound.currentTime = 0;
    }

    const modal = document.getElementById(modalId);
    if (modal) {
        modal.remove();
    }
}

function checkForEmergencies() {
    fetch('backend/check_emergencies.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.emergencies && data.emergencies.length > 0) {
                data.emergencies.forEach(emergency => {
                    if (!document.getElementById('emergencyModal_' + emergency.id)) {
                        showEmergencyModal(emergency);
                    }
                });
            }
        })
        .catch(error => console.error('Error checking for emergencies:', error));
}

setInterval(checkForEmergencies, 2000);

window.addEventListener('DOMContentLoaded', checkForEmergencies);
</script>

<style>
.emergency-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.5);
  z-index: 10000;
  display: flex;
  align-items: center;
  justify-content: center;
}

.emergency-modal-content {
  background: #fff;
  border: 3px solid #d60000;
  padding: 30px;
  border-radius: 15px;
  width: 400px;
  text-align: center;
  animation: shakeModal 0.4s ease-in-out;
  box-shadow: 0 0 15px rgba(214, 0, 0, 0.4);
  position: relative;
}

.emergency-modal-content h4 {
  color: #d60000;
  margin-bottom: 15px;
}

.emergency-modal-content p {
  margin: 8px 0;
}

.emergency-modal-content button {
  background-color: #d60000;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  cursor: pointer;
  margin-top: 15px;
}

.emergency-modal-content .close-btn {
  position: absolute;
  right: 15px;
  top: 10px;
  font-size: 20px;
  color: #888;
  cursor: pointer;
}

.emergency-modal:nth-child(1) {
  z-index: 10000;
}

.emergency-modal:nth-child(2) {
  z-index: 9999;
  transform: translate(20px, 20px);
}

.emergency-modal:nth-child(3) {
  z-index: 9998;
  transform: translate(40px, 40px);
}

@keyframes shakeModal {
  0% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  50% { transform: translateX(5px); }
  75% { transform: translateX(-5px); }
  100% { transform: translateX(0); }
}
</style>
