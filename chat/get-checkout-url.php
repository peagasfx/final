<?php
/**
 * Retorna a URL de checkout salva no painel admin (JSON).
 * Usado pelo chat para redirecionar dinamicamente.
 */
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, max-age=0');

$defaultUrl = '/payment.html';

try {
    require_once __DIR__ . '/../admin/config.php';
    initDb();
    $pdo = getDB();
    $stmt = $pdo->query("SELECT value FROM settings WHERE key_name = 'checkout_url'");
    $url = $stmt->fetchColumn();
    if ($url === false || $url === '') {
        $url = $defaultUrl;
    }
    echo json_encode(['success' => true, 'checkout_url' => $url]);
} catch (Throwable $e) {
    echo json_encode(['success' => true, 'checkout_url' => $defaultUrl]);
}
