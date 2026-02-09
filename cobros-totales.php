<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';
$totalesCobros = [];

try {
    $totalesCobros = db()->query(
        'SELECT COALESCE(c.nombre, cs.cliente) AS cliente,
                c.codigo AS cliente_codigo,
                c.color_hex AS cliente_color,
                s.nombre AS servicio,
                COUNT(cs.id) AS cobros_total,
                SUM(cs.monto) AS monto_cobros,
                COALESCE(SUM(p.monto), 0) AS monto_pagos
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         LEFT JOIN pagos_clientes p ON p.cobro_id = cs.id
         GROUP BY cliente, cliente_codigo, cliente_color, servicio
         ORDER BY cliente ASC, servicio ASC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = 'No se pudieron cargar los totales de cobros.';
} catch (Error $e) {
    $errorMessage = 'No se pudieron cargar los totales de cobros.';
}

$moneyFormatter = static function (float $value): string {
    return '$' . number_format($value, 2, ',', '.');
};

$sumCobros = 0;
$sumMontoCobros = 0.0;
$sumMontoPagos = 0.0;
$sumSaldo = 0.0;

foreach ($totalesCobros as $total) {
    $montoCobrado = (float) ($total['monto_cobros'] ?? 0);
    $montoPagado = (float) ($total['monto_pagos'] ?? 0);
    $saldo = $montoCobrado - $montoPagado;
    $sumCobros += (int) ($total['cobros_total'] ?? 0);
    $sumMontoCobros += $montoCobrado;
    $sumMontoPagos += $montoPagado;
    $sumSaldo += $saldo;
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Totales por cliente"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Cobros de servicios"; $title = "Totales por cliente y servicio"; include('partials/page-title.php'); ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title mb-0">Totales por cliente y servicio</h5>
                            <p class="text-muted mb-0">Resumen consolidado de cobros y pagos por cliente.</p>
                        </div>
                        <span class="badge text-bg-primary"><?php echo count($totalesCobros); ?> filas</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Cobros</th>
                                        <th>Monto cobrado</th>
                                        <th>Monto pagado</th>
                                        <th>Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($totalesCobros)) : ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No hay totales disponibles.</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($totalesCobros as $total) : ?>
                                            <?php
                                            $montoCobrado = (float) ($total['monto_cobros'] ?? 0);
                                            $montoPagado = (float) ($total['monto_pagos'] ?? 0);
                                            $saldo = $montoCobrado - $montoPagado;
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($total['cliente_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8'); ?>;">
                                                        <?php echo htmlspecialchars($total['cliente_codigo'] ?? 'SIN-COD', ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <?php echo htmlspecialchars($total['cliente'], ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($total['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo (int) ($total['cobros_total'] ?? 0); ?></td>
                                                <td><?php echo $moneyFormatter($montoCobrado); ?></td>
                                                <td><?php echo $moneyFormatter($montoPagado); ?></td>
                                                <td>
                                                    <span class="fw-semibold <?php echo $saldo > 0 ? 'text-danger' : 'text-success'; ?>">
                                                        <?php echo $moneyFormatter($saldo); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                                <?php if (!empty($totalesCobros)) : ?>
                                    <tfoot>
                                        <tr class="table-light">
                                            <th colspan="2" class="text-end">Totales</th>
                                            <th><?php echo $sumCobros; ?></th>
                                            <th><?php echo $moneyFormatter($sumMontoCobros); ?></th>
                                            <th><?php echo $moneyFormatter($sumMontoPagos); ?></th>
                                            <th>
                                                <span class="fw-semibold <?php echo $sumSaldo > 0 ? 'text-danger' : 'text-success'; ?>">
                                                    <?php echo $moneyFormatter($sumSaldo); ?>
                                                </span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
