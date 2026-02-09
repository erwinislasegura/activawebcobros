<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/config/flow.php';
require __DIR__ . '/services/FlowClient.php';
require __DIR__ . '/modules/flow/controllers/FlowPaymentsController.php';

$controller = new FlowPaymentsController();
$controller->ensureTables();

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$localOrderId = trim($_GET['local_order_id'] ?? '');

$order = null;
if ($orderId) {
    $order = $controller->getOrderById($orderId);
} elseif ($localOrderId !== '') {
    $order = $controller->getOrderByLocalId($localOrderId);
}

$errors = [];
$statusMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null) && $order) {
    $config = flow_config();
    if ($config['api_key'] === '' || $config['secret_key'] === '') {
        $errors[] = 'Configura las credenciales de Flow para actualizar el estado.';
    } elseif (empty($order['flow_token'])) {
        $errors[] = 'Esta orden no tiene token de Flow asociado.';
    } else {
        $client = new FlowClient($config['api_key'], $config['secret_key'], $config['base_url']);
        $response = $controller->updateStatusFromFlow($client, $order['flow_token']);
        if (!empty($response['success'])) {
            $statusMessage = 'Estado actualizado correctamente.';
            $order = $controller->getOrderById((int) $order['id']);
        } else {
            $errors[] = $response['error'] ?? 'No se pudo consultar el estado en Flow.';
        }
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Detalle orden Flow"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = "Pagos Flow"; $title = "Detalle de orden"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-body">
                                <?php if (!$order) : ?>
                                    <div class="alert alert-warning">No se encontró la orden solicitada.</div>
                                <?php else : ?>
                                    <?php if (!empty($errors)) : ?>
                                        <div class="alert alert-danger">
                                            <?php foreach ($errors as $error) : ?>
                                                <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($statusMessage) : ?>
                                        <div class="alert alert-success">
                                            <?php echo htmlspecialchars($statusMessage, ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="text-muted">Orden local</div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($order['local_order_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-muted">Token Flow</div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars((string) $order['flow_token'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-muted">Estado</div>
                                            <span class="badge bg-info-subtle text-info badge-label"><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-muted">Monto</div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars($order['currency'] . ' ' . $order['amount'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-muted">Email pagador</div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars((string) $order['payer_email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <div class="text-muted">Última actualización</div>
                                            <div class="fw-semibold"><?php echo htmlspecialchars((string) $order['updated_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </div>
                                    </div>

                                    <form method="post" class="mt-3">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="btn btn-primary">Actualizar estado</button>
                                        <a href="flow-payments-list.php" class="btn btn-light">Volver al listado</a>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include('partials/footer.php'); ?>
        </div>
    </div>

    <?php include('partials/customizer.php'); ?>
    <?php include('partials/footer-scripts.php'); ?>
</body>

</html>
