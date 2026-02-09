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

$filters = [
    'status' => trim($_GET['status'] ?? ''),
    'from' => trim($_GET['from'] ?? ''),
    'to' => trim($_GET['to'] ?? ''),
];

$orders = $controller->listOrders($filters);

include __DIR__ . '/../../app/views/flow/orders_list.php';
