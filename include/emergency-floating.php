<div class="floating-help-container" id="floatingHelp">
  <div class="help-text" id="helpText">Need Emergency Assistance?</div>
  <a href="<?php echo BASE_URL; ?>/emergency-help" class="auto-repair-help-btn" title="Auto Repair Help">
     <i class="fas fa-exclamation-triangle"></i>
  </a>
</div>

<style>
  .floating-help-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 9999;
    transition: transform 0.4s ease, opacity 0.4s ease;
  }

  .floating-help-container.hide {
    opacity: 0;
    transform: translateY(100px);
    pointer-events: none;
  }

  .auto-repair-help-btn i {
    font-size: 20px;
  }

  .auto-repair-help-btn {
    background: linear-gradient(to right, #ff0000, #cc0000);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 28px;
    position: relative;
    z-index: 1;
    animation: glowPulse 2s infinite ease-in-out;
    transition: background-color 0.3s ease;
    box-shadow: 0 0 10px rgba(255, 0, 0, 0.7);
  }

  .auto-repair-help-btn:hover {
    background: linear-gradient(to right, #ff0000, #cc0000);
    cursor: pointer;
  }

  @keyframes glowPulse {
    0% { box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.7); }
    70% { box-shadow: 0 0 15px 10px rgba(255, 0, 0, 0); }
    100% { box-shadow: 0 0 0 0 rgba(255, 0, 0, 0); }
  }

  .help-text {
    background-color: #333;
    color: white;
    padding: 10px 15px;
    border-radius: 20px;
    font-size: 14px;
    white-space: nowrap;
    opacity: 0;
    transform: translateX(10px);
    transition: opacity 0.5s ease, transform 0.5s ease;
  }

  .help-text.show {
    opacity: 1;
    transform: translateX(0);
  }
</style>

<script>
  window.addEventListener("load", function () {
    const text = document.getElementById("helpText");
    text.classList.add("show");
    setTimeout(() => text.classList.remove("show"), 2000);
  });

  let lastScrollTop = 0;
  const floatingHelp = document.getElementById("floatingHelp");

  window.addEventListener("scroll", function () {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

    if (currentScroll > lastScrollTop) {
      floatingHelp.classList.add("hide");
    } else {
      floatingHelp.classList.remove("hide");
    }

    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
  });
</script>
