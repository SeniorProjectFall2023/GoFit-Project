<?php
// Your PHP file (e.g., openai-proxy.php)
$apiKey = "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";
$url = "https://api.openai.com/v1/chat/completions";
$requestData = file_get_contents("php://input");

$headers = [
    "Content-Type: application/json",
    "Authorization: Bearer " . $apiKey
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $requestData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

header('Content-Type: application/json');
echo $response;
?>
