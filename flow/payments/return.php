<?php
require __DIR__ . '/../../app/bootstrap.php';

if (!flow_user_can_access()) {
    redirect('error-403.php');
}

$localOrderId = trim($_GET['local_order_id'] ?? '');
redirect('/flow/orders/index.php?status=pending');
