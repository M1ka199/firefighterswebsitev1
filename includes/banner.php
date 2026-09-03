<?php
declare(strict_types=1);

$bannerTitle = $bannerTitle ?? ($seo['banner_title'] ?? 'FF Wulften am Harz');
$bannerIntro = $bannerIntro ?? ($seo['banner_intro'] ?? 'Ehrenamtlich und verlässlich im Einsatz für die Bürgerinnen und Bürger.');
?>
<section class="relative py-12 sm:py-14 bg-gradient-to-r from-slate-100 via-white to-slate-100 border-b border-slate-200 overflow-hidden">
  <!-- Subtle Grid Accent -->
  <div class="absolute inset-0 opacity-40 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none"></div>

  <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-xs font-bold text-sand uppercase tracking-widest mb-2">
        <a href="/index.php" class="hover:text-navy transition">Startseite</a>
        <span class="text-slate-400">/</span>
        <span class="text-slate-500 font-semibold"><?= e($bannerTitle) ?></span>
      </div>

      <!-- Main Banner Title -->
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold uppercase text-navy tracking-tight leading-tight mb-3">
        <?= e($bannerTitle) ?>
      </h1>

      <!-- Intro Text -->
      <p class="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed font-light">
        <?= e($bannerIntro) ?>
      </p>
    </div>
  </div>
</section>
