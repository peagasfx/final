<?php
/**
 * PROXY DE CONSULTA CPF - ATUALIZADO
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET');

// 1. CAPTURA O CPF DO FINAL DA URL (PATH_INFO) OU QUERY STRING
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
$cpf = preg_replace('/\D/', '', $path);

// Se o PATH_INFO estiver vazio, tenta pegar via ?cpf=
if (empty($cpf)) {
    $cpf = isset($_GET['cpf']) ? preg_replace('/\D/', '', $_GET['cpf']) : '';
}

// 2. VALIDAÇÃO
if (strlen($cpf) !== 11) {
    echo json_encode([
        'error' => 'CPF inválido',
        'message' => 'O sistema esperava 11 dígitos.',
        'valor_recebido' => $cpf
    ]);
    exit;
}

// 3. CONSULTA NA NOVA API EXTERNA
// Alterado para o novo endpoint com parâmetro ?cpf=
$apiUrl = "https://guiabenefibr.com/a/consulta-cpf.php?cpf=" . $cpf;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0 Safari/537.36"
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// 4. RETORNO PARA O JAVASCRIPT
if ($response === false) {
    http_response_code(500);
    echo json_encode([
        'error' => 'API Offline', 
        'message' => 'Falha na conexão com o servidor de dados.',
        'curl_error' => $error
    ]);
} else {
    http_response_code($httpCode);
    echo $response;
}