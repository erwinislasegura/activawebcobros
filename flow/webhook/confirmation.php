<?php
require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/models/FlowConfigModel.php';
require __DIR__ . '/../../app/models/FlowOrderModel.php';
require __DIR__ . '/../../app/models/FlowWebhookLogModel.php';
require __DIR__ . '/../../app/services/FlowClient.php';
require __DIR__ . '/../../app/controllers/FlowWebhookController.php';

$configModel = new FlowConfigModel();
$orderModel = new FlowOrderModel();
$logModel = new FlowWebhookLogModel();
$client = new FlowClient($configModel);
$controller = new FlowWebhookController($orderModel, $logModel, $client);

try {
    $result = $controller->confirmation($_POST);
    http_response_code($result['status']);
    header('Content-Type: text/plain');
    echo $result['message'];
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    echo 'Error interno';
}
