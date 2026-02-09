<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/config/flow.php';
require __DIR__ . '/services/FlowClient.php';
require __DIR__ . '/modules/flow/controllers/FlowPaymentsController.php';

$controller = new FlowPaymentsController();
$controller->ensureTables();

$config = flow_config();

$formData = [
    'amount' => '',
    'currency' => 'CLP',
    'subject' => '',
    'local_order_id' => '',
    'email' => '',
];

?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Crear pago Flow"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = "Pagos Flow"; $title = "Crear pago"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Nueva orden en Flow</h5>
                                <p class="text-muted mb-0">Completa los datos para crear una orden y obtener el link de pago.</p>
                            </div>
                            <div class="card-body">
                                <form method="post" action="flow-payments-create.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="flow-amount">Monto</label>
                                            <input type="number" step="0.01" min="0" id="flow-amount" name="amount" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="flow-currency">Moneda</label>
                                            <input type="text" id="flow-currency" name="currency" class="form-control" value="<?php echo htmlspecialchars($formData['currency'], ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="flow-local-order">Orden local</label>
                                            <input type="text" id="flow-local-order" name="local_order_id" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="flow-subject">Descripción / Glosa</label>
                                            <input type="text" id="flow-subject" name="subject" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="flow-email">Email pagador</label>
                                            <input type="email" id="flow-email" name="email" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">URL de retorno</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(base_url() . '/flow-payments-detail.php', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                            <div class="form-text">Flow redirigirá al finalizar el pago.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">URL de confirmación</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars(base_url() . '/flow-webhook-confirmation.php', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                            <div class="form-text">Configura esta URL en Flow como <strong>urlConfirmation</strong>.</div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Crear pago</button>
                                    <a href="flow-payments-list.php" class="btn btn-light">Ver órdenes</a>
                                </form>
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
