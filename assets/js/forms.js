/**
 * FF Wulften am Harz - Formularverarbeitung (Kontakt & Schnupperdienst)
 */
document.addEventListener('DOMContentLoaded', () => {
  const forms = document.querySelectorAll('.ajax-form');

  forms.forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const submitBtn = form.querySelector('button[type="submit"]');
      const feedbackBox = form.querySelector('.form-feedback');
      const originalBtnHtml = submitBtn ? submitBtn.innerHTML : '';

      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-navy-dark inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
          Wird übermittelt...
        `;
      }

      if (feedbackBox) {
        feedbackBox.classList.add('hidden');
        feedbackBox.innerHTML = '';
      }

      try {
        const formData = new FormData(form);
        const response = await fetch(form.action || window.location.href, {
          method: 'POST',
          body: formData,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await response.json();

        if (feedbackBox) {
          feedbackBox.classList.remove('hidden');
          if (data.success) {
            feedbackBox.className = 'form-feedback p-4 rounded-xl mb-6 bg-emerald-950/80 border border-emerald-500/50 text-emerald-200 text-sm font-medium shadow-lg backdrop-blur-md';
            feedbackBox.innerHTML = `
              <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <div>
                  <p class="font-bold text-white">${data.message || 'Vielen Dank! Ihre Nachricht wurde erfolgreich übermittelt.'}</p>
                  <p class="text-xs text-emerald-300 mt-0.5">Unser Team wird sich schnellstmöglich bei Ihnen melden.</p>
                </div>
              </div>
            `;
            form.reset();
          } else {
            feedbackBox.className = 'form-feedback p-4 rounded-xl mb-6 bg-alarm/20 border border-alarm/50 text-red-200 text-sm font-medium shadow-lg backdrop-blur-md';
            feedbackBox.innerHTML = `
              <div class="flex items-center gap-3">
                <span class="text-xl">⚠️</span>
                <div>
                  <p class="font-bold text-white">${data.message || 'Bitte prüfen Sie Ihre Eingaben.'}</p>
                </div>
              </div>
            `;
          }
          feedbackBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      } catch (err) {
        if (feedbackBox) {
          feedbackBox.classList.remove('hidden');
          feedbackBox.className = 'form-feedback p-4 rounded-xl mb-6 bg-alarm/20 border border-alarm/50 text-red-200 text-sm font-medium shadow-lg backdrop-blur-md';
          feedbackBox.innerHTML = '<p>Ein technischer Übertragungsfehler ist aufgetreten. Bitte versuchen Sie es erneut oder rufen Sie uns an.</p>';
        }
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalBtnHtml;
        }
      }
    });
  });
});
