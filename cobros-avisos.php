<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';
$cobros = [];

try {
    $cobros = db()->query(
        'SELECT cs.id,
                COALESCE(c.nombre, cs.cliente) AS cliente,
                c.codigo AS cliente_codigo,
                c.color_hex AS cliente_color,
                c.correo AS cliente_correo,
                cs.referencia,
                cs.fecha_primer_aviso,
                cs.fecha_segundo_aviso,
                cs.fecha_tercer_aviso,
                cs.estado,
                s.nombre AS servicio
         FROM cobros_servicios cs
         LEFT JOIN clientes c ON c.id = cs.cliente_id
         JOIN servicios s ON s.id = cs.servicio_id
         ORDER BY cs.id DESC'
    )->fetchAll();
} catch (Exception $e) {
    $errorMessage = 'No se pudieron cargar los avisos.';
} catch (Error $e) {
    $errorMessage = 'No se pudieron cargar los avisos.';
}

function build_aviso_mailto(?string $correo, string $cliente, string $servicio, string $fechaAviso, string $referencia, string $tipo): string
{
    if ($correo === null || $correo === '') {
        return '#';
    }
    $subject = rawurlencode(sprintf('Aviso %s - %s', $tipo, $servicio));
    $body = rawurlencode(sprintf("Hola %s,%0D%0A%0D%0ATe recordamos el aviso %s del servicio %s.%0D%0AReferencia: %s%0D%0AFecha aviso: %s%0D%0A%0D%0AGracias.", $cliente, $tipo, $servicio, $referencia, $fechaAviso));
    return 'mailto:' . rawurlencode($correo) . '?subject=' . $subject . '&body=' . $body;
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Listado de avisos"; include('partials/title-meta.php'); ?>

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

                <?php $subtitle = "Cobros de servicios"; $title = "Listado de avisos"; include('partials/page-title.php'); ?>

                <?php if ($errorMessage !== '') : ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between gap-2">
                        <div>
                            <h5 class="card-title mb-0">Avisos por cliente</h5>
                            <p class="text-muted mb-0">Gestiona y envía avisos asociados a cobros.</p>
                        </div>
                        <span class="badge text-bg-primary"><?php echo count($cobros); ?> registros</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-centered mb-0">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>Referencia</th>
                                        <th>Primer aviso</th>
                                        <th>Segundo aviso</th>
                                        <th>Tercer aviso</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($cobros)) : ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay avisos registrados.</td>
                                        </tr>
                                    <?php else : ?>
                                        <?php foreach ($cobros as $cobro) : ?>
                                            <?php
                                            $correo = $cobro['cliente_correo'] ?? '';
                                            $cliente = (string) $cobro['cliente'];
                                            $servicio = (string) $cobro['servicio'];
                                            $referencia = (string) ($cobro['referencia'] ?? '');
                                            $primer = $cobro['fecha_primer_aviso'] ? date('d/m/Y', strtotime($cobro['fecha_primer_aviso'])) : '-';
                                            $segundo = $cobro['fecha_segundo_aviso'] ? date('d/m/Y', strtotime($cobro['fecha_segundo_aviso'])) : '-';
                                            $tercero = $cobro['fecha_tercer_aviso'] ? date('d/m/Y', strtotime($cobro['fecha_tercer_aviso'])) : '-';
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="badge" style="background-color: <?php echo htmlspecialchars($cobro['cliente_color'] ?? '#6c757d', ENT_QUOTES, 'UTF-8'); ?>;">
                                                        <?php echo htmlspecialchars($cobro['cliente_codigo'] ?? 'SIN-COD', ENT_QUOTES, 'UTF-8'); ?>
                                                    </span>
                                                    <?php echo htmlspecialchars($cliente, ENT_QUOTES, 'UTF-8'); ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($servicio, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($referencia, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($primer, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($segundo, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($tercero, ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td class="text-nowrap">
                                                    <?php
                                                    $primerUrl = $cobro['fecha_primer_aviso'] ? build_aviso_mailto($correo, $cliente, $servicio, $primer, $referencia, '1') : '#';
                                                    $segundoUrl = $cobro['fecha_segundo_aviso'] ? build_aviso_mailto($correo, $cliente, $servicio, $segundo, $referencia, '2') : '#';
                                                    $terceroUrl = $cobro['fecha_tercer_aviso'] ? build_aviso_mailto($correo, $cliente, $servicio, $tercero, $referencia, '3') : '#';
                                                    $disabled = ($correo === '' || $correo === null);
                                                    ?>
                                                    <a class="btn btn-sm btn-outline-primary <?php echo $disabled ? 'disabled' : ''; ?>" href="<?php echo htmlspecialchars($primerUrl, ENT_QUOTES, 'UTF-8'); ?>">Aviso 1</a>
                                                    <a class="btn btn-sm btn-outline-warning <?php echo $disabled ? 'disabled' : ''; ?>" href="<?php echo htmlspecialchars($segundoUrl, ENT_QUOTES, 'UTF-8'); ?>">Aviso 2</a>
                                                    <a class="btn btn-sm btn-outline-danger <?php echo $disabled ? 'disabled' : ''; ?>" href="<?php echo htmlspecialchars($terceroUrl, ENT_QUOTES, 'UTF-8'); ?>">Aviso 3</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted d-block mt-2">Los botones de envío se habilitan si el cliente tiene correo registrado.</small>
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
