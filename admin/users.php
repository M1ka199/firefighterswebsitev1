<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Helpers.php';

Auth::requireAdmin();

$db = Database::getConnection();
$currentUser = Auth::user();

// Benutzer löschen
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        if ($currentUser && (int)$currentUser['id'] === $delId) {
            setFlash('error', 'Sie können Ihr eigenes Benutzerkonto nicht löschen.');
        } else {
            // Prüfen ob es der letzte Admin ist
            $adminCount = (int)$db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
            $delUserRole = $db->query("SELECT role FROM users WHERE id = {$delId}")->fetchColumn();

            if ($delUserRole === 'admin' && $adminCount <= 1) {
                setFlash('error', 'Der letzte verbleibende Administrator kann nicht gelöscht werden.');
            } else {
                $stmtDel = $db->prepare('DELETE FROM users WHERE id = ?');
                $stmtDel->execute([$delId]);
                setFlash('success', 'Benutzer wurde erfolgreich gelöscht.');
            }
        }
        header('Location: /admin/users.php');
        exit;
    }
}

$adminTitle = 'Benutzerverwaltung';
$activeNav = 'users';
require_once __DIR__ . '/includes/admin_header.php';

// Alle Benutzer laden
$stmt = $db->query('SELECT * FROM users ORDER BY role ASC, id ASC');
$users = $stmt->fetchAll();

$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">System & Sicherheit</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Benutzerverwaltung
      </h1>
      <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
        Verwalten Sie Administratoren und Redakteure für den Zugriff auf das Feuerwehr-CMS.
      </p>
    </div>

    <a href="/admin/user-edit.php" class="px-5 py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
      <span>+</span> Neuen Benutzer anlegen
    </a>
  </div>

  <div class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[11px] border-b border-slate-200">
          <tr>
            <th class="py-4 px-6">Benutzer</th>
            <th class="py-4 px-6">Rolle & Berechtigung</th>
            <th class="py-4 px-6">Letzter Login</th>
            <th class="py-4 px-6">Erstellt am</th>
            <th class="py-4 px-6 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php foreach ($users as $u): ?>
            <?php $isSelf = ($currentUser && (int)$currentUser['id'] === (int)$u['id']); ?>
            <tr class="hover:bg-slate-50/80 transition">
              <td class="py-4 px-6">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-navy text-xs">
                    <?= strtoupper(substr($u['username'], 0, 2)) ?>
                  </div>
                  <div>
                    <strong class="text-navy block font-bold text-sm">
                      <?= e($u['full_name']) ?>
                      <?php if ($isSelf): ?>
                        <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md ml-1.5">Du</span>
                      <?php endif; ?>
                    </strong>
                    <span class="text-xs text-slate-500 font-mono">@<?= e($u['username']) ?></span>
                  </div>
                </div>
              </td>

              <td class="py-4 px-6">
                <?php if ($u['role'] === 'admin'): ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                    <span>👑</span> Administrator
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                    <span>✏️</span> Redakteur
                  </span>
                <?php endif; ?>
              </td>

              <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-500">
                <?= !empty($u['last_login']) ? date('d.m.Y H:i', strtotime($u['last_login'])) . ' Uhr' : 'Noch nie eingeloggt' ?>
              </td>

              <td class="py-4 px-6 whitespace-nowrap text-xs text-slate-500">
                <?= !empty($u['created_at']) ? date('d.m.Y', strtotime($u['created_at'])) : '–' ?>
              </td>

              <td class="py-4 px-6 text-right whitespace-nowrap">
                <div class="inline-flex items-center gap-2">
                  <a href="/admin/user-edit.php?id=<?= $u['id'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy font-bold text-xs uppercase tracking-wider transition border border-slate-200">
                    Bearbeiten
                  </a>
                  
                  <?php if (!$isSelf): ?>
                    <a href="/admin/users.php?delete=<?= $u['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Möchten Sie diesen Benutzer wirklich unwiderruflich löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200" title="Benutzer löschen">
                      ✕
                    </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
