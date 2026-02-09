<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/config/flow.php';
require __DIR__ . '/services/FlowClient.php';
require __DIR__ . '/modules/flow/controllers/FlowPaymentsController.php';
require __DIR__ . '/modules/flow/controllers/FlowWebhookController.php';

$payload = $_POST;
$config = flow_config();

$paymentsController = new FlowPaymentsController();
$webhookController = new FlowWebhookController($paymentsController);

if ($config['api_key'] === '' || $config['secret_key'] === '') {
    http_response_code(500);
    echo 'Configuración Flow incompleta.';
    exit;
}

$client = new FlowClient($config['api_key'], $config['secret_key'], $config['base_url']);
$result = $webhookController->confirmation($client, $payload);
if ($result['status'] === 500) {
    error_log('Flow webhook error: ' . $result['message']);
}

http_response_code($result['status']);
header('Content-Type: text/plain');
echo $result['message'];
