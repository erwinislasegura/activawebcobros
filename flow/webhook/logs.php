<?php
require __DIR__ . '/../../app/bootstrap.php';
require __DIR__ . '/../../app/models/FlowWebhookLogModel.php';

if (!flow_user_can_access()) {
    redirect('error-403.php');
}

$model = new FlowWebhookLogModel();
$logs = $model->list();

include __DIR__ . '/../../app/views/flow/webhook_logs.php';
