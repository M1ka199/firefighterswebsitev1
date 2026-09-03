/**
 * FF Wulften am Harz - Hauptskript
 */
document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Menu Toggle
  const mobileBtn = document.getElementById('mobile-menu-btn');
  const mobileMenu = document.getElementById('mobile-menu');

  if (mobileBtn && mobileMenu) {
    mobileBtn.addEventListener('click', () => {
      const isExpanded = mobileBtn.getAttribute('aria-expanded') === 'true';
      mobileBtn.setAttribute('aria-expanded', !isExpanded);
      mobileMenu.classList.toggle('hidden');
    });
  }

  // 2. Mobile Dropdown Toggle fuer Kontakt
  const mobileDropdownBtn = document.getElementById('mobile-dropdown-btn');
  const mobileDropdownMenu = document.getElementById('mobile-dropdown-menu');

  if (mobileDropdownBtn && mobileDropdownMenu) {
    mobileDropdownBtn.addEventListener('click', (e) => {
      e.preventDefault();
      mobileDropdownMenu.classList.toggle('hidden');
    });
  }

  // 3. Header Scroll Effect
  /*const header = document.querySelector('header');
  if (header) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 40) {
        header.classList.add('shadow-2xl', 'bg-navy-dark/95');
        header.classList.remove('bg-navy-dark/85');
      } else {
        header.classList.remove('shadow-2xl', 'bg-navy-dark/95');
        header.classList.add('bg-navy-dark/85');
      }
    });
  }*/
});
