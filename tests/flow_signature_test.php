<?php

declare(strict_types=1);

require __DIR__ . '/../app/services/FlowClient.php';
require __DIR__ . '/../app/models/FlowConfigModel.php';

class FlowConfigModelStub extends FlowConfigModel
{
    public function getActiveConfig(): array
    {
        return [
            'api_key' => 'api',
            'secret_key' => 'secret',
            'base_url' => 'https://example.com',
        ];
    }
}

$client = new FlowClient(new FlowConfigModelStub());

$params = [
    'currency' => 'CLP',
    'amount' => '1000',
    'apiKey' => 'ABC123',
    'commerceOrder' => 'ORD-99',
];

$expectedString = 'amount1000apiKeyABC123commerceOrderORD-99currencyCLP';
$expectedSignature = hash_hmac('sha256', $expectedString, 'secret');

$signature = $client->buildSignature($params, 'secret');
if ($signature !== $expectedSignature) {
    fwrite(STDERR, "Signature mismatch. Expected {$expectedSignature} got {$signature}.\n");
    exit(1);
}

$paramsWithSignature = $params + ['s' => 'ignored'];
$signatureWithS = $client->buildSignature($paramsWithSignature, 'secret');
if ($signatureWithS !== $expectedSignature) {
    fwrite(STDERR, "Signature should ignore 's' parameter.\n");
    exit(1);
}

echo "OK\n";
