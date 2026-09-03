<?php
declare(strict_types=1);

$adminTitle = 'Benutzer bearbeiten';
$activeNav = 'users';
require_once __DIR__ . '/includes/admin_header.php';
Auth::requireAdmin();

$db = Database::getConnection();
$currentUser = Auth::user();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userItem = null;

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $userItem = $stmt->fetch();
}

$isEdit = !empty($userItem);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $username = strtolower(trim($_POST['username'] ?? ''));
    $fullName = trim($_POST['full_name'] ?? '');
    $role     = in_array($_POST['role'] ?? '', ['admin', 'redakteur']) ? $_POST['role'] : 'redakteur';
    $password = trim($_POST['password'] ?? '');

    // Validierung
    if (empty($username) || empty($fullName)) {
        setFlash('error', 'Bitte Benutzername und vollständigen Namen angeben.');
    } elseif (!$isEdit && empty($password)) {
        setFlash('error', 'Für neue Benutzer ist ein Passwort erforderlich.');
    } else {
        // Prüfen, ob Benutzername bereits vergeben ist (außer beim aktuellen User)
        $checkStmt = $db->prepare('SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1');
        $checkStmt->execute([$username, $id]);
        if ($checkStmt->fetch()) {
            setFlash('error', 'Dieser Benutzername ist bereits vergeben.');
        } else {
            if ($isEdit) {
                if (!empty($password)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmtUp = $db->prepare('UPDATE users SET username = ?, full_name = ?, role = ?, password_hash = ? WHERE id = ?');
                    $stmtUp->execute([$username, $fullName, $role, $hash, $id]);
                } else {
                    $stmtUp = $db->prepare('UPDATE users SET username = ?, full_name = ?, role = ? WHERE id = ?');
                    $stmtUp->execute([$username, $fullName, $role, $id]);
                }
                setFlash('success', "Benutzer '{$username}' wurde erfolgreich aktualisiert.");
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmtIns = $db->prepare('INSERT INTO users (username, full_name, role, password_hash) VALUES (?, ?, ?, ?)');
                $stmtIns->execute([$username, $fullName, $role, $hash]);
                setFlash('success', "Neuer Benutzer '{$username}' wurde erfolgreich angelegt.");
            }

            header('Location: /admin/users.php');
            exit;
        }
    }
}

$csrf = Auth::csrfToken();
?>

<div class="max-w-3xl mx-auto space-y-8">
  <div>
    <a href="/admin/users.php" class="text-xs font-bold text-navy hover:text-sand uppercase inline-flex items-center gap-1 mb-2">
      <span>&larr;</span> Zurück zur Benutzerübersicht
    </a>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight">
      <?= $isEdit ? 'Benutzer bearbeiten' : 'Neuen Benutzer anlegen' ?>
    </h1>
  </div>

  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white">
    <form action="" method="POST" class="space-y-6">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Benutzername (Login) <span class="text-red-500">*</span>
          </label>
          <input type="text" name="username" required value="<?= $isEdit ? e($userItem['username']) : '' ?>" placeholder="z.B. mmueeller oder s_lindemann" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
          <span class="text-[11px] text-slate-400 mt-1 block">Wird für den Login verwendet (Kleinbuchstaben).</span>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Vollständiger Name <span class="text-red-500">*</span>
          </label>
          <input type="text" name="full_name" required value="<?= $isEdit ? e($userItem['full_name']) : '' ?>" placeholder="z.B. Michael Müller" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Rolle & Berechtigung <span class="text-red-500">*</span>
          </label>
          <select name="role" required class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            <option value="admin" <?= ($isEdit && $userItem['role'] === 'admin') ? 'selected' : '' ?>>
              👑 Administrator (Voller Zugriff inkl. Benutzer)
            </option>
            <option value="redakteur" <?= ($isEdit && $userItem['role'] === 'redakteur') ? 'selected' : '' ?>>
              ✏️ Redakteur (Einsätze, Termine, Berichte verwalten)
            </option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Passwort <?= $isEdit ? '<span class="text-slate-400 font-normal">(leer lassen für unverändert)</span>' : '<span class="text-red-500">*</span>' ?>
          </label>
          <input type="password" name="password" <?= $isEdit ? '' : 'required' ?> placeholder="••••••••••••" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
          <span class="text-[11px] text-slate-400 mt-1 block">Sicheres Passwort mit mind. 8 Zeichen empfohlen.</span>
        </div>
      </div>

      <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
        <a href="/admin/users.php" class="text-xs font-bold uppercase text-slate-500 hover:text-navy transition">
          Abbrechen
        </a>

        <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
          <?= $isEdit ? 'Änderungen speichern' : 'Benutzer erstellen' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
