<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';

$isCli = (php_sapi_name() === 'cli');

try {
    $db = Database::getConnection();
    Database::initSchema($db);
    Database::seedInitialData($db);

    $msg = "Datenbank erfolgreich initialisiert und mit realistischen Demodaten für die Freiwillige Feuerwehr Wulften am Harz befüllt!\n";
    $msg .= "Admin-Zugang: Benutzer 'admin' / Passwort 'admin123'\n";
    $msg .= "Datenbank-Datei: database/feuerwehr.sqlite\n";

    if ($isCli) {
        echo $msg;
    } else {
        echo '<!DOCTYPE html><html lang="de"><head><meta charset="UTF-8"><title>Datenbank-Setup</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-[#020b18] text-white flex items-center justify-center min-h-screen p-4 font-sans">';
        echo '<div class="max-w-md w-full bg-[#002b66]/80 p-8 rounded-2xl border border-[#997c33]/50 shadow-2xl text-center">';
        echo '<div class="text-4xl mb-4">🚒 ✅</div>';
        echo '<h1 class="text-2xl font-bold uppercase mb-2">Setup erfolgreich!</h1>';
        echo '<p class="text-sm text-slate-300 mb-6">' . nl2br(htmlspecialchars($msg)) . '</p>';
        echo '<div class="flex gap-4 justify-center">';
        echo '<a href="/index.php" class="px-5 py-2.5 rounded-xl bg-[#997c33] text-[#001738] font-bold uppercase text-xs">Zur Website</a>';
        echo '<a href="/admin/login.php" class="px-5 py-2.5 rounded-xl bg-white/10 text-white font-bold uppercase text-xs border border-white/20">Zum CMS Login</a>';
        echo '</div>';
        echo '</div></body></html>';
    }
} catch (Throwable $e) {
    if ($isCli) {
        echo "Fehler bei der Initialisierung: " . $e->getMessage() . "\n";
    } else {
        echo "<h1>Fehler bei der Initialisierung</h1><p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
