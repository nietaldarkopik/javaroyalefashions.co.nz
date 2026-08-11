// NZ Product Catalog — shared front-end behaviors
// Adapted from the ANYAM/benangjarum-style static template. Anything that
// used to fake cart/checkout state in JS (quick-add counters, a client-side
// "submit" that redirected without hitting a server) has been removed —
// those actions are now real <form> submits to Laravel routes, so the
// browser's native submit must be allowed to happen.

document.addEventListener('DOMContentLoaded', () => {

  /* ---------- Mobile nav drawer ---------- */
  const menuToggle = document.querySelector('.menu-toggle');
  const mobileNav = document.querySelector('.mobile-nav');
  const mobileBackdrop = document.querySelector('.mobile-nav-backdrop');
  const mobileClose = document.querySelector('.mobile-nav-close');

  function openMobileNav(){
    mobileNav && mobileNav.classList.add('open');
    mobileBackdrop && mobileBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeMobileNav(){
    mobileNav && mobileNav.classList.remove('open');
    mobileBackdrop && mobileBackdrop.classList.remove('open');
    document.body.style.overflow = '';
  }
  menuToggle && menuToggle.addEventListener('click', openMobileNav);
  mobileClose && mobileClose.addEventListener('click', closeMobileNav);
  mobileBackdrop && mobileBackdrop.addEventListener('click', closeMobileNav);

  // Mobile accordion submenus
  document.querySelectorAll('.m-nav-item > .m-nav-link').forEach(link => {
    link.addEventListener('click', () => {
      const item = link.closest('.m-nav-item');
      const wasOpen = item.classList.contains('open');
      document.querySelectorAll('.m-nav-item').forEach(i => i.classList.remove('open'));
      if (!wasOpen) item.classList.add('open');
    });
  });

  /* ---------- Cart drawer (real cart contents, rendered server-side) ---------- */
  const cartDrawer = document.querySelector('.cart-drawer');
  const cartBackdrop = document.querySelector('.drawer-backdrop');
  const cartOpenBtns = document.querySelectorAll('[data-cart-open]');
  const cartCloseBtn = document.querySelector('.cart-close');

  function openCart(){
    cartDrawer && cartDrawer.classList.add('open');
    cartBackdrop && cartBackdrop.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeCart(){
    cartDrawer && cartDrawer.classList.remove('open');
    cartBackdrop && cartBackdrop.classList.remove('open');
    document.body.style.overflow = '';
  }
  cartOpenBtns.forEach(btn => btn.addEventListener('click', (e) => { e.preventDefault(); openCart(); }));
  cartCloseBtn && cartCloseBtn.addEventListener('click', closeCart);
  cartBackdrop && cartBackdrop.addEventListener('click', closeCart);

  /* ---------- Promo popup (decorative email capture — no backend) ---------- */
  const promoPopup = document.querySelector('.promo-popup');
  const promoBackdrop = document.querySelector('.promo-popup-backdrop');
  const promoClose = document.querySelector('.promo-popup-close');
  const promoSkip = document.querySelector('[data-promo-skip]');
  const promoForm = document.querySelector('.promo-popup-form');

  function openPromo(){
    promoPopup && promoPopup.classList.add('open');
    promoBackdrop && promoBackdrop.classList.add('open');
  }
  function closePromo(){
    promoPopup && promoPopup.classList.remove('open');
    promoBackdrop && promoBackdrop.classList.remove('open');
    try { sessionStorage.setItem('promo_seen', '1'); } catch (err) {}
  }
  if (promoPopup && !sessionStorage.getItem('promo_seen')) {
    setTimeout(openPromo, 2500);
  }
  promoClose && promoClose.addEventListener('click', closePromo);
  promoBackdrop && promoBackdrop.addEventListener('click', closePromo);
  promoSkip && promoSkip.addEventListener('click', (e) => { e.preventDefault(); closePromo(); });
  if (promoForm) {
    promoForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const btn = promoForm.querySelector('button');
      btn.textContent = 'Thanks!';
      setTimeout(closePromo, 900);
    });
  }

  /* ---------- Horizontal carousels (drag to scroll + arrows) ---------- */
  document.querySelectorAll('.carousel-wrap').forEach(wrap => {
    const track = wrap.querySelector('.carousel-track');
    if (!track) return;
    const prev = wrap.querySelector('[data-carousel-prev]');
    const next = wrap.querySelector('[data-carousel-next]');
    const step = () => track.querySelector(':scope > *')?.getBoundingClientRect().width + 20 || 300;

    prev && prev.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    next && next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));

    let isDown = false, startX = 0, scrollLeft = 0;
    track.addEventListener('mousedown', (e) => {
      isDown = true;
      track.classList.add('dragging');
      startX = e.pageX - track.offsetLeft;
      scrollLeft = track.scrollLeft;
    });
    ['mouseleave', 'mouseup'].forEach(evt => track.addEventListener(evt, () => {
      isDown = false;
      track.classList.remove('dragging');
    }));
    track.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - track.offsetLeft;
      track.scrollLeft = scrollLeft - (x - startX) * 1.4;
    });
  });

  /* ---------- Search overlay ---------- */
  const searchOverlay = document.querySelector('.search-overlay');
  const searchOpenBtns = document.querySelectorAll('[data-search-open]');
  const searchCloseBtn = document.querySelector('.search-close');

  searchOpenBtns.forEach(btn => btn.addEventListener('click', (e) => {
    e.preventDefault();
    searchOverlay && searchOverlay.classList.toggle('open');
    if (searchOverlay && searchOverlay.classList.contains('open')) {
      setTimeout(() => { const inp = searchOverlay.querySelector('input'); inp && inp.focus(); }, 300);
    }
  }));
  searchCloseBtn && searchCloseBtn.addEventListener('click', () => searchOverlay.classList.remove('open'));

  /* ---------- Wishlist toggle (visual only — no persistence) ---------- */
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const icon = btn.querySelector('i');
      if (!icon) return;
      const active = icon.classList.toggle('fa-solid');
      icon.classList.toggle('fa-regular', !active);
      btn.classList.toggle('is-active', active);
    });
  });

  /* ---------- Gallery thumbnail swap (product page) ---------- */
  const thumbs = document.querySelectorAll('.gallery-thumbs .thumb');
  const mainImg = document.getElementById('main-product-image');
  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
      thumbs.forEach(t => t.classList.remove('active'));
      thumb.classList.add('active');
      if (mainImg && thumb.dataset.fullImage) {
        mainImg.src = thumb.dataset.fullImage;
      }
    });
  });

  /* ---------- Quantity stepper (drives a real number input) ---------- */
  document.querySelectorAll('.qty-stepper').forEach(stepper => {
    const input = stepper.querySelector('input[type="number"]');
    if (!input) return;
    const max = parseInt(input.getAttribute('max'), 10) || 99;

    stepper.querySelector('.qty-minus')?.addEventListener('click', () => {
      input.value = Math.max(1, (parseInt(input.value, 10) || 1) - 1);
    });
    stepper.querySelector('.qty-plus')?.addEventListener('click', () => {
      input.value = Math.min(max, (parseInt(input.value, 10) || 1) + 1);
    });
  });

  /* ---------- Accordion (product page / static pages) ---------- */
  document.querySelectorAll('.acc-head').forEach(head => {
    head.addEventListener('click', () => {
      const item = head.closest('.acc-item');
      const wasOpen = item.classList.contains('open');
      item.parentElement.querySelectorAll('.acc-item').forEach(i => i.classList.remove('open'));
      if (!wasOpen) item.classList.add('open');
    });
  });

  /* ---------- Filter chips (product listing — real links, active state is server-rendered) ---------- */
  // No JS needed: chips are real <a href> links and the "active" class is
  // set from the current request's filters when the page renders.

  /* ---------- Newsletter form (decorative — no backend) ---------- */
  const newsForm = document.querySelector('.news-form');
  if (newsForm) {
    newsForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const input = newsForm.querySelector('input');
      const btn = newsForm.querySelector('button');
      btn.textContent = 'Thanks!';
      input.value = '';
      setTimeout(() => { btn.textContent = 'Subscribe'; }, 2000);
    });
  }

  /* ---------- Hero slideshow ---------- */
  const slides = document.querySelectorAll('.hero-slide');
  const dotsWrap = document.querySelector('.hero-dots');
  if (slides.length > 1) {
    let current = 0;
    slides.forEach((s, i) => {
      const dot = document.createElement('button');
      if (i === 0) dot.classList.add('active');
      dot.addEventListener('click', () => goTo(i));
      dotsWrap && dotsWrap.appendChild(dot);
    });
    const dots = dotsWrap ? dotsWrap.querySelectorAll('button') : [];

    function goTo(index){
      slides[current].classList.remove('active');
      dots[current] && dots[current].classList.remove('active');
      current = index;
      slides[current].classList.add('active');
      dots[current] && dots[current].classList.add('active');
    }
    setInterval(() => { goTo((current + 1) % slides.length); }, 5000);
  }

  /* ---------- Checkout: shipping area radio cards + live totals ---------- */
  document.querySelectorAll('.radio-card-head input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', () => {
      const name = radio.name;
      document.querySelectorAll('.radio-card').forEach(card => {
        const input = card.querySelector(`input[name="${name}"]`);
        if (input) card.classList.toggle('active', input.checked);
      });
      if (name === 'area') updateCheckoutTotals();
    });
  });

  function updateCheckoutTotals(){
    const subtotalEl = document.getElementById('sum-subtotal');
    const shippingEl = document.getElementById('sum-shipping');
    const totalEl = document.getElementById('sum-total');
    if (!subtotalEl || !shippingEl || !totalEl) return;

    const checkedArea = document.querySelector('input[name="area"]:checked');
    const shippingCost = checkedArea ? parseFloat(checkedArea.closest('.radio-card').dataset.rate || '0') : 0;
    const subtotal = parseFloat(subtotalEl.dataset.value || '0');

    shippingEl.textContent = '$' + shippingCost.toFixed(2);
    totalEl.textContent = '$' + (subtotal + shippingCost).toFixed(2);
  }

  /* ---------- Scroll reveal ---------- */
  const revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12 });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('show'));
  }

});
