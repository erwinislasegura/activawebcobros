<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/config/flow.php';
require __DIR__ . '/services/FlowClient.php';
require __DIR__ . '/modules/flow/controllers/FlowPaymentsController.php';

$errors = [];
$message = null;
$linkUrl = null;

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf($_POST['csrf_token'] ?? null)) {
    redirect('flow-payments-new.php');
}

$amount = (float) ($_POST['amount'] ?? 0);
$currency = strtoupper(trim($_POST['currency'] ?? 'CLP'));
$subject = trim($_POST['subject'] ?? '');
$localOrderId = trim($_POST['local_order_id'] ?? '');
$email = trim($_POST['email'] ?? '');

if ($amount <= 0) {
    $errors[] = 'El monto debe ser mayor a cero.';
}
if ($subject === '' || $localOrderId === '') {
    $errors[] = 'Completa la descripción y la orden local.';
}
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'El email ingresado no es válido.';
}

$config = flow_config();
if ($config['api_key'] === '' || $config['secret_key'] === '') {
    $errors[] = 'Configura la API Key y Secret Key en Flow antes de crear pagos.';
}

$controller = new FlowPaymentsController();
$controller->ensureTables();
$tableError = $controller->getLastError();
if ($tableError) {
    $errors[] = $tableError;
}

if (empty($errors)) {
    $client = new FlowClient($config['api_key'], $config['secret_key'], $config['base_url']);
    $payload = [
        'apiKey' => $config['api_key'],
        'commerceOrder' => $localOrderId,
        'subject' => $subject,
        'amount' => $amount,
        'currency' => $currency,
        'email' => $email,
        'urlReturn' => base_url() . '/flow-payments-detail.php?local_order_id=' . urlencode($localOrderId),
        'urlConfirmation' => base_url() . '/flow-webhook-confirmation.php',
    ];

    $result = $controller->createPayment($client, $payload);
    $response = $result['response'] ?? [];

    if (!empty($response['success'])) {
        $data = $response['data'] ?? [];
        $paymentUrl = $data['url'] ?? null;
        $token = $data['token'] ?? null;
        if ($paymentUrl && $token) {
            $linkUrl = $paymentUrl . '?token=' . urlencode($token);
        }
        $message = 'Pago creado correctamente.';
    } else {
        $errors[] = $response['error'] ?? 'No fue posible crear la orden en Flow.';
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Resultado pago Flow"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = "Pagos Flow"; $title = "Resultado creación"; include('partials/page-title.php'); ?>

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

                                <?php if ($message) : ?>
                                    <div class="alert alert-success">
                                        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($linkUrl) : ?>
                                    <p class="mb-3">Enlace de pago generado:</p>
                                    <a class="btn btn-primary" href="<?php echo htmlspecialchars($linkUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Ir al pago en Flow</a>
                                <?php endif; ?>

                                <div class="mt-4">
                                    <a href="flow-payments-new.php" class="btn btn-light">Crear otro pago</a>
                                    <a href="flow-payments-list.php" class="btn btn-secondary">Ver órdenes</a>
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
