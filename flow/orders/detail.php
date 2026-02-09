<?php
require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/models/FlowConfigModel.php';
require __DIR__ . '/../../app/models/FlowOrderModel.php';
require __DIR__ . '/../../app/services/FlowClient.php';
require __DIR__ . '/../../app/controllers/FlowPaymentsController.php';

if (!flow_user_can_access()) {
    redirect('error-403.php');
}

$configModel = new FlowConfigModel();
$orderModel = new FlowOrderModel();
$client = new FlowClient($configModel);
$controller = new FlowPaymentsController($orderModel, $configModel, $client);

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = $orderId ? $controller->getOrder($orderId) : null;
$errors = [];
$statusMessage = null;
$lastStatusJson = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null) && $order) {
    try {
        $response = $controller->refreshStatus((int) $order['id'], (string) $order['flow_token']);
        $statusMessage = 'Estado actualizado correctamente.';
        $order = $controller->getOrder((int) $order['id']);
        $lastStatusJson = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

if ($order && $lastStatusJson === '') {
    $lastStatusJson = $order['last_status_response']
        ? json_encode(json_decode($order['last_status_response'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        : '';
}

include __DIR__ . '/../../app/views/flow/order_detail.php';
