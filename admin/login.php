<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::startSession();

// Wenn bereits eingeloggt, direkt ins Dashboard
if (Auth::check()) {
    header('Location: /admin/index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Bitte Benutzername und Passwort angeben.';
    } else {
        if (Auth::login($username, $password)) {
            header('Location: /admin/index.php');
            exit;
        } else {
            $error = 'Ungültige Anmeldedaten. Bitte prüfen Sie Ihre Eingaben.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CMS Login | Freiwillige Feuerwehr Wulften am Harz</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            navy: { DEFAULT: '#002b66', dark: '#001433', light: '#003d8f' },
            sand: { DEFAULT: '#997c33', light: '#bfa04e' },
            alarm: '#dc2626'
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen flex items-center justify-center p-4 font-eurostile">
  
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <div class="mb-4">
        <img src="/assets/img/logo.png" alt="FF Wulften Logo" class="h-20 w-auto mx-auto object-contain drop-shadow-md">
      </div>
      <h1 class="text-2xl sm:text-3xl font-extrabold uppercase tracking-tight text-navy">
        FF Wulften am Harz
      </h1>
      <p class="text-xs font-bold text-sand tracking-widest uppercase mt-1">
        Interner Verwaltungsbereich
      </p>
    </div>

    <div class="light-panel rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-xl bg-white">
      
      <?php if ($error): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-semibold">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form action="/admin/login.php" method="POST" class="space-y-5">
        <div>
          <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Benutzername
          </label>
          <input type="text" id="username" name="username" required autocomplete="username" placeholder="Benutzername eingeben" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
        </div>

        <div>
          <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Passwort
          </label>
          <input type="password" id="password" name="password" required autocomplete="current-password" placeholder="••••••••••••" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
        </div>

        <div>
          <button type="submit" class="w-full py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition-all duration-300 shadow-sm transform hover:-translate-y-0.5">
            Anmelden
          </button>
        </div>
      </form>

      <div class="mt-8 pt-6 border-t border-slate-100 text-center">
        <a href="/index.php" class="text-xs text-slate-500 hover:text-navy transition font-medium">
          &larr; Zurück zur öffentlichen Website
        </a>
      </div>

    </div>
  </div>

</body>
</html>
