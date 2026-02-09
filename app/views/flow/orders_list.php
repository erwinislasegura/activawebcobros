<?php include __DIR__ . '/../../../partials/html.php'; ?>

<head>
    <?php $title = 'Órdenes Flow'; include __DIR__ . '/../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../partials/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include __DIR__ . '/../../../partials/menu.php'; ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Pagos Flow'; $title = 'Órdenes / Estados'; include __DIR__ . '/../../../partials/page-title.php'; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card gm-section">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Listado de órdenes</h5>
                        </div>
                        <div class="card-body">
                            <form class="row g-3 mb-3" method="get">
                                <div class="col-md-3">
                                    <label class="form-label">Estado</label>
                                    <input type="text" name="status" class="form-control" value="<?php echo htmlspecialchars($filters['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Desde</label>
                                    <input type="date" name="from" class="form-control" value="<?php echo htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" name="to" class="form-control" value="<?php echo htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary">Filtrar</button>
                                    <a href="/flow/orders/index.php" class="btn btn-light">Limpiar</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Orden local</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                            <th>Token</th>
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
                                                    <td><?php echo htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($order['local_order_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars((string) $order['amount'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars((string) $order['flow_token'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td>
                                                        <a href="/flow/orders/detail.php?id=<?php echo htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-sm btn-primary">Ver</a>
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
        <?php include __DIR__ . '/../../../partials/footer.php'; ?>
    </div>
</div>
<?php include __DIR__ . '/../../../partials/customizer.php'; ?>
<?php include __DIR__ . '/../../../partials/footer-scripts.php'; ?>
</body>
</html>
