<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/config/flow.php';
require __DIR__ . '/services/FlowClient.php';
require __DIR__ . '/modules/flow/controllers/FlowPaymentsController.php';

$controller = new FlowPaymentsController();
$controller->ensureTables();
$tableError = $controller->getLastError();

$statusFilter = trim($_GET['status'] ?? '');
$fromFilter = trim($_GET['from'] ?? '');
$toFilter = trim($_GET['to'] ?? '');

$orders = $controller->listOrders([
    'status' => $statusFilter,
    'from' => $fromFilter,
    'to' => $toFilter,
]);
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Órdenes Flow"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = "Pagos Flow"; $title = "Órdenes / Estados"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Listado de órdenes</h5>
                            </div>
                            <div class="card-body">
                                <?php if ($tableError) : ?>
                                    <div class="alert alert-warning">
                                        <?php echo htmlspecialchars($tableError, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>
                                <form class="row g-3 mb-3" method="get">
                                    <div class="col-md-3">
                                        <label class="form-label">Estado</label>
                                        <input type="text" name="status" class="form-control" value="<?php echo htmlspecialchars($statusFilter, ENT_QUOTES, 'UTF-8'); ?>" placeholder="pending">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Desde</label>
                                        <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($fromFilter, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Hasta</label>
                                        <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($toFilter, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-primary">Filtrar</button>
                                        <a href="flow-payments-list.php" class="btn btn-light">Limpiar</a>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered align-middle">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Orden local</th>
                                                <th>Monto</th>
                                                <th>Moneda</th>
                                                <th>Estado</th>
                                                <th>Creada</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($orders)) : ?>
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">Sin órdenes registradas.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($orders as $order) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($order['local_order_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) $order['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($order['currency'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <a href="flow-payments-detail.php?id=<?php echo htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-primary">Ver</a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
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
