<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Auth.php';

header('Content-Type: application/json; charset=utf-8');

Auth::startSession();
if (!Auth::check()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Nicht autorisiert. Bitte einloggen.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$host       = trim($input['smtp_host'] ?? '');
$port       = (int)($input['smtp_port'] ?? 587);
$encryption = trim(strtolower($input['smtp_encryption'] ?? 'tls')); // 'none', 'ssl', 'tls'
$username   = trim($input['smtp_user'] ?? '');
$password   = $input['smtp_pass'] ?? '';
$timeout    = 6; // Sekunden

if (empty($host) || $port <= 0) {
    echo json_encode(['success' => false, 'message' => 'SMTP-Host und gültiger Port (z.B. 587 oder 465) sind erforderlich.']);
    exit;
}

$startTime = microtime(true);
$socket = null;

try {
    $transport = ($encryption === 'ssl') ? 'ssl://' : 'tcp://';
    $socketTarget = $transport . $host . ':' . $port;

    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ]);

    $socket = @stream_socket_client($socketTarget, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

    if (!$socket) {
        throw new RuntimeException("Verbindungsaufbau fehlgeschlagen: {$errstr} (Fehlercode {$errno})");
    }

    stream_set_timeout($socket, $timeout);

    // 1. Server-Begrüßung lesen (Code 220)
    $response = fgets($socket, 512);
    if (!$response || !str_starts_with(trim($response), '220')) {
        throw new RuntimeException("Server hat nicht mit Code 220 geantwortet: " . trim((string)$response));
    }

    // 2. EHLO Handshake
    fwrite($socket, "EHLO " . gethostname() . "\r\n");
    $ehloOk = false;
    while ($line = fgets($socket, 512)) {
        if (str_starts_with($line, '250')) $ehloOk = true;
        if (substr($line, 3, 1) === ' ') break;
    }

    if (!$ehloOk) {
        throw new RuntimeException("EHLO wurde vom Server nicht akzeptiert.");
    }

    // 3. STARTTLS falls gewünscht
    if ($encryption === 'tls') {
        fwrite($socket, "STARTTLS\r\n");
        $tlsResp = fgets($socket, 512);
        if (!$tlsResp || !str_starts_with(trim($tlsResp), '220')) {
            throw new RuntimeException("STARTTLS abgelehnt: " . trim((string)$tlsResp));
        }

        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            throw new RuntimeException("TLS-Verschlüsselung konnte nicht ausgehandelt werden.");
        }

        // Nach TLS erneut EHLO senden
        fwrite($socket, "EHLO " . gethostname() . "\r\n");
        while ($line = fgets($socket, 512)) {
            if (substr($line, 3, 1) === ' ') break;
        }
    }

    // 4. Authentifizierung testen falls Zugangsdaten vorliegen
    if (!empty($username) && !empty($password)) {
        fwrite($socket, "AUTH LOGIN\r\n");
        $authResp = fgets($socket, 512);
        if ($authResp && str_starts_with(trim($authResp), '334')) {
            fwrite($socket, base64_encode($username) . "\r\n");
            $userResp = fgets($socket, 512);
            if (!$userResp || !str_starts_with(trim($userResp), '334')) {
                throw new RuntimeException("Benutzername abgelehnt: " . trim((string)$userResp));
            }

            fwrite($socket, base64_encode($password) . "\r\n");
            $passResp = fgets($socket, 512);
            if (!$passResp || !str_starts_with(trim($passResp), '235')) {
                throw new RuntimeException("Passwort abgelehnt: " . trim((string)$passResp));
            }
        }
    }

    // Verbindung sauber schließen
    fwrite($socket, "QUIT\r\n");
    fclose($socket);

    $duration = round((microtime(true) - $startTime) * 1000);

    echo json_encode([
        'success' => true,
        'latency_ms' => $duration,
        'message' => "Verbindung zu {$host}:{$port} ({$encryption}) erfolgreich hergestellt! Latenz: {$duration} ms"
    ]);

} catch (Throwable $e) {
    if (isset($socket) && is_resource($socket)) {
        fclose($socket);
    }
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
