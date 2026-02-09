<?php include __DIR__ . '/../../../../partials/html.php'; ?>

<head>
    <?php $title = 'Crear pago Flow'; include __DIR__ . '/../../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../../partials/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include __DIR__ . '/../../../../partials/menu.php'; ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Pagos Flow'; $title = 'Crear pago'; include __DIR__ . '/../../../../partials/page-title.php'; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card gm-section">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Nueva orden en Flow</h5>
                            <p class="text-muted mb-0">Completa los datos para crear una orden y obtener el link de pago.</p>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($errors)) : ?>
                                <div class="alert alert-danger">
                                    <?php foreach ($errors as $error) : ?>
                                        <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($success)) : ?>
                                <div class="alert alert-success">
                                    <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                                </div>
                            <?php endif; ?>

                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="flow-local-order">Orden local</label>
                                        <input type="text" id="flow-local-order" name="local_order_id" class="form-control" required value="<?php echo htmlspecialchars($form['local_order_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="flow-amount">Monto</label>
                                        <input type="number" min="1" id="flow-amount" name="amount" class="form-control" required value="<?php echo htmlspecialchars((string) ($form['amount'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="flow-currency">Moneda</label>
                                        <input type="text" id="flow-currency" name="currency" class="form-control" value="<?php echo htmlspecialchars($form['currency'] ?? 'CLP', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="flow-subject">Descripción / Glosa</label>
                                        <input type="text" id="flow-subject" name="subject" class="form-control" required value="<?php echo htmlspecialchars($form['subject'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="flow-email">Email pagador</label>
                                        <input type="email" id="flow-email" name="customer_email" class="form-control" value="<?php echo htmlspecialchars($form['customer_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Crear pago</button>
                                <a href="/flow/orders/index.php" class="btn btn-light">Ver órdenes</a>
                            </form>

                            <?php if (!empty($paymentUrl)) : ?>
                                <div class="mt-4">
                                    <a class="btn btn-success" href="<?php echo htmlspecialchars($paymentUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Ir a pagar</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include __DIR__ . '/../../../../partials/footer.php'; ?>
    </div>
</div>
<?php include __DIR__ . '/../../../../partials/customizer.php'; ?>
<?php include __DIR__ . '/../../../../partials/footer-scripts.php'; ?>
</body>
</html>
