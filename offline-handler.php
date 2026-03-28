<style>
    #offline-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: #f8f9fa;
        display: none;
        justify-content: center;
        align-items: center;
        text-align: center;
        z-index: 9999;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    #offline-overlay.visible {
        display: flex;
        opacity: 1;
    }

    .offline-message-box {
        padding: 2rem;
        max-width: 450px;
        width: 90%;
        transform: translateY(15px);
        transition: transform 0.4s ease, opacity 0.4s ease;
        opacity: 0;
    }

    #offline-overlay.visible .offline-message-box {
        transform: translateY(0);
        opacity: 1;
    }

    .offline-icon-container {
        margin-bottom: 2.5rem;
        height: 140px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .offline-icon-container svg {
        width: 100%;
        height: 100%;
    }

    .gear-slow-spin {
        animation: spin 6s linear infinite;
        transform-origin: center;
    }

    .gear-fast-spin {
        animation: spin 3s linear infinite reverse;
        transform-origin: center;
    }

    .piston-animation {
        animation: piston 2s ease-in-out infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @keyframes piston {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(5px);
        }
    }

    .spark-animation {
        animation: spark 1.5s linear infinite;
        opacity: 0;
    }

    @keyframes spark {

        0%,
        100% {
            opacity: 0;
            transform: scale(0.5);
        }

        50% {
            opacity: 1;
            transform: scale(1.2);
        }
    }


    .offline-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .offline-text {
        color: #7f8c8d;
        font-size: 1.05rem;
        line-height: 1.7;
    }

    .no-scroll {
        overflow: hidden;
    }
</style>

<div id="offline-overlay">
    <div class="offline-message-box">
        <div id="connection-icon-container" class="offline-icon-container">
            <!-- SVG illustrations will be injected here by JavaScript -->
        </div>
        <h2 id="connection-title" class="offline-title">Checking Connection...</h2>
        <p id="connection-text" class="offline-text">Just a moment while we check your connection status.</p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const offlineOverlay = document.getElementById('offline-overlay');
        const body = document.body;
        const iconContainer = document.getElementById('connection-icon-container');
        const connectionTitle = document.getElementById('connection-title');
        const connectionText = document.getElementById('connection-text');

        const svgOffline = `
            <svg viewBox="0 0 200 120" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="plugBodyGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#5D6D7E;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#34495E;stop-opacity:1" />
                    </linearGradient>
                    <linearGradient id="prongGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#D5D8DC;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#ABB2B9;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#D5D8DC;stop-opacity:1" />
                    </linearGradient>
                </defs>
                <!-- Ground Shadow -->
                <ellipse cx="100" cy="110" rx="70" ry="5" fill="#e0e0e0"/>
                <!-- Car Body -->
                <path d="M 25,105 C 15,105 10,95 15,85 L 35,50 C 40,40 50,35 70,35 L 130,35 C 150,35 160,40 165,50 L 185,85 C 190,95 185,105 175,105 Z" fill="#ffffff" stroke="#34495e" stroke-width="4" stroke-linejoin="round"/>
                <!-- Windows -->
                <path d="M 70,40 L 130,40 C 140,40 145,45 150,50 L 80,50 C 75,45 70,40 70,40 Z" fill="#aed6f1" stroke="#34495e" stroke-width="2"/>
                <path d="M 75,50 L 50,50 L 40,80 L 75,80 Z" fill="#aed6f1" stroke="#34495e" stroke-width="2"/>
                <path d="M 125,50 L 150,50 L 160,80 L 125,80 Z" fill="#aed6f1" stroke="#34495e" stroke-width="2"/>
                <!-- Wheels -->
                <circle cx="60" cy="100" r="15" fill="#ffffff" stroke="#34495e" stroke-width="4"/>
                <circle cx="140" cy="100" r="15" fill="#ffffff" stroke="#34495e" stroke-width="4"/>
                <circle cx="60" cy="100" r="5" fill="#34495e"/>
                <circle cx="140" cy="100" r="5" fill="#34495e"/>
                <!-- Disconnected Plug - Realistic -->
                <g transform="translate(130, 0)">
                    <!-- Cord -->
                    <path d="M 20,45 C 30,45 40,35 50,35" stroke="#566573" stroke-width="5" fill="none" stroke-linecap="round"/>
                    <!-- Plug Body -->
                    <path d="M 48,22 H 68 V 38 H 48 Z" fill="url(#plugBodyGradient)" rx="2"/>
                    <path d="M 48,22 L 52,18 H 72 L 68,22 Z" fill="#34495E"/>
                    <!-- Prongs -->
                    <rect x="52" y="10" width="4" height="8" fill="url(#prongGradient)" rx="1"/>
                    <rect x="64" y="10" width="4" height="8" fill="url(#prongGradient)" rx="1"/>
                    <!-- Sparks -->
                    <path class="spark-animation" d="M 40,25 L 45,20 L 50,25 L 45,30 Z" fill="#f1c40f" style="animation-delay: 0s;"/>
                    <path class="spark-animation" d="M 35,35 L 40,30 L 45,35 L 40,40 Z" fill="#f1c40f" style="animation-delay: 0.5s;"/>
                </g>
            </svg>`;

        const svgSlow = `
<svg viewBox="0 0 250 150" xmlns="http://www.w3.org/2000/svg">

  <rect x="20" y="30" width="210" height="90" rx="10" fill="#7f8c8d" stroke="#2c3e50" stroke-width="4"/>

  <rect x="40" y="100" width="170" height="10" rx="3" fill="#34495e"/>
  
  <g class="piston-animation" style="animation-delay: 0s;">
    <rect x="40" y="40" width="30" height="40" fill="#ecf0f1" stroke="#95a5a6" stroke-width="2"/>
    <line x1="55" y1="80" x2="55" y2="100" stroke="#bdc3c7" stroke-width="4"/>
  </g>
  <g class="piston-animation" style="animation-delay: 0.5s;">
    <rect x="95" y="40" width="30" height="40" fill="#ecf0f1" stroke="#95a5a6" stroke-width="2"/>
    <line x1="110" y1="80" x2="110" y2="100" stroke="#bdc3c7" stroke-width="4"/>
  </g>
  <g class="piston-animation" style="animation-delay: 1s;">
    <rect x="150" y="40" width="30" height="40" fill="#ecf0f1" stroke="#95a5a6" stroke-width="2"/>
    <line x1="165" y1="80" x2="165" y2="100" stroke="#bdc3c7" stroke-width="4"/>
  </g>

  <g transform="translate(60, 125)">
    <circle r="15" fill="#ffffff" stroke="#2980b9" stroke-width="3"/>
    <path class="gear-slow-spin" d="M 0 -12 L 3 -9 L 9 -10 L 10 -6 L 12 0 L 10 6 L 9 10 L 3 9 L 0 12 L -3 9 L -9 10 L -10 6 L -12 0 L -10 -6 L -9 -10 L -3 -9 Z" fill="#2980b9"/>
    <circle r="3" fill="#ffffff"/>
  </g>

  <g transform="translate(190, 125)">
    <circle r="15" fill="#ffffff" stroke="#2980b9" stroke-width="3"/>
    <path class="gear-fast-spin" d="M 0 -12 L 3 -9 L 9 -10 L 10 -6 L 12 0 L 10 6 L 9 10 L 3 9 L 0 12 L -3 9 L -9 10 L -10 6 L -12 0 L -10 -6 L -9 -10 L -3 -9 Z" fill="#2980b9"/>
    <circle r="3" fill="#ffffff"/>
  </g>

  <rect x="20" y="20" width="30" height="10" fill="#95a5a6" rx="3"/>
  <rect x="200" y="20" width="30" height="10" fill="#95a5a6" rx="3"/>
</svg>
`;

        const updateConnectionStatus = () => {
            if (!navigator.onLine) {
                iconContainer.innerHTML = svgOffline;
                connectionTitle.textContent = 'Signal Disconnected';
                connectionText.textContent = "We've lost the signal. Please check your network connection to get back on the road.";
                offlineOverlay.classList.add('visible');
                body.classList.add('no-scroll');
                return;
            }

            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            if (connection && (connection.effectiveType === 'slow-2g' || connection.effectiveType === '2g')) {
                iconContainer.innerHTML = svgSlow;
                connectionTitle.textContent = 'Engine Running Slow';
                connectionText.textContent = 'Your connection is like an engine needing a tune-up. Some pages might take a moment longer to load.';
                offlineOverlay.classList.add('visible');
                body.classList.add('no-scroll');
            } else {
                offlineOverlay.classList.remove('visible');
                body.classList.remove('no-scroll');
            }
        };

        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);

        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (connection) {
            connection.addEventListener('change', updateConnectionStatus);
        }

        setTimeout(updateConnectionStatus, 100);
    });
</script>