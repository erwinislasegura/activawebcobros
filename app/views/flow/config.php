<?php include __DIR__ . '/../../../../partials/html.php'; ?>

<head>
    <?php $title = 'Configuración Flow'; include __DIR__ . '/../../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../../partials/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include __DIR__ . '/../../../../partials/menu.php'; ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Pagos Flow'; $title = 'Configuración'; include __DIR__ . '/../../../../partials/page-title.php'; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card gm-section">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <div>
                                <h5 class="card-title mb-0">Credenciales Flow</h5>
                                <p class="text-muted mb-0">Define el ambiente y las llaves de integración para pagos.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" form="flow-config-form" name="action" value="test" class="btn btn-outline-secondary">Probar configuración</button>
                                <button type="submit" form="flow-config-form" name="action" value="save" class="btn btn-primary">Guardar</button>
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

                            <?php if (!empty($success)) : ?>
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
                                            <option value="production" <?php echo ($flowConfig['environment'] ?? '') === 'production' ? 'selected' : ''; ?>>Producción</option>
                                            <option value="sandbox" <?php echo ($flowConfig['environment'] ?? '') === 'sandbox' ? 'selected' : ''; ?>>Sandbox</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="flow-api-key">API Key</label>
                                        <input type="password" id="flow-api-key" name="api_key" class="form-control" value="<?php echo htmlspecialchars($flowConfig['api_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label" for="flow-secret-key">Secret Key</label>
                                        <input type="password" id="flow-secret-key" name="secret_key" class="form-control" value="<?php echo htmlspecialchars($flowConfig['secret_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off">
                                        <?php if (!empty($flowConfig['masked_secret_key'])) : ?>
                                            <div class="form-text">Actual: <?php echo htmlspecialchars($flowConfig['masked_secret_key'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="flow-return-base">Base URL retorno</label>
                                        <input type="text" id="flow-return-base" name="return_url_base" class="form-control" value="<?php echo htmlspecialchars($flowConfig['return_url_base'] ?? base_url(), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="flow-confirm-base">Base URL confirmación</label>
                                        <input type="text" id="flow-confirm-base" name="confirmation_url_base" class="form-control" value="<?php echo htmlspecialchars($flowConfig['confirmation_url_base'] ?? base_url(), ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL confirmación (registrar en Flow)</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(rtrim($flowConfig['confirmation_url_base'] ?? base_url(), '/') . '/flow/webhook/confirmation.php', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">URL retorno</label>
                                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(rtrim($flowConfig['return_url_base'] ?? base_url(), '/') . '/flow/payments/return.php', ENT_QUOTES, 'UTF-8'); ?>" readonly>
                                    </div>
                                </div>
                            </form>
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
