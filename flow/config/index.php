<?php
require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/models/FlowConfigModel.php';
require __DIR__ . '/../../app/services/FlowClient.php';
require __DIR__ . '/../../app/controllers/FlowConfigController.php';

if (!flow_user_can_access()) {
    redirect('error-403.php');
}

$model = new FlowConfigModel();
$controller = new FlowConfigController($model);
$errors = [];
$success = null;

$flowConfig = $controller->show();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? 'save';
    $payload = [
        'environment' => $_POST['environment'] ?? 'production',
        'api_key' => $_POST['api_key'] ?? '',
        'secret_key' => $_POST['secret_key'] ?? '',
        'return_url_base' => $_POST['return_url_base'] ?? null,
        'confirmation_url_base' => $_POST['confirmation_url_base'] ?? null,
    ];

    if ($action === 'test') {
        $result = $controller->testConfig($payload);
    } else {
        $result = $controller->save($payload);
    }

    $errors = $result['errors'] ?? [];
    $success = $result['success'] ?? null;
    $flowConfig = $controller->show();
}

include __DIR__ . '/../../app/views/flow/config.php';
