<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Helpers.php';

Auth::requireLogin();
$user = Auth::user();
$activeNav = $activeNav ?? basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="de" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($adminTitle ?? 'CMS Dashboard') ?> | FF Wulften am Harz</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: {
              DEFAULT: '#002b66',
              dark: '#001433',
              surface: '#f1f5f9',
              light: '#003d8f'
            },
            sand: {
              DEFAULT: '#997c33',
              light: '#bfa04e',
              dark: '#735b22'
            },
            alarm: '#dc2626'
          },
          fontFamily: {
            sans: ['"Helvetica Neue"', 'Helvetica', 'Arial', 'system-ui', 'sans-serif'],
            eurostile: ['"Helvetica Neue"', 'Helvetica', 'Arial', 'system-ui', 'sans-serif']
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen font-sans flex flex-col">

  <!-- Gesamter CMS Wrapper mit vertikalem Menü links -->
  <div class="min-h-screen flex flex-col lg:flex-row flex-1">
    
    <!-- DESKTOP SIDEBAR: Vertikales Menü links (hochkant) -->
    <aside class="hidden lg:flex lg:flex-col w-64 xl:w-72 bg-navy-dark text-white border-r border-navy/40 flex-shrink-0 sticky top-0 h-screen overflow-y-auto z-40">
      
      <!-- Brand Header (Zentriertes Logo) -->
      <div class="p-6 border-b border-white/10 flex flex-col items-center justify-center text-center">
        <a href="/admin/index.php" class="inline-block hover:scale-105 transition-transform">
          <img src="/assets/img/logo.png" alt="FF Wulften Logo" class="h-14 w-auto object-contain drop-shadow mx-auto">
        </a>
        <span class="text-[11px] font-bold uppercase tracking-widest text-sand-light mt-2.5 block">
          FF Wulften am Harz
        </span>
        <span class="text-[9px] text-slate-400 uppercase tracking-widest font-semibold block">
          CMS Dashboard
        </span>
      </div>

      <!-- Vertikales Navigationsmenü -->
      <nav class="flex-1 px-4 py-6 space-y-1.5 text-xs font-semibold uppercase tracking-wider">
        
        <div class="px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
          Inhalte & Einsätze
        </div>

        <a href="/admin/index.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'index') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
          <span class="text-base">📊</span>
          <span>Übersicht</span>
        </a>

        <a href="/admin/einsaetze.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'einsaetze' || $activeNav === 'einsatz-edit') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
          <span class="text-base">🚨</span>
          <span>Einsätze</span>
        </a>

        <a href="/admin/mitglieder.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'mitglieder' || $activeNav === 'mitglied-edit') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
          <span class="text-base">👥</span>
          <span>Ortskommando</span>
        </a>

        <a href="/admin/fahrzeuge.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'fahrzeuge' || $activeNav === 'fahrzeug-edit') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
          <span class="text-base">🚒</span>
          <span>Fahrzeuge</span>
        </a>

        <a href="/admin/termine.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'termine') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
          <span class="text-base">📅</span>
          <span>Termine</span>
        </a>

        <a href="/admin/anfragen.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'anfragen') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
          <span class="text-base">✉️</span>
          <span>Formulare</span>
        </a>

        <?php if (($user['role'] ?? '') === 'admin'): ?>
          <div class="pt-4 px-3 pb-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            Design & System
          </div>

          <a href="/admin/seiten.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'seiten') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
            <span class="text-base">📄</span>
            <span>Seiten & SEO</span>
          </a>

          <a href="/admin/hero.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'hero') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
            <span class="text-base">🖼️</span>
            <span>Hero-Slider</span>
          </a>

          <a href="/admin/css-settings.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'css-settings') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
            <span class="text-base">🎨</span>
            <span>Globale CSS</span>
          </a>

          <a href="/admin/settings.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'settings') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
            <span class="text-base">⚙️</span>
            <span>Einstellungen</span>
          </a>

          <a href="/admin/users.php" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition <?= ($activeNav === 'users' || $activeNav === 'user-edit') ? 'bg-sand text-white font-bold shadow' : 'text-slate-300 hover:text-white hover:bg-white/10' ?>">
            <span class="text-base">👤</span>
            <span>Benutzer</span>
          </a>
        <?php endif; ?>

      </nav>

      <!-- User & Quick Action Footer in Sidebar -->
      <div class="p-4 border-t border-white/10 bg-navy/60">
        <div class="flex items-center justify-between mb-3 px-2">
          <div class="truncate">
            <span class="block text-xs font-bold text-white truncate"><?= e($user['full_name']) ?></span>
            <span class="block text-[10px] text-slate-400 uppercase tracking-wider"><?= e($user['role']) ?></span>
          </div>
          <span class="w-2.5 h-2.5 rounded-full bg-emerald-400" title="Online"></span>
        </div>

        <div class="grid grid-cols-2 gap-2 text-center text-xs font-bold uppercase tracking-wider">
          <a href="/index.php" target="_blank" class="py-2 px-2.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-200 hover:text-white border border-white/15 transition flex items-center justify-center gap-1">
            <span>Website</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
          </a>
          <a href="/admin/logout.php" class="py-2 px-2.5 rounded-lg bg-red-600/30 hover:bg-red-600 text-red-200 hover:text-white border border-red-500/40 transition">
            Abmelden
          </a>
        </div>
      </div>

    </aside>

    <!-- MOBILE HEADER BAR (nur kleine Bildschirme) -->
    <header class="lg:hidden bg-navy-dark text-white border-b border-navy/40 px-4 py-3 sticky top-0 z-40 flex items-center justify-between shadow-md">
      <div class="flex items-center gap-2.5">
        <img src="/assets/img/logo.png" alt="FF Wulften" class="h-8 w-auto object-contain">
        <span class="text-xs font-bold uppercase tracking-wider">FF Wulften am Harz</span>
      </div>

      <div class="flex items-center gap-2">
        <a href="/index.php" target="_blank" class="p-2 rounded-lg bg-white/10 text-xs font-bold text-slate-200 hover:text-white" title="Website ansehen">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
        <button id="admin-mobile-toggle" aria-label="CMS Menü öffnen" class="p-2 rounded-lg bg-white/10 hover:bg-white/20 text-white">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>
      </div>
    </header>

    <!-- MOBILE SIDEBAR DRAWER (einklappbar) -->
    <div id="admin-mobile-drawer" class="fixed inset-0 z-50 bg-black/60 hidden lg:hidden transition-opacity">
      <div class="w-64 max-w-[80vw] h-full bg-navy-dark text-white p-6 flex flex-col justify-between shadow-2xl overflow-y-auto">
        <div>
          <div class="flex items-center justify-between pb-4 border-b border-white/10 mb-4">
            <span class="font-bold uppercase text-sm tracking-wider">CMS Navigation</span>
            <button id="admin-mobile-close" class="p-1 rounded-lg text-slate-400 hover:text-white">
              ✕
            </button>
          </div>

          <nav class="space-y-1 text-xs uppercase tracking-wider font-semibold">
            <a href="/admin/index.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'index') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">📊 Übersicht</a>
            <a href="/admin/einsaetze.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'einsaetze' || $activeNav === 'einsatz-edit') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">🚨 Einsätze</a>
            <a href="/admin/mitglieder.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'mitglieder' || $activeNav === 'mitglied-edit') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">👥 Ortskommando</a>
            <a href="/admin/fahrzeuge.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'fahrzeuge' || $activeNav === 'fahrzeug-edit') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">🚒 Fahrzeuge</a>
            <a href="/admin/termine.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'termine') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">📅 Termine</a>
            <a href="/admin/anfragen.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'anfragen') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">✉️ Formulare</a>
            <?php if (($user['role'] ?? '') === 'admin'): ?>
              <a href="/admin/seiten.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'seiten') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">📄 Seiten & SEO</a>
              <a href="/admin/hero.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'hero') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">🖼️ Hero-Slider</a>
              <a href="/admin/css-settings.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'css-settings') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">🎨 Globale CSS</a>
              <a href="/admin/settings.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'settings') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">⚙️ Einstellungen</a>
              <a href="/admin/users.php" class="block px-3 py-2 rounded-lg <?= ($activeNav === 'users' || $activeNav === 'user-edit') ? 'bg-sand text-white font-bold' : 'text-slate-300' ?>">👤 Benutzer</a>
            <?php endif; ?>
          </nav>
        </div>

        <div class="pt-4 border-t border-white/10 space-y-2">
          <a href="/index.php" target="_blank" class="block text-center py-2 rounded-lg bg-white/10 text-xs font-bold uppercase text-white">Zur Website</a>
          <a href="/admin/logout.php" class="block text-center py-2 rounded-lg bg-red-600/40 text-xs font-bold uppercase text-white">Abmelden</a>
        </div>
      </div>
    </div>

    <!-- HAUPTINHALT RECHTS -->
    <div class="flex-1 flex flex-col min-w-0">

      <!-- Flash Messages -->
      <?php if ($flash): ?>
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-6">
          <div class="p-4 rounded-xl text-sm font-semibold shadow-sm <?= ($flash['type'] === 'success') ? 'bg-emerald-50 border border-emerald-300 text-emerald-800' : 'bg-red-50 border border-red-300 text-red-800' ?>">
            <?= e($flash['message']) ?>
          </div>
        </div>
      <?php endif; ?>

      <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8">
