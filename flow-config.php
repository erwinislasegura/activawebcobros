<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/config/flow.php';
require __DIR__ . '/services/FlowClient.php';
require __DIR__ . '/modules/flow/controllers/FlowConfigController.php';

$controller = new FlowConfigController();
$errors = [];
$success = null;
$warning = null;

$config = $controller->getConfig();
$tableError = $controller->getLastError();
if ($tableError) {
    $warning = $tableError;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    if ($tableError) {
        $errors[] = $tableError;
    }
    $environment = strtolower(trim($_POST['environment'] ?? ''));
    $apiKey = trim($_POST['api_key'] ?? '');
    $secretKey = trim($_POST['secret_key'] ?? '');
    $action = trim($_POST['action'] ?? 'save');

    $candidate = [
        'environment' => $environment,
        'api_key' => $apiKey,
        'secret_key' => $secretKey,
    ];

    $errors = $controller->validateConfig($candidate);
    if (empty($errors)) {
        if ($action === 'save') {
            $controller->saveConfig($environment, $apiKey, $secretKey);
            $success = 'Configuración guardada correctamente.';
        } else {
            $success = 'Configuración validada localmente. La firma se generará correctamente.';
        }
    }

    $config = array_merge($config, $candidate, [
        'base_url' => flow_base_url($environment),
    ]);
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Configuración Flow"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = "Pagos Flow"; $title = "Configuración"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Credenciales Flow</h5>
                                    <p class="text-muted mb-0">Define el ambiente y las llaves de integración para pagos.</p>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="submit" form="flow-config-form" name="action" value="check" class="btn btn-outline-secondary">Probar conexión</button>
                                    <button type="submit" form="flow-config-form" name="action" value="save" class="btn btn-primary">Guardar configuración</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($warning) : ?>
                                    <div class="alert alert-warning">
                                        <?php echo htmlspecialchars($warning, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($success) : ?>
                                    <div class="alert alert-success">
                                        <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                <?php endif; ?>

                                <form id="flow-config-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="flow-env">Ambiente</label>
                                            <select id="flow-env" name="environment" class="form-select">
                                                <option value="sandbox" <?php echo ($config['environment'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>Sandbox</option>
                                                <option value="production" <?php echo ($config['environment'] ?? '') === 'production' ? 'selected' : ''; ?>>Producción</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="flow-api-key">API Key</label>
                                            <input type="password" id="flow-api-key" name="api_key" class="form-control" value="<?php echo htmlspecialchars($config['api_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label" for="flow-secret-key">Secret Key</label>
                                            <input type="password" id="flow-secret-key" name="secret_key" class="form-control" value="<?php echo htmlspecialchars($config['secret_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="flow-base-url">Base URL</label>
                                            <input type="text" id="flow-base-url" class="form-control" value="<?php echo htmlspecialchars($config['base_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                            <div class="form-text">Calculada según el ambiente seleccionado.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Origen de configuración</label>
                                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($config['source'] ?? 'env', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                        </div>
                                    </div>
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
