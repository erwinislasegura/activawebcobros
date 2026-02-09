<?php include __DIR__ . '/../../../../partials/html.php'; ?>

<head>
    <?php $title = 'Logs Webhook Flow'; include __DIR__ . '/../../../../partials/title-meta.php'; ?>
    <?php include __DIR__ . '/../../../../partials/head-css.php'; ?>
</head>

<body>
<div class="wrapper">
    <?php include __DIR__ . '/../../../../partials/menu.php'; ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Pagos Flow'; $title = 'Logs Webhook'; include __DIR__ . '/../../../../partials/page-title.php'; ?>

            <div class="row">
                <div class="col-12">
                    <div class="card gm-section">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Eventos recibidos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered align-middle">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Token</th>
                                            <th>Procesado</th>
                                            <th>Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($logs)) : ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Sin logs registrados.</td>
                                            </tr>
                                        <?php else : ?>
                                            <?php foreach ($logs as $log) : ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($log['flow_token'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo ((int) $log['processed'] === 1) ? 'Sí' : 'No'; ?></td>
                                                    <td><?php echo htmlspecialchars((string) $log['processing_notes'], ENT_QUOTES, 'UTF-8'); ?></td>
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
        <?php include __DIR__ . '/../../../../partials/footer.php'; ?>
    </div>
</div>
<?php include __DIR__ . '/../../../../partials/customizer.php'; ?>
<?php include __DIR__ . '/../../../../partials/footer-scripts.php'; ?>
</body>
</html>
