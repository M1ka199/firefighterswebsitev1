<?php
declare(strict_types=1);

require_once __DIR__ . '/Database.php';

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function getSetting(string $key, string $default = ''): string {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT setting_value FROM system_settings WHERE setting_key = ?');
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return ($val !== false) ? (string)$val : $default;
    } catch (Throwable $t) {
        return $default;
    }
}

function getPageSeo(string $slug): array {
    try {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM seiten_seo WHERE slug = ? LIMIT 1');
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        if ($row) {
            return $row;
        }
    } catch (Throwable $t) {}

    return [
        'slug' => $slug,
        'page_title' => 'Freiwillige Feuerwehr Wulften am Harz',
        'meta_description' => 'Offizielle Webseite der Freiwilligen Feuerwehr Wulften am Harz',
        'keywords' => 'Feuerwehr Wulften am Harz, Notruf 112',
        'banner_title' => 'FF Wulften am Harz',
        'banner_intro' => 'Ehrenamtlich und verlässlich im Einsatz für unsere Gemeinde.'
    ];
}

function formatDateGerman(?string $dateStr): string {
    if (!$dateStr) return '';
    $timestamp = strtotime($dateStr);
    return date('d.m.Y', $timestamp);
}

function formatTimeGerman(?string $timeStr): string {
    if (!$timeStr) return '';
    $parts = explode(':', $timeStr);
    return $parts[0] . ':' . ($parts[1] ?? '00') . ' Uhr';
}

function formatDateTimeGerman(?string $dtStr): string {
    if (!$dtStr) return '';
    $timestamp = strtotime($dtStr);
    return date('d.m.Y, H:i', $timestamp) . ' Uhr';
}

function getCategoryBadge(string $category): string {
    $cat = strtolower($category);
    switch ($cat) {
        case 'brand':
            return '<span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-red-50 text-red-700 border border-red-200 shadow-sm"><span class="w-2 h-2 rounded-full bg-red-600 animate-pulse mr-1.5"></span>Brand</span>';
        case 'th':
            return '<span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-300 shadow-sm"><span class="w-2 h-2 rounded-full bg-[#997c33] mr-1.5"></span>TH (Hilfeleistung)</span>';
        case 'bma':
            return '<span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 shadow-sm">BMA Fehlalarm</span>';
        default:
            return '<span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-300 shadow-sm">Sonstiges</span>';
    }
}

function getHierarchyName(int $level): string {
    switch ($level) {
        case 1: return 'Ortsbrandmeister';
        case 2: return 'Stellvertretung';
        case 3: return 'Gruppenführer & Fachwarte';
        case 4: return 'Erweitertes Kommando';
        default: return 'Funktionsträger';
    }
}

function setFlash(string $type, string $message): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
