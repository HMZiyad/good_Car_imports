/**
 * Good Car Imports — Frontend JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
  // --- Header & Navigation ---
  const header = document.getElementById('site-header');
  const hamburger = document.getElementById('nav-hamburger');
  const mobileMenu = document.getElementById('mobile-menu');

  // Scroll effect for header
  window.addEventListener('scroll', () => {
    if (window.scrollY > 10) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });

  // --- Theme Toggle ---
  const themeToggleBtns = document.querySelectorAll('.theme-toggle-btn');
  themeToggleBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      
      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
    });
  });
  // Mobile menu toggle
  function closeMobileMenu() {
    if (hamburger && mobileMenu) {
      hamburger.classList.remove('open');
      mobileMenu.classList.remove('open');
      hamburger.setAttribute('aria-expanded', 'false');
      mobileMenu.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    }
  }

  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.contains('open');
      if (isOpen) {
        closeMobileMenu();
      } else {
        hamburger.classList.add('open');
        mobileMenu.classList.add('open');
        hamburger.setAttribute('aria-expanded', 'true');
        mobileMenu.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
      }
    });

    // Close menu when clicking a nav link
    const mobileNavLinks = mobileMenu.querySelectorAll('.mobile-nav-link, .mobile-cta-btn');
    mobileNavLinks.forEach(link => {
      link.addEventListener('click', closeMobileMenu);
    });

    // Close menu on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
        closeMobileMenu();
      }
    });

    // Close menu if screen resizes above mobile breakpoint
    window.addEventListener('resize', () => {
      if (window.innerWidth > 768 && mobileMenu.classList.contains('open')) {
        closeMobileMenu();
      }
    });
  }

  // --- Filter Sidebar (Mobile) ---
  const filterToggle = document.getElementById('filter-toggle');
  const filterSidebar = document.getElementById('filter-sidebar');
  
  if (filterToggle && filterSidebar) {
    filterToggle.addEventListener('click', () => {
      filterSidebar.classList.toggle('open');
      if (filterSidebar.classList.contains('open')) {
        filterToggle.innerHTML = '<span class="material-symbols-outlined">close</span> Close Filters';
      } else {
        filterToggle.innerHTML = '<span class="material-symbols-outlined">tune</span> Filters';
      }
    });
  }

  // Auto-submit filter form on checkbox change
  const filterForm = document.getElementById('filter-form');
  if (filterForm) {
    const checkboxes = filterForm.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => {
      cb.addEventListener('change', () => {
        // If clicking a grade pill, toggle the visual class
        const parent = cb.closest('.grade-pill');
        if (parent) {
          if (cb.checked) {
            parent.classList.add('active');
          } else {
            parent.classList.remove('active');
          }
        }
        filterForm.submit();
      });
    });
  }
});

// --- Form Submissions ---

// Contact & Booking Inquiry Form
async function submitInquiry(event) {
  event.preventDefault();
  
  const form = event.target;
  const submitBtn = form.querySelector('button[type="submit"]');
  const feedbackEl = document.getElementById('contact-feedback') || document.getElementById('form-feedback');
  
  // Disable button
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Sending...';
  
  try {
    const formData = new FormData(form);
    const response = await fetch('api/inquiry.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    feedbackEl.style.display = 'block';
    if (result.success) {
      feedbackEl.className = 'form-message success';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">check_circle</span> ${result.message}`;
      form.reset();
    } else {
      feedbackEl.className = 'form-message error';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> ${result.message}`;
    }
  } catch (error) {
    feedbackEl.style.display = 'block';
    feedbackEl.className = 'form-message error';
    feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> An error occurred. Please try again or contact us via WhatsApp.`;
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

// Pre-Order Form
async function submitPreOrder(event) {
  event.preventDefault();
  
  const form = event.target;
  const submitBtn = document.getElementById('submit-btn');
  const feedbackEl = document.getElementById('preorder-feedback');
  
  submitBtn.disabled = true;
  submitBtn.textContent = 'Submitting...';
  
  try {
    const formData = new FormData(form);
    formData.append('type', 'pre_order');
    
    const response = await fetch('api/inquiry.php', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    feedbackEl.style.display = 'block';
    if (result.success) {
      feedbackEl.className = 'form-message success';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">check_circle</span> ${result.message}`;
      // Hide form fields and show success message
      form.querySelectorAll('.form-row, .form-group').forEach(el => el.style.display = 'none');
      submitBtn.style.display = 'none';
      form.querySelector('.btn-outline').style.display = 'none';
      
      // Also update the step info
      const stepInfo = document.querySelector('#step-3 .form-step-info');
      stepInfo.innerHTML = '<h2>Request Received!</h2><p>Thank you for your pre-order request. Our team will review your requirements and contact you shortly with suitable options.</p>';
    } else {
      feedbackEl.className = 'form-message error';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> ${result.message}`;
    }
  } catch (error) {
    feedbackEl.style.display = 'block';
    feedbackEl.className = 'form-message error';
    feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> An error occurred. Please try again or contact us via WhatsApp.`;
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = 'Submit Pre-Order Request';
  }
}
