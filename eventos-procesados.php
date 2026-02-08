<?php
require __DIR__ . '/app/bootstrap.php';

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && verify_csrf($_POST['csrf_token'] ?? null)) {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    if ($_POST['action'] === 'delete' && $id > 0) {
        try {
            $stmt = db()->prepare('DELETE FROM events WHERE id = ?');
            $stmt->execute([$id]);
            redirect('eventos-procesados.php');
        } catch (Exception $e) {
            $errorMessage = 'No se pudo eliminar el evento. Verifica dependencias asociadas.';
        }
    }
}

$stmt = db()->query(
    'SELECT e.id,
            e.titulo,
            r.destinatario_nombre,
            r.destinatario_correo,
            r.responded_at,
            GROUP_CONCAT(DISTINCT CONCAT(a.nombre, " · ", a.tipo) ORDER BY a.nombre SEPARATOR ", ") AS autoridades
     FROM event_authority_requests r
     INNER JOIN events e ON e.id = r.event_id
     INNER JOIN event_authority_confirmations c ON c.request_id = r.id
     INNER JOIN authorities a ON a.id = c.authority_id
     WHERE r.estado = "respondido"
     GROUP BY r.id
     ORDER BY r.responded_at DESC'
);
$procesados = $stmt->fetchAll();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Eventos procesados"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">

                <?php $subtitle = "Eventos Municipales"; $title = "Eventos procesados"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Eventos con validación confirmada</h5>
                                    <p class="text-muted mb-0">Registro de autoridades confirmadas por evento.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php if ($errorMessage !== '') : ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Evento</th>
                                                <th>Autoridades confirmadas</th>
                                                <th>Destinatario</th>
                                                <th>Respondido</th>
                                                <th class="text-end">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($procesados)) : ?>
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted">No hay eventos procesados.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($procesados as $registro) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($registro['titulo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($registro['autoridades'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php echo htmlspecialchars($registro['destinatario_nombre'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                                            <div class="text-muted small"><?php echo htmlspecialchars($registro['destinatario_correo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                                        </td>
                                                        <td><?php echo htmlspecialchars($registro['responded_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-end">
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-soft-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Acciones
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end">
                                                                    <li><a class="dropdown-item" href="eventos-detalle.php?id=<?php echo (int) $registro['id']; ?>">Ver</a></li>
                                                                    <li><a class="dropdown-item" href="eventos-editar.php?id=<?php echo (int) $registro['id']; ?>">Editar</a></li>
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <li>
                                                                        <form method="post" class="px-3 py-1" data-confirm="¿Estás seguro de eliminar este evento?">
                                                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                                            <input type="hidden" name="action" value="delete">
                                                                            <input type="hidden" name="id" value="<?php echo (int) $registro['id']; ?>">
                                                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">Eliminar</button>
                                                                        </form>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
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

            <?php include('partials/footer.php'); ?>

        </div>

    </div>

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>
</body>

</html>
