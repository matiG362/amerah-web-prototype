// ============ AMERAH PALACE — shared interactions ============
document.addEventListener('DOMContentLoaded', () => {

  /* ---- solid header on scroll ---- */
  const header = document.querySelector('.site-header');
  const toggleHeader = () => {
    if (!header) return;
    if (window.scrollY > 60) header.classList.add('solid');
    else header.classList.remove('solid');
  };
  toggleHeader();
  window.addEventListener('scroll', toggleHeader, { passive: true });

  /* ---- mobile nav ---- */
  const navToggle = document.querySelector('.nav-toggle');
  const mobileNav = document.querySelector('.mobile-nav');
  const mobileClose = document.querySelector('.mobile-nav-close');
  if (navToggle && mobileNav) {
    navToggle.addEventListener('click', () => {
      mobileNav.classList.add('open');
      document.body.style.overflow = 'hidden';
    });
  }
  if (mobileClose && mobileNav) {
    mobileClose.addEventListener('click', () => {
      mobileNav.classList.remove('open');
      document.body.style.overflow = '';
    });
  }
  document.querySelectorAll('.mobile-nav a').forEach(a => {
    a.addEventListener('click', () => {
      mobileNav.classList.remove('open');
      document.body.style.overflow = '';
    });
  });

  /* ---- reveal on scroll ---- */
  const revealEls = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window && revealEls.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add('in'));
  }

  /* ---- accordion (FAQ) ---- */
  document.querySelectorAll('.accordion-item').forEach(item => {
    const q = item.querySelector('.accordion-q');
    const a = item.querySelector('.accordion-a');
    if (!q || !a) return;
    q.addEventListener('click', () => {
      const isOpen = item.classList.contains('open');
      document.querySelectorAll('.accordion-item.open').forEach(openItem => {
        if (openItem !== item) {
          openItem.classList.remove('open');
          openItem.querySelector('.accordion-a').style.maxHeight = null;
        }
      });
      if (isOpen) {
        item.classList.remove('open');
        a.style.maxHeight = null;
      } else {
        item.classList.add('open');
        a.style.maxHeight = a.scrollHeight + 'px';
      }
    });
  });

/* ---- live inquiry form submission via FormSubmit ---- */
  var form = document.querySelector('#inquiry-form');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var submitBtn = form.querySelector('button[type="submit"]');
      var originalText = submitBtn.textContent;
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending Inquiry...';

      var formData = new FormData(form);
      
      // Formatting options for luxury delivery
      formData.append('_subject', 'New Reservation Inquiry — Amerah Palace Hotel');
      formData.append('_template', 'table');
      formData.append('_captcha', 'false');

      fetch('https://formsubmit.co/ajax/55756a6c88219acd657df52df18b26c7', {
        method: 'POST',
        body: formData
      })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        var success = document.querySelector('.form-success');
        form.style.display = 'none';
        if (success) success.classList.add('show');
        window.scrollTo({ top: form.offsetTop - 140, behavior: 'smooth' });
      })
      .catch(function (error) {
        alert('Please reach us directly at reservations@amerahhotels.com or call +251 94 883 1748');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
      });
    });
  }
  /* ---- pre-fill room interest from query param (?room=) ---- */
  const params = new URLSearchParams(window.location.search);
  const roomParam = params.get('room');
  const roomSelect = document.querySelector('#room-interest');
  if (roomParam && roomSelect) {
    const match = Array.from(roomSelect.options).find(
      o => o.value.toLowerCase() === roomParam.toLowerCase()
    );
    if (match) roomSelect.value = match.value;
  }

});
