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

$errors = [];
$success = null;
$paymentUrl = null;
$form = [
    'local_order_id' => '',
    'subject' => '',
    'amount' => '',
    'currency' => 'CLP',
    'customer_email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $form = [
        'local_order_id' => trim($_POST['local_order_id'] ?? ''),
        'subject' => trim($_POST['subject'] ?? ''),
        'amount' => (int) ($_POST['amount'] ?? 0),
        'currency' => strtoupper(trim($_POST['currency'] ?? 'CLP')),
        'customer_email' => trim($_POST['customer_email'] ?? ''),
    ];

    try {
        $result = $controller->createPayment($form);
        if (!empty($result['errors'])) {
            $errors = $result['errors'];
        } else {
            $success = 'Pago creado correctamente.';
            $paymentUrl = $result['payment_url'] ?? null;
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

include __DIR__ . '/../../app/views/flow/new_payment.php';
