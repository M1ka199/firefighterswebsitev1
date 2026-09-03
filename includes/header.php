<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Helpers.php';

// Falls keine SEO-Variable definiert ist, Standard aus slug laden
if (!isset($seo)) {
    $currentSlug = basename($_SERVER['PHP_SELF'], '.php');
    if ($currentSlug === 'index') $currentSlug = 'startseite';
    $seo = getPageSeo($currentSlug);
}

$customCss = getSetting('custom_css', '');
$instagramUrl = getSetting('instagram_url', 'https://www.instagram.com');
$activePage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="de" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($seo['page_title'] ?? 'Freiwillige Feuerwehr Wulften am Harz') ?></title>
  <meta name="description" content="<?= e($seo['meta_description'] ?? '') ?>">
  <meta name="keywords" content="<?= e($seo['keywords'] ?? '') ?>">
  <meta name="theme-color" content="#002b66">
  
  <!-- Open Graph -->
  <meta property="og:title" content="<?= e($seo['page_title'] ?? '') ?>">
  <meta property="og:description" content="<?= e($seo['meta_description'] ?? '') ?>">
  <meta property="og:type" content="website">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: {
              DEFAULT: '#002b66',
              dark: '#00193d',
              surface: '#f1f5f9',
              light: '#003d8f'
            },
            sand: {
              DEFAULT: '#997c33',
              light: '#bfa04e',
              dark: '#735b22',
              surface: '#fbf8f1'
            },
            alarm: {
              DEFAULT: '#dc2626',
              dark: '#b91c1c'
            }
          },
          fontFamily: {
            sans: ['"Helvetica Neue"', 'Helvetica', 'Arial', 'system-ui', 'sans-serif'],
            eurostile: ['"Helvetica Neue"', 'Helvetica', 'Arial', 'system-ui', 'sans-serif'],
            helvetica: ['"Helvetica Neue"', 'Helvetica', 'Arial', 'system-ui', 'sans-serif']
          }
        }
      }
    }
  </script>

  <!-- Custom CSS & Fonts -->
  <link rel="stylesheet" href="/assets/css/custom.css">

  <?php if (!empty($customCss)): ?>
    <style id="cms-custom-css">
      <?= $customCss ?>
    </style>
  <?php endif; ?>
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen flex flex-col font-sans selection:bg-sand/30 selection:text-navy">

  <!-- Top Full-Width Emergency 112 Banner -->
  <div class="w-full bg-gradient-to-r from-red-700 via-alarm to-red-700 text-white shadow-sm border-b border-red-800/40 relative z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex items-center justify-center sm:justify-between relative text-xs">
      
      <!-- Notruf Button: Auf Mobilgeräten mittig, ab sm: links -->
      <div class="flex-shrink-0 z-10 mx-auto sm:mx-0">
        <a href="tel:112" class="inline-flex items-center gap-2 bg-white text-red-700 hover:bg-red-50 px-4 sm:px-3.5 py-1.5 sm:py-1 rounded-full font-extrabold text-xs uppercase tracking-wider shadow transition-all duration-200 hover:scale-105">
          <span class="w-2 h-2 rounded-full bg-red-600 animate-ping"></span>
          <span>Notruf: 112</span>
        </a>
      </div>

      <!-- Mittig zentrierter Hinweistext (ab sm: sichtbar) -->
      <div class="hidden sm:flex absolute inset-0 items-center justify-center pointer-events-none px-4">
        <span class="font-bold tracking-wide uppercase text-xs text-center text-white drop-shadow-xs">
          Im Notfall immer Notruf <span class="underline decoration-2 font-black">112</span> wählen!
        </span>
      </div>

      <!-- Rechts (Ausgleich ab sm:) -->
      <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-semibold text-white/90 z-10">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
        <span>24/7 Einsatzbereit</span>
      </div>

    </div>
  </div>

  <!-- Sticky Header Navigation (Hell, Modern & Übersichtlich) -->
  <header class="sticky top-0 z-40 w-full backdrop-blur-md bg-white/90 border-b border-slate-200/90 shadow-sm transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-center lg:justify-between h-20">
        
        <!-- Logo (Auf Mobilgeräten zentriert, ab lg: linksbündig) -->
        <a href="/index.php" class="flex items-center justify-center gap-3.5 group mx-auto lg:mx-0">
          <img src="/assets/img/logo.png" alt="FF Wulften am Harz" class="h-12 group-hover:scale-105 transition-transform duration-300 mix-blend-multiply">
        </a>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center space-x-1">
          <a href="/index.php" class="nav-link-animated px-3.5 py-2 text-xs font-bold uppercase tracking-wider <?= ($activePage === 'index') ? 'is-active text-navy font-extrabold' : 'text-slate-600 hover:text-navy' ?>">Startseite</a>
          <a href="/ueber-uns.php" class="nav-link-animated px-3.5 py-2 text-xs font-bold uppercase tracking-wider <?= ($activePage === 'ueber-uns') ? 'is-active text-navy font-extrabold' : 'text-slate-600 hover:text-navy' ?>">Über Uns</a>
          <a href="/einsaetze.php" class="nav-link-animated px-3.5 py-2 text-xs font-bold uppercase tracking-wider <?= ($activePage === 'einsaetze' || $activePage === 'einsatz-detail') ? 'is-active text-navy font-extrabold' : 'text-slate-600 hover:text-navy' ?>">Einsätze</a>
          <a href="/kommando.php" class="nav-link-animated px-3.5 py-2 text-xs font-bold uppercase tracking-wider <?= ($activePage === 'kommando') ? 'is-active text-navy font-extrabold' : 'text-slate-600 hover:text-navy' ?>">Kommando</a>
          <a href="/termine.php" class="nav-link-animated px-3.5 py-2 text-xs font-bold uppercase tracking-wider <?= ($activePage === 'termine') ? 'is-active text-navy font-extrabold' : 'text-slate-600 hover:text-navy' ?>">Termine</a>

          <!-- Kontakt Dropdown mit Unterpunkten: Kontakt & Schnupperdienst -->
          <div class="relative group">
            <button class="nav-link-animated px-3.5 py-2 text-xs font-bold uppercase tracking-wider text-slate-600 hover:text-navy transition inline-flex items-center gap-1 <?= ($activePage === 'kontakt' || $activePage === 'schnupperdienst') ? 'is-active text-navy font-extrabold' : '' ?>">
              <span>Kontakt</span>
              <svg class="w-4 h-4 transition-transform group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            
            <div class="absolute right-0 mt-2 w-56 rounded-2xl bg-white border border-slate-200 py-2 shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
              <a href="/kontakt.php" class="block px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-navy transition flex items-center gap-2">
                <svg class="w-4 h-4 text-sand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Kontaktformular
              </a>
              <a href="/schnupperdienst.php" class="block px-4 py-2.5 text-xs font-bold text-sand hover:bg-amber-50/50 hover:text-sand-dark transition flex items-center gap-2">
                <span>🔥</span> Schnupperdienst
              </a>
            </div>
          </div>

          <!-- Instagram nur als Logo/Icon -->
          <a href="<?= e($instagramUrl) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram" title="Instagram" class="p-2.5 rounded-xl text-slate-600 hover:text-pink-600 hover:bg-slate-100/70 transition flex items-center justify-center">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.13-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          </a>
        </nav>

        <!-- Right Side: Kontakt CTA (nur ab Desktop) -->
        <div class="hidden lg:flex items-center gap-3">
          <a href="/kontakt.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-navy text-white hover:bg-navy-light font-bold text-xs uppercase tracking-wider transition shadow-sm">
            <span>Kontakt aufnehmen</span>
          </a>
        </div>

      </div>
    </div>
  </header>
  
  <main class="flex-grow">
