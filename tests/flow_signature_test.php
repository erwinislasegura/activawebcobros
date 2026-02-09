<?php

declare(strict_types=1);

require __DIR__ . '/../services/FlowClient.php';

$client = new FlowClient('api', 'secret', 'https://example.com');

$params = [
    'currency' => 'CLP',
    'amount' => '1000',
    'apiKey' => 'ABC123',
    'commerceOrder' => 'ORD-99',
];

$expectedString = 'amount1000apiKeyABC123commerceOrderORD-99currencyCLP';
$expectedSignature = hash_hmac('sha256', $expectedString, 'secret');

$signature = $client->buildSignature($params);
if ($signature !== $expectedSignature) {
    fwrite(STDERR, "Signature mismatch. Expected {$expectedSignature} got {$signature}.\n");
    exit(1);
}

$paramsWithSignature = $params + ['s' => 'ignored'];
$signatureWithS = $client->buildSignature($paramsWithSignature);
if ($signatureWithS !== $expectedSignature) {
    fwrite(STDERR, "Signature should ignore 's' parameter.\n");
    exit(1);
}

echo "OK\n";
