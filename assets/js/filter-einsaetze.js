/**
 * FF Wulften am Harz - Interaktiver Filter fuer Einsaetze
 */
document.addEventListener('DOMContentLoaded', () => {
  const cards = document.querySelectorAll('.einsatz-card');
  const catButtons = document.querySelectorAll('[data-filter-cat]');
  const yearSelect = document.getElementById('filter-year');
  const searchInput = document.getElementById('filter-search');
  const countDisplay = document.getElementById('einsaetze-count');
  const emptyState = document.getElementById('einsaetze-empty');

  if (!cards.length) return;

  let currentCategory = 'all';
  let currentYear = 'all';
  let currentSearch = '';

  function applyFilters() {
    let visibleCount = 0;

    cards.forEach(card => {
      const cardCat = card.getAttribute('data-cat') || '';
      const cardYear = card.getAttribute('data-year') || '';
      const cardText = card.innerText.toLowerCase();

      const matchCat = (currentCategory === 'all' || cardCat === currentCategory);
      const matchYear = (currentYear === 'all' || cardYear === currentYear);
      const matchSearch = (!currentSearch || cardText.includes(currentSearch));

      if (matchCat && matchYear && matchSearch) {
        card.style.display = '';
        card.classList.remove('hidden');
        visibleCount++;
      } else {
        card.style.display = 'none';
        card.classList.add('hidden');
      }
    });

    if (countDisplay) {
      countDisplay.textContent = `Zeige ${visibleCount} von ${cards.length} Einsätzen`;
    }

    if (emptyState) {
      if (visibleCount === 0) {
        emptyState.classList.remove('hidden');
      } else {
        emptyState.classList.add('hidden');
      }
    }
  }

  // Kategorie Filter Klicks
  catButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      catButtons.forEach(b => {
        b.classList.remove('bg-sand', 'text-navy-dark', 'font-bold');
        b.classList.add('glass-tile', 'text-slate-300');
      });
      btn.classList.add('bg-sand', 'text-navy-dark', 'font-bold');
      btn.classList.remove('glass-tile', 'text-slate-300');

      currentCategory = btn.getAttribute('data-filter-cat');
      applyFilters();
    });
  });

  // Jahr Dropdown
  if (yearSelect) {
    yearSelect.addEventListener('change', (e) => {
      currentYear = e.target.value;
      applyFilters();
    });
  }

  // Suchfeld
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      currentSearch = e.target.value.trim().toLowerCase();
      applyFilters();
    });
  }

  applyFilters();
});
