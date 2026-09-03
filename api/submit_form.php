<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Helpers.php';

$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Nur POST-Anfragen erlaubt.']);
    } else {
        header('Location: /kontakt.php');
    }
    exit;
}

$type = trim($_POST['type'] ?? 'kontakt');
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$age = !empty($_POST['age']) ? (int)$_POST['age'] : null;
$message = trim($_POST['message'] ?? '');

// Zusätzliche Angaben aus Kontakt- oder Schnupperdienst-Formular
$abteilung = trim($_POST['abteilung'] ?? '');
$topic     = trim($_POST['topic'] ?? '');
$interest  = trim($_POST['interest'] ?? '');
$priorExp  = trim($_POST['prior_exp'] ?? '');

$meta = [];
if (!empty($abteilung)) {
    $meta[] = "Abteilung: {$abteilung}";
}
if (!empty($topic)) {
    $meta[] = "Betreff: {$topic}";
}
if (!empty($interest)) {
    $meta[] = "Interesse an: {$interest}";
}
if (!empty($priorExp)) {
    $meta[] = "Vorerfahrung: {$priorExp}";
}

if (!empty($meta)) {
    $message = implode("\n", $meta) . "\n\nNachricht:\n" . $message;
}

// Validierung
if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Bitte geben Sie einen gültigen Namen und eine E-Mail-Adresse an.']);
        exit;
    } else {
        setFlash('error', 'Bitte geben Sie einen gültigen Namen und eine E-Mail-Adresse an.');
        header('Location: ' . ($type === 'schnupperdienst' ? '/schnupperdienst.php' : '/kontakt.php'));
        exit;
    }
}

try {
    $db = Database::getConnection();
    $stmt = $db->prepare('
        INSERT INTO form_submissions (type, name, email, phone, age, message, status)
        VALUES (?, ?, ?, ?, ?, ?, "neu")
    ');
    $stmt->execute([$type, $name, $email, $phone, $age, $message]);

    // Optional: E-Mail Benachrichtigung an Admin via SMTP falls konfiguriert
    $adminEmail = getSetting('contact_email', 'info@feuerwehr-wulften.de');
    $subject = ($type === 'schnupperdienst') 
        ? "🔥 Neue Schnupperdienst-Anmeldung von {$name}" 
        : "Neue Kontaktanfrage über FF-Wulften.de von {$name}";
    
    // (Native mail attempt - fails gracefully if no sendmail configured)
    @mail($adminEmail, $subject, "Neue Nachricht:\nName: {$name}\nE-Mail: {$email}\nTelefon: {$phone}\n\n{$message}", "From: noreply@feuerwehr-wulften.de");

    $successMsg = ($type === 'schnupperdienst')
        ? 'Vielen Dank für dein Interesse! Deine Schnupperdienst-Anmeldung ist eingegangen. Wir melden uns zeitnah bei dir.'
        : 'Vielen Dank! Ihre Nachricht wurde erfolgreich an die Feuerwehr Wulften übermittelt.';

    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $successMsg]);
        exit;
    } else {
        setFlash('success', $successMsg);
        header('Location: ' . ($type === 'schnupperdienst' ? '/schnupperdienst.php?success=1' : '/kontakt.php?success=1'));
        exit;
    }
} catch (Throwable $e) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Datenbankfehler: ' . $e->getMessage()]);
        exit;
    } else {
        setFlash('error', 'Ein Fehler ist aufgetreten.');
        header('Location: /kontakt.php');
        exit;
    }
}
