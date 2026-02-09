<?php include __DIR__ . '/../../../partials/html.php'; ?>

<head>
    <?php $title = 'Detalle orden Flow'; include __DIR__ . '/../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../partials/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include __DIR__ . '/../../../partials/menu.php'; ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Pagos Flow'; $title = 'Detalle de orden'; include __DIR__ . '/../../../partials/page-title.php'; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card gm-section">
                        <div class="card-body">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger">
                                    <?php foreach ($errors as $error) : ?>
                                        <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!$order) : ?>
                                <div class="alert alert-warning">No se encontró la orden solicitada.</div>
                            <?php else : ?>
                                <?php if (!empty($statusMessage)) : ?>
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
                                        <div class="fw-semibold"><?php echo htmlspecialchars((string) $order['customer_email'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="text-muted">Última actualización</div>
                                        <div class="fw-semibold"><?php echo htmlspecialchars((string) $order['updated_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    </div>
                                </div>

                                <form method="post" class="mt-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="btn btn-primary">Actualizar estado</button>
                                    <a href="/flow/orders/index.php" class="btn btn-light">Volver al listado</a>
                                </form>

                                <?php if (is_superuser()) : ?>
                                    <div class="mt-4">
                                        <h6>Última respuesta Flow</h6>
                                        <pre class="bg-light p-3 rounded"><?php echo htmlspecialchars($lastStatusJson, ENT_QUOTES, 'UTF-8'); ?></pre>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
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
