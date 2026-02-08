<?php
require __DIR__ . '/app/bootstrap.php';

$token = trim($_GET['token'] ?? '');
$errors = [];
$notice = '';
$attendance = null;
$attendanceStatus = null;

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS event_authority_attendance (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id INT UNSIGNED NOT NULL,
            authority_id INT UNSIGNED NOT NULL,
            token VARCHAR(64) NOT NULL,
            status ENUM("pendiente", "confirmado", "rechazado") NOT NULL DEFAULT "pendiente",
            responded_at TIMESTAMP NULL DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY event_authority_attendance_unique (event_id, authority_id),
            UNIQUE KEY event_authority_attendance_token_unique (token),
            CONSTRAINT event_authority_attendance_event_id_fk FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE,
            CONSTRAINT event_authority_attendance_authority_id_fk FOREIGN KEY (authority_id) REFERENCES authorities (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

if ($token !== '') {
    $stmtAttendance = db()->prepare(
        'SELECT ea.id, ea.status, e.titulo, e.ubicacion, e.fecha_inicio, e.fecha_fin
         FROM event_authority_attendance ea
         INNER JOIN events e ON e.id = ea.event_id
         WHERE ea.token = ?'
    );
    $stmtAttendance->execute([$token]);
    $attendance = $stmtAttendance->fetch() ?: null;
    $attendanceStatus = $attendance['status'] ?? null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';
    $token = trim($_POST['token'] ?? $token);

    if ($token !== '' && !$attendance) {
        $stmtAttendance = db()->prepare(
            'SELECT ea.id, ea.status, e.titulo, e.ubicacion, e.fecha_inicio, e.fecha_fin
             FROM event_authority_attendance ea
             INNER JOIN events e ON e.id = ea.event_id
             WHERE ea.token = ?'
        );
        $stmtAttendance->execute([$token]);
        $attendance = $stmtAttendance->fetch() ?: null;
        $attendanceStatus = $attendance['status'] ?? null;
    }

    if ($token === '' || !$attendance) {
        $errors[] = 'El enlace de confirmación no es válido.';
    } elseif (!in_array($action, ['confirm', 'decline'], true)) {
        $errors[] = 'Selecciona una respuesta válida.';
    } else {
        $newStatus = $action === 'confirm' ? 'confirmado' : 'rechazado';
        $stmtUpdate = db()->prepare('UPDATE event_authority_attendance SET status = ?, responded_at = NOW() WHERE id = ?');
        $stmtUpdate->execute([$newStatus, (int) $attendance['id']]);
        $attendanceStatus = $newStatus;
        $notice = $action === 'confirm'
            ? 'Tu participación ha sido confirmada. ¡Gracias por tu respuesta!'
            : 'Tu respuesta ha sido registrada. Gracias por informarnos.';
    }
}

$municipalidad = get_municipalidad();
$logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
$logoUrl = preg_match('/^https?:\/\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Confirmar asistencia"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="auth-box overflow-hidden align-items-center d-flex">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-5 col-md-7 col-sm-9">
                    <div class="card p-4">
                        <div class="text-center mb-4">
                            <img src="<?php echo htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo municipalidad" height="36">
                            <h4 class="fw-bold mt-3 mb-1">Confirmación de participación</h4>
                            <p class="text-muted mb-0">Municipalidad de <?php echo htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>

                        <?php if (!empty($errors)) : ?>
                            <div class="alert alert-danger">
                                <?php foreach ($errors as $error) : ?>
                                    <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($notice !== '') : ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>

                        <?php if ($attendance) : ?>
                            <div class="border rounded-3 p-3 bg-light mb-3">
                                <div class="fw-semibold"><?php echo htmlspecialchars($attendance['titulo'] ?? 'Evento', ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="text-muted small">
                                    <?php echo htmlspecialchars($attendance['ubicacion'] ?? '-', ENT_QUOTES, 'UTF-8'); ?> ·
                                    <?php echo htmlspecialchars($attendance['fecha_inicio'] ?? '-', ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if (!empty($attendance['fecha_fin'])) : ?>
                                        - <?php echo htmlspecialchars($attendance['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php endif; ?>
                                </div>
                                <?php if ($attendanceStatus && $attendanceStatus !== 'pendiente') : ?>
                                    <div class="mt-2">
                                        <?php if ($attendanceStatus === 'confirmado') : ?>
                                            <span class="badge text-bg-success">Confirmada</span>
                                        <?php elseif ($attendanceStatus === 'rechazado') : ?>
                                            <span class="badge text-bg-danger">Rechazada</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted mb-4">
                            A continuación puedes confirmar tu participación en el evento. Esta respuesta quedará registrada en el sistema municipal.
                        </p>

                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

                            <div class="d-grid gap-2">
                                <button type="submit" name="action" value="confirm" class="btn btn-primary fw-semibold">
                                    Confirmar participación
                                </button>
                                <button type="submit" name="action" value="decline" class="btn btn-outline-secondary fw-semibold">
                                    No podré asistir
                                </button>
                            </div>
                        </form>

                        <p class="text-muted mt-4 mb-0 text-center" style="font-size: 12px;">
                            Este enlace es personal. Si recibiste este mensaje por error, puedes cerrar esta ventana.
                        </p>
                    </div>
                    <p class="text-center text-muted mt-4 mb-0">
                        © <script>document.write(new Date().getFullYear())</script> Go Muni - tecnologia escalable
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php include('partials/footer-scripts.php'); ?>
</body>

</html>
