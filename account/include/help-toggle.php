<div class="era-fab-container">
    <div class="era-fab-menu">
        <a href="<?php echo BASE_URL; ?>/account/emergency-help" class="era-fab-item era-fab-emergency">
            <i class="fas fa-bolt"></i>
            <span class="era-fab-label">Emergency Road Assistance</span>
        </a>
        <a href="<?php echo BASE_URL; ?>/account/chatbot" class="era-fab-item era-fab-chatbot">
            <img src="<?php echo BASE_URL; ?>/assets/img/chatbot/chatbot.png" alt="AI Chatbot" style="width: 24px; height: 24px;">
            <span class="era-fab-label">AI Chatbot</span>
        </a>
    </div>
    <span class="era-fab-onboarding-label" id="fabOnboardingLabel">Need Help? Tap here</span>
    <button class="era-fab-toggle"><i class="fas fa-plus"></i></button>
</div>

<style>
    :root {
        --primary-color: #ffc107;
        --dark-gray: #1F2937;
        --danger-color: #dc3545;
    }

    .era-fab-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1050;
    }

    .era-fab-toggle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        border: none;
        transition: transform 0.3s ease, background-color 0.3s ease;
        cursor: pointer;
    }

    .era-fab-container.active .era-fab-toggle {
        transform: rotate(45deg);
        background-color: var(--dark-gray);
        color: white;
    }

    .era-fab-menu {
        position: absolute;
        bottom: 70px;
        right: 5px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    }

    .era-fab-item {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: white;
        color: var(--dark-gray);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        text-decoration: none;
        transition: all 0.3s ease;
        opacity: 0;
        transform: scale(0);
        pointer-events: none;
    }

    .era-fab-container.active .era-fab-item {
        opacity: 1;
        transform: scale(1);
        pointer-events: auto;
    }

    .era-fab-item.era-fab-emergency {
        background-color: var(--danger-color);
        color: white;
        animation: era-fab-pulse 2s infinite;
    }

    .era-fab-item.era-fab-chatbot {
        background-color: var(--primary-color);
    }

    @keyframes era-fab-pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    .era-fab-container.active .era-fab-item:nth-child(1) {
        transition-delay: 0.2s;
    }

    .era-fab-container.active .era-fab-item:nth-child(2) {
        transition-delay: 0.1s;
    }

    .era-fab-item .era-fab-label {
        position: absolute;
        right: 60px;
        background: var(--dark-gray);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .era-fab-label.show {
        opacity: 1;
    }

    .era-fab-onboarding-label {
        position: absolute;
        bottom: 15px;
        right: 80px;
        background-color: var(--dark-gray);
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 14px;
        white-space: nowrap;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transform: translateX(10px);
        transition: opacity 0.5s ease, transform 0.5s ease;
        pointer-events: none;
    }

    .era-fab-onboarding-label.show {
        opacity: 1;
        transform: translateX(0);
    }

    .era-fab-container {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1050;
        transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55), opacity 0.3s ease-in-out;
    }

    .era-fab-container.fab-hidden {
        opacity: 0;
        transform: translateY(150%);
        pointer-events: none;
    }
</style>

<script>
    (function() {
        document.addEventListener('DOMContentLoaded', () => {
            const fabContainer = document.querySelector('.era-fab-container');
            const fabToggle = document.querySelector('.era-fab-toggle');
            const fabOnboardingLabel = document.getElementById('fabOnboardingLabel');

            if (fabOnboardingLabel) {
                setTimeout(() => {
                    fabOnboardingLabel.classList.add('show');
                }, 1000);
                setTimeout(() => {
                    fabOnboardingLabel.classList.remove('show');
                }, 3000);
            }

            if (fabToggle && fabContainer) {
                fabToggle.addEventListener('click', () => {
                    fabContainer.classList.toggle('active');

                    if (fabContainer.classList.contains('active')) {
                        const emergencyLabel = document.querySelector('.era-fab-emergency .era-fab-label');
                        const chatbotLabel = document.querySelector('.era-fab-chatbot .era-fab-label');

                        setTimeout(() => {
                            if (emergencyLabel) emergencyLabel.classList.add('show');
                            if (chatbotLabel) chatbotLabel.classList.add('show');
                        }, 300);

                        setTimeout(() => {
                            if (emergencyLabel) emergencyLabel.classList.remove('show');
                            if (chatbotLabel) chatbotLabel.classList.remove('show');
                        }, 2300);
                    }
                });
            }

            if (fabContainer) {
                let lastScrollTop = 0;
                const scrollThreshold = 100;

                window.addEventListener('scroll', function() {
                    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                    if (Math.abs(scrollTop - lastScrollTop) < 20) {
                        return;
                    }

                    if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
                        fabContainer.classList.add('fab-hidden');
                        fabContainer.classList.remove('active');
                    } else {
                        fabContainer.classList.remove('fab-hidden');
                    }

                    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                }, false);
            }
        });
    })();
</script>