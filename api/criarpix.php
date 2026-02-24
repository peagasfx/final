<?php
header('Content-Type: application/json');

// --- CREDENCIAIS GHOSTSPAYS ---
$secretKey = "sk_live_lLZWDvrWjkEDbefWFe7R6zK1pLr6T7Q0F7Z7owZlG3j4mUAz"; // Username
$companyId = "6268a2e7-67f7-49c6-a3ff-38c67f3185d7"; // Password

// Captura os dados enviados pelo HTML
$input = json_decode(file_get_contents('php://input'), true);
$cpf = preg_replace('/\D/', '', $input['cpf']);
$nome = $input['nome'];

// Configuração do pagamento
$data = [
    "customer" => [
        "name" => $nome,
        "document" => $cpf,
        "email" => "contribuinte@gov.br",
        "phone" => "11999999999"
    ],
    "paymentMethod" => "PIX",
    "amount" => 13845, 
    "items" => [
        [
            "title" => "Curso Online",
            "unitPrice" => 13845,
            "quantity" => 1
        ]
    ]
];

$credentials = base64_encode($secretKey . ':' . $companyId);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.ghostspaysv2.com/functions/v1/transactions",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => [
        'Authorization: Basic ' . $credentials,
        'Content-Type: application/json'
    ],
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo $response;
?>