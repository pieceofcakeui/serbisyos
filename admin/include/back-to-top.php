<button id="floatScrollBtn" class="float-scroll-btn" aria-label="Scroll">
  <span class="icon" id="btnIcon" aria-hidden="true">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round">
      <polyline points="18 15 12 9 6 15"></polyline>
    </svg>
  </span>
  <span class="btn-text" id="btnText">Back to top</span>
</button>

<style>
.float-scroll-btn{
  position: fixed;
  right: 20px;
  bottom: 24px;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: .55rem .9rem;
  border-radius: 12px;
  border: 2px solid rgba(0,0,0,.08);
  background: #fff;
  box-shadow: 0 6px 18px rgba(0,0,0,.08);
  cursor: pointer;
  transform: translateY(10px);
  transition: transform .18s ease, opacity .18s ease, box-shadow .12s ease;
  opacity: 0;
  pointer-events: none;
  color: #212529;
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  font-size: 14px;
  z-index: 9999;
}

.float-scroll-btn.show{
  opacity: 1;
  pointer-events: auto;
  transform: translateY(0);
}

.float-scroll-btn:hover{
  box-shadow: 0 10px 28px rgba(0,0,0,.14);
  transform: translateY(-2px);
  border-color: rgba(0,0,0,.12);
  color: #0d6efd;
}

.float-scroll-btn .icon{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  width: 22px;
  height: 22px;
}

@media (max-width:420px){
  .float-scroll-btn .btn-text{ display:none; }
}
</style>

<script>
(function(){
  const btn = document.getElementById('floatScrollBtn');
  const iconWrap = document.getElementById('btnIcon');
  const text = document.getElementById('btnText');

  let lastScroll = window.scrollY || document.documentElement.scrollTop;
  let ticking = false;
  const showAfter = 150;

  const upSVG = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>';
  const downSVG = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';

  function updateButtonOnScroll(){
    const current = window.scrollY || document.documentElement.scrollTop;
    const direction = (current > lastScroll) ? 'down' : (current < lastScroll) ? 'up' : 'none';

    if (current > showAfter) {
      btn.classList.add('show');
    } else {
      btn.classList.remove('show');
    }

    if (direction === 'down') {
      iconWrap.innerHTML = downSVG;
      text.textContent = 'Scroll to bottom';
      btn.dataset.action = 'bottom';
    } else if (direction === 'up') {
      iconWrap.innerHTML = upSVG;
      text.textContent = 'Back to top';
      btn.dataset.action = 'top';
    }
    lastScroll = current <= 0 ? 0 : current;
    ticking = false;
  }

  window.addEventListener('scroll', function(){
    if (!ticking) {
      window.requestAnimationFrame(updateButtonOnScroll);
      ticking = true;
    }
  }, {passive: true});

  btn.addEventListener('click', function(){
    const act = btn.dataset.action || 'top';
    if (act === 'top') {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
      window.scrollTo({ top: document.documentElement.scrollHeight, behavior: 'smooth' });
    }
  });

  document.addEventListener('DOMContentLoaded', updateButtonOnScroll);
  updateButtonOnScroll();
})();
</script>
