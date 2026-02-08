<?php
require __DIR__ . '/app/bootstrap.php';

$query = trim($_GET['q'] ?? '');
$hasQuery = $query !== '';
$results = [
    'vistas' => [],
    'eventos' => [],
    'autoridades' => [],
    'usuarios' => [],
    'tipos_evento' => [],
    'grupos_autoridades' => [],
    'roles' => [],
];

$menuViews = [
    ['label' => 'Nuevo evento', 'url' => 'eventos-editar.php'],
    ['label' => 'Invitar autoridades', 'url' => 'eventos-invitacion-autoridades.php'],
    ['label' => 'Autoridades evento', 'url' => 'eventos-autoridades-nueva.php'],
    ['label' => 'Resumen eventos', 'url' => 'eventos-autoridades-resumen.php'],
    ['label' => 'Procesados', 'url' => 'eventos-procesados.php'],
    ['label' => 'Crear autoridad', 'url' => 'autoridades-editar.php'],
    ['label' => 'Carga masiva autoridades', 'url' => 'autoridades-carga-masiva.php'],
    ['label' => 'Calendario', 'url' => 'calendar.php'],
    ['label' => 'Crear usuario', 'url' => 'usuarios-crear.php'],
    ['label' => 'Crear rol', 'url' => 'roles-editar.php'],
    ['label' => 'Matriz permisos', 'url' => 'roles-permisos.php'],
    ['label' => 'Municipalidad', 'url' => 'municipalidad.php'],
    ['label' => 'Tipos de evento', 'url' => 'eventos-tipos.php'],
    ['label' => 'Grupos autoridades', 'url' => 'grupos-autoridades.php'],
    ['label' => 'Correo de envío', 'url' => 'notificaciones-correo.php'],
    ['label' => 'Correo invitación', 'url' => 'invitacion-correo.php'],
    ['label' => 'WhatsApp envíos', 'url' => 'notificaciones-whatsapp.php'],
    ['label' => 'Correo validación', 'url' => 'configuracion-email.php'],
];

if ($hasQuery) {
    $needle = mb_strtolower($query, 'UTF-8');
    foreach ($menuViews as $view) {
        if (mb_strpos(mb_strtolower($view['label'], 'UTF-8'), $needle) !== false) {
            $results['vistas'][] = $view;
        }
    }

    $like = '%' . $query . '%';

    $stmt = db()->prepare('SELECT id, titulo, fecha_inicio FROM events WHERE titulo LIKE ? OR descripcion LIKE ? ORDER BY fecha_inicio DESC LIMIT 10');
    $stmt->execute([$like, $like]);
    $results['eventos'] = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT id, nombre, tipo, correo FROM authorities WHERE nombre LIKE ? OR tipo LIKE ? OR correo LIKE ? ORDER BY nombre LIMIT 10');
    $stmt->execute([$like, $like, $like]);
    $results['autoridades'] = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT id, nombre, apellido, correo FROM users WHERE nombre LIKE ? OR apellido LIKE ? OR correo LIKE ? ORDER BY nombre LIMIT 10');
    $stmt->execute([$like, $like, $like]);
    $results['usuarios'] = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT id, nombre FROM event_types WHERE nombre LIKE ? ORDER BY nombre LIMIT 10');
    $stmt->execute([$like]);
    $results['tipos_evento'] = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT id, nombre FROM authority_groups WHERE nombre LIKE ? ORDER BY nombre LIMIT 10');
    $stmt->execute([$like]);
    $results['grupos_autoridades'] = $stmt->fetchAll();

    $stmt = db()->prepare('SELECT id, nombre FROM roles WHERE nombre LIKE ? ORDER BY nombre LIMIT 10');
    $stmt->execute([$like]);
    $results['roles'] = $stmt->fetchAll();
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Búsqueda'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">

                <?php $subtitle = 'Búsqueda'; $title = 'Buscar en el sistema'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-body">
                                <form method="get" class="row g-3 align-items-center">
                                    <div class="col-md-10">
                                        <label class="form-label" for="search-input">Buscar</label>
                                        <input id="search-input" type="search" name="q" class="form-control" placeholder="Buscar eventos, autoridades, usuarios o vistas" value="<?php echo htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!$hasQuery) : ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info">Ingresa un término para iniciar la búsqueda.</div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="row g-3 mt-1">
                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Vistas del sistema</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['vistas'])) : ?>
                                        <div class="text-muted">Sin coincidencias en vistas.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['vistas'] as $view) : ?>
                                                <a class="list-group-item list-group-item-action d-flex align-items-center justify-content-between" href="<?php echo htmlspecialchars($view['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    <span><?php echo htmlspecialchars($view['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                    <i class="ti ti-chevron-right text-muted"></i>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Eventos</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['eventos'])) : ?>
                                        <div class="text-muted">Sin coincidencias en eventos.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['eventos'] as $event) : ?>
                                                <a class="list-group-item list-group-item-action" href="eventos-editar.php?id=<?php echo (int) $event['id']; ?>">
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($event['fecha_inicio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Autoridades</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['autoridades'])) : ?>
                                        <div class="text-muted">Sin coincidencias en autoridades.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['autoridades'] as $authority) : ?>
                                                <a class="list-group-item list-group-item-action" href="autoridades-editar.php?id=<?php echo (int) $authority['id']; ?>">
                                                    <div class="fw-semibold"><?php echo htmlspecialchars($authority['nombre'], ENT_QUOTES, 'UTF-8'); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($authority['tipo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($authority['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Usuarios</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['usuarios'])) : ?>
                                        <div class="text-muted">Sin coincidencias en usuarios.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['usuarios'] as $user) : ?>
                                                <a class="list-group-item list-group-item-action" href="usuarios-editar.php?id=<?php echo (int) $user['id']; ?>">
                                                    <div class="fw-semibold"><?php echo htmlspecialchars(trim(($user['nombre'] ?? '') . ' ' . ($user['apellido'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                                                    <div class="text-muted small"><?php echo htmlspecialchars($user['correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                </a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Tipos de evento</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['tipos_evento'])) : ?>
                                        <div class="text-muted">Sin coincidencias en tipos de evento.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['tipos_evento'] as $tipo) : ?>
                                                <a class="list-group-item list-group-item-action" href="eventos-tipos.php"><?php echo htmlspecialchars($tipo['nombre'], ENT_QUOTES, 'UTF-8'); ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Grupos de autoridades</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['grupos_autoridades'])) : ?>
                                        <div class="text-muted">Sin coincidencias en grupos.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['grupos_autoridades'] as $group) : ?>
                                                <a class="list-group-item list-group-item-action" href="grupos-autoridades.php"><?php echo htmlspecialchars($group['nombre'], ENT_QUOTES, 'UTF-8'); ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card gm-section">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Roles</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($results['roles'])) : ?>
                                        <div class="text-muted">Sin coincidencias en roles.</div>
                                    <?php else : ?>
                                        <div class="list-group list-group-flush">
                                            <?php foreach ($results['roles'] as $role) : ?>
                                                <a class="list-group-item list-group-item-action" href="roles-editar.php?id=<?php echo (int) $role['id']; ?>"><?php echo htmlspecialchars($role['nombre'], ENT_QUOTES, 'UTF-8'); ?></a>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <?php include('partials/footer.php'); ?>
        </div>
    </div>

    <?php include('partials/footer-scripts.php'); ?>
</body>

</html>
