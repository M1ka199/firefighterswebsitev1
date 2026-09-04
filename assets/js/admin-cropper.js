/**
 * Admin Image Cropper with Real-Time Live Preview
 * Uses Cropper.js for seamless cropping across all admin forms
 */
(function() {
  'use strict';

  let currentCropper = null;
  let currentFileInput = null;
  let currentHiddenInput = null;
  let currentPreviewImg = null;
  let currentAspectRatio = NaN;

  // Erstelle das Modal-DOM dynamisch, falls noch nicht vorhanden
  function ensureModal() {
    let modal = document.getElementById('admin-cropper-modal');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'admin-cropper-modal';
    modal.className = 'fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-6 overflow-y-auto';
    modal.innerHTML = `
      <div class="bg-white w-full max-w-5xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col my-auto max-h-[92vh]">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
          <div>
            <div class="flex items-center gap-2">
              <span class="w-2.5 h-2.5 rounded-full bg-sand"></span>
              <h3 class="text-base sm:text-lg font-extrabold text-navy uppercase tracking-tight">
                Bild zuschneiden & Ausrichtung
              </h3>
            </div>
            <p class="text-xs text-slate-500 mt-0.5 font-light">
              Verschiebe und zoome das Bild. Die Live-Vorschau rechts zeigt exakt das spätere Erscheinungsbild.
            </p>
          </div>
          <button type="button" id="cropper-btn-close" class="w-9 h-9 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold text-base transition">
            ✕
          </button>
        </div>

        <!-- Body: 2 Spalten (Links: Cropper, Rechts: Live-Vorschau) -->
        <div class="p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 overflow-y-auto flex-1 items-start">
          
          <!-- Spalte Links: Cropper Arbeitsfläche & Werkzeuge (8 Spalten) -->
          <div class="lg:col-span-8 flex flex-col space-y-3">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
              <span>✂️</span> Bildausschnitt anpassen
            </span>

            <div class="w-full h-80 sm:h-[420px] bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 shadow-inner flex items-center justify-center relative">
              <img id="cropper-target-image" src="" alt="Zu beschneidendes Bild" class="max-w-full max-h-full block">
            </div>

            <!-- Toolbar / Steuerung -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
              <div class="flex items-center gap-1.5">
                <button type="button" id="crop-zoom-in" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition" title="Vergrößern">
                  🔍+ Zoom In
                </button>
                <button type="button" id="crop-zoom-out" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition" title="Verkleinern">
                  🔍- Zoom Out
                </button>
                <button type="button" id="crop-rotate-left" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition" title="90° nach links drehen">
                  ↺ -90°
                </button>
                <button type="button" id="crop-rotate-right" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition" title="90° nach rechts drehen">
                  ↻ +90°
                </button>
              </div>

              <button type="button" id="crop-reset" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-600 text-xs font-bold transition" title="Zurücksetzen">
                Zurücksetzen
              </button>
            </div>
          </div>

          <!-- Spalte Rechts: Echtzeit-Live-Vorschau (4 Spalten) -->
          <div class="lg:col-span-4 bg-slate-50 border border-slate-200 rounded-2xl p-5 space-y-4">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-navy uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Echtzeit-Live-Vorschau
              </span>
              <span class="text-[10px] font-bold text-sand-dark uppercase bg-sand/10 px-2 py-0.5 rounded-md">
                1:1 Website-Ansicht
              </span>
            </div>

            <p class="text-[11px] text-slate-500 font-light leading-snug">
              Genau so wird das Bild den Besuchern auf der Website angezeigt:
            </p>

            <!-- Container für die synchrone Cropper-Vorschau -->
            <div class="w-full flex justify-center items-center py-2">
              <div class="cropper-live-preview rounded-xl overflow-hidden border-2 border-slate-300 shadow-md bg-white transition-all"></div>
            </div>

            <div class="bg-amber-50/80 border border-amber-200/80 rounded-xl p-3 text-[11px] text-slate-600 leading-tight space-y-1">
              <div class="font-bold text-sand-dark flex items-center gap-1">
                <span>💡</span> Tipp zur Ausrichtung
              </div>
              <p>
                Achte darauf, dass das Hauptmotiv (Fahrzeug, Gesicht oder Einsatzszene) mittig im Ausschnitt liegt.
              </p>
            </div>
          </div>

        </div>

        <!-- Footer Buttons -->
        <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200 flex items-center justify-end gap-3">
          <button type="button" id="cropper-btn-cancel" class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold uppercase text-xs tracking-wider transition">
            Abbrechen
          </button>
          <button type="button" id="cropper-btn-apply" class="px-6 py-2.5 rounded-xl bg-sand hover:bg-sand-dark text-white font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2">
            <span>✓</span> Ausschnitt übernehmen
          </button>
        </div>

      </div>
    `;

    document.body.appendChild(modal);

    // Event Listener für Modal-Buttons
    document.getElementById('cropper-btn-close').addEventListener('click', closeModal);
    document.getElementById('cropper-btn-cancel').addEventListener('click', closeModal);
    document.getElementById('cropper-btn-apply').addEventListener('click', applyCrop);

    document.getElementById('crop-zoom-in').addEventListener('click', () => currentCropper && currentCropper.zoom(0.1));
    document.getElementById('crop-zoom-out').addEventListener('click', () => currentCropper && currentCropper.zoom(-0.1));
    document.getElementById('crop-rotate-left').addEventListener('click', () => currentCropper && currentCropper.rotate(-90));
    document.getElementById('crop-rotate-right').addEventListener('click', () => currentCropper && currentCropper.rotate(90));
    document.getElementById('crop-reset').addEventListener('click', () => currentCropper && currentCropper.reset());

    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    return modal;
  }

  function openCropper(imageSrc, aspectRatio) {
    const modal = ensureModal();
    const targetImg = document.getElementById('cropper-target-image');
    
    // Preview-Box Seitenverhältnis anpassen
    const previewContainer = modal.querySelector('.cropper-live-preview');
    if (previewContainer) {
      if (!isNaN(aspectRatio) && aspectRatio > 0) {
        const previewWidth = 240;
        const previewHeight = Math.round(previewWidth / aspectRatio);
        previewContainer.style.width = `${previewWidth}px`;
        previewContainer.style.height = `${previewHeight}px`;
      } else {
        previewContainer.style.width = '240px';
        previewContainer.style.height = '160px';
      }
    }

    modal.classList.remove('hidden');

    if (currentCropper) {
      currentCropper.destroy();
      currentCropper = null;
    }

    function initCropperInstance() {
      if (typeof Cropper === 'undefined') {
        console.error('Cropper.js ist nicht geladen.');
        return;
      }

      currentCropper = new Cropper(targetImg, {
        aspectRatio: isNaN(aspectRatio) ? NaN : aspectRatio,
        viewMode: 1, // Innerhalb des Bildes bleiben
        dragMode: 'move',
        autoCropArea: 0.95,
        restore: false,
        guides: true,
        center: true,
        highlight: false,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: false,
        preview: '.cropper-live-preview'
      });
    }

    targetImg.onload = initCropperInstance;
    targetImg.src = imageSrc;
    if (targetImg.complete) {
      initCropperInstance();
    }
  }

  function closeModal() {
    const modal = document.getElementById('admin-cropper-modal');
    if (modal) modal.classList.add('hidden');
    if (currentCropper) {
      currentCropper.destroy();
      currentCropper = null;
    }
  }

  function applyCrop() {
    if (!currentCropper) {
      closeModal();
      return;
    }

    // Exportiere das Bild in hoher Auflösung (max. 1600px Breite für exzellente Schärfe)
    const canvas = currentCropper.getCroppedCanvas({
      maxWidth: 1600,
      maxHeight: 1600,
      imageSmoothingEnabled: true,
      imageSmoothingQuality: 'high'
    });

    if (!canvas) {
      alert('Der Bildausschnitt konnte nicht erzeugt werden.');
      closeModal();
      return;
    }

    const dataUrl = canvas.toDataURL('image/jpeg', 0.90);

    // In verstecktes Input-Feld schreiben
    if (currentHiddenInput) {
      currentHiddenInput.value = dataUrl;
    }

    // Vorschau im Formular aktualisieren
    if (currentPreviewImg) {
      currentPreviewImg.src = dataUrl;
      currentPreviewImg.classList.remove('hidden');

      // Erfolgs-Hinweis einblenden falls Container vorhanden
      const badge = currentPreviewImg.closest('.image-crop-wrapper')?.querySelector('.crop-success-badge');
      if (badge) {
        badge.classList.remove('hidden');
      }
    }

    closeModal();
  }

  // Initialisierung für alle Inputs mit data-cropper="true"
  function initCroppers() {
    const fileInputs = document.querySelectorAll('input[type="file"][data-cropper="true"]');
    
    fileInputs.forEach(input => {
      // Vermeide Mehrfach-Registrierung
      if (input.dataset.cropperInitialized === 'true') return;
      input.dataset.cropperInitialized = 'true';

      const wrapper = input.closest('.image-crop-wrapper') || input.parentElement;
      const hiddenInput = wrapper ? wrapper.querySelector('input[name="cropped_image"]') : null;
      const previewImg = wrapper ? wrapper.querySelector('.crop-form-preview') : null;
      const adjustBtn = wrapper ? wrapper.querySelector('.btn-adjust-crop') : null;

      const rawRatio = input.getAttribute('data-aspect-ratio');
      const ratio = rawRatio ? parseFloat(rawRatio) : (16 / 9);

      // Bei Dateiauswahl
      input.addEventListener('change', function(e) {
        const file = this.files && this.files[0];
        if (!file) return;

        if (!file.type.match(/^image\/(jpeg|png|webp)$/)) {
          alert('Bitte wählen Sie ein gültiges Bild (JPG, PNG oder WebP) aus.');
          return;
        }

        currentFileInput = this;
        currentHiddenInput = hiddenInput;
        currentPreviewImg = previewImg;
        currentAspectRatio = ratio;

        const reader = new FileReader();
        reader.onload = function(evt) {
          openCropper(evt.target.result, ratio);
        };
        reader.readAsDataURL(file);
      });

      // Klick auf "Ausschnitt anpassen" bei bestehendem Bild
      if (adjustBtn) {
        adjustBtn.addEventListener('click', function() {
          const currentSrc = previewImg ? previewImg.getAttribute('src') : null;
          if (!currentSrc || currentSrc === '') {
            input.click();
            return;
          }

          currentFileInput = input;
          currentHiddenInput = hiddenInput;
          currentPreviewImg = previewImg;
          currentAspectRatio = ratio;

          openCropper(currentSrc, ratio);
        });
      }
    });
  }

  // DOM ready Listener
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCroppers);
  } else {
    initCroppers();
  }

  window.initAdminCroppers = initCroppers;
})();
