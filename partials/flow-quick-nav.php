<?php
$flowCurrentStep = isset($flowCurrentStep) ? (string) $flowCurrentStep : '';
$flowSteps = [
    ['key' => 'clientes', 'label' => '1) Cliente', 'href' => 'clientes-crear.php'],
    ['key' => 'asociaciones', 'label' => '2) Asociación', 'href' => 'clientes-servicios-asociar.php'],
    ['key' => 'servicios', 'label' => '3) Servicios', 'href' => 'cobros-servicios-agregar.php'],
    ['key' => 'cobros', 'label' => '4) Cobros', 'href' => 'cobros-servicios-registros.php'],
    ['key' => 'pagos', 'label' => '5) Pagos', 'href' => 'cobros-pagos.php'],
    ['key' => 'avisos', 'label' => '6) Avisos', 'href' => 'cobros-avisos.php'],
    ['key' => 'totales', 'label' => '7) Totales', 'href' => 'cobros-totales.php'],
];
?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($flowSteps as $step) : ?>
                    <?php $isCurrent = $step['key'] === $flowCurrentStep; ?>
                    <a class="btn btn-sm <?php echo $isCurrent ? 'btn-primary' : 'btn-outline-primary'; ?>" href="<?php echo htmlspecialchars($step['href'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo $isCurrent ? 'aria-current="page"' : ''; ?>>
                        <?php echo htmlspecialchars($step['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
