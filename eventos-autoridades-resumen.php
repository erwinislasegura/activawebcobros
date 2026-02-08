<?php
require __DIR__ . '/app/bootstrap.php';

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

$events = db()->query('SELECT id, titulo, fecha_inicio FROM events WHERE habilitado = 1 ORDER BY fecha_inicio DESC')->fetchAll();
$selectedEventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;
$selectedEvent = null;
$authorities = [];
$confirmedIds = [];
$attendanceMap = [];

if ($selectedEventId > 0) {
    foreach ($events as $event) {
        if ((int) $event['id'] === $selectedEventId) {
            $selectedEvent = $event;
            break;
        }
    }

    $stmtAuthorities = db()->prepare(
        'SELECT a.id, a.nombre, a.tipo
         FROM authorities a
         INNER JOIN event_authorities ea ON ea.authority_id = a.id
         WHERE ea.event_id = ?
         ORDER BY a.nombre'
    );
    $stmtAuthorities->execute([$selectedEventId]);
    $authorities = $stmtAuthorities->fetchAll();

    $stmtConfirmed = db()->prepare(
        'SELECT DISTINCT c.authority_id
         FROM event_authority_confirmations c
         INNER JOIN event_authority_requests r ON r.id = c.request_id
         WHERE r.event_id = ?'
    );
    $stmtConfirmed->execute([$selectedEventId]);
    $confirmedIds = array_map('intval', $stmtConfirmed->fetchAll(PDO::FETCH_COLUMN));

    $stmtAttendance = db()->prepare(
        'SELECT authority_id, status
         FROM event_authority_attendance
         WHERE event_id = ?'
    );
    $stmtAttendance->execute([$selectedEventId]);
    foreach ($stmtAttendance->fetchAll() as $attendance) {
        $attendanceMap[(int) $attendance['authority_id']] = $attendance['status'] ?? 'pendiente';
    }
}

$totalAssigned = count($authorities);
$totalConfirmed = 0;
foreach ($authorities as $authority) {
    $attendanceStatus = $attendanceMap[(int) $authority['id']] ?? 'pendiente';
    if ($attendanceStatus === 'confirmado') {
        $totalConfirmed++;
    }
}
$totalPending = max(0, $totalAssigned - $totalConfirmed);
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Eventos / Autoridades'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">

                <?php $subtitle = 'Eventos Municipales'; $title = 'Eventos / Autoridades'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Autoridades asociadas por evento</h5>
                                    <p class="text-muted mb-0">Revisa confirmaciones de asistencia y aprobación externa por autoridad.</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="get" class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label" for="evento-resumen-select">Evento</label>
                                        <select id="evento-resumen-select" name="event_id" class="form-select" onchange="this.form.submit()">
                                            <option value="">Selecciona un evento</option>
                                            <?php foreach ($events as $event) : ?>
                                                <option value="<?php echo (int) $event['id']; ?>" <?php echo $selectedEventId === (int) $event['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if ($selectedEvent) : ?>
                                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                                <span class="badge text-bg-primary">Total: <?php echo $totalAssigned; ?></span>
                                                <span class="badge text-bg-success">Asistencia confirmada: <?php echo $totalConfirmed; ?></span>
                                                <span class="badge text-bg-warning">Asistencia pendiente: <?php echo $totalPending; ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </form>

                                <div class="table-responsive mt-4">
                                    <table class="table table-striped table-centered mb-0">
                                        <thead>
                                            <tr>
                                                <th>Autoridad</th>
                                                <th>Tipo</th>
                                                <th>Confirmación de asistencia</th>
                                                <th>Aprobado por autoridad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($selectedEventId === 0) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-muted">Selecciona un evento para ver sus autoridades.</td>
                                                </tr>
                                            <?php elseif (empty($authorities)) : ?>
                                                <tr>
                                                    <td colspan="4" class="text-muted">No hay autoridades asociadas a este evento.</td>
                                                </tr>
                                            <?php else : ?>
                                                <?php foreach ($authorities as $authority) : ?>
                                                    <?php
                                                    $authorityId = (int) $authority['id'];
                                                    $attendanceStatus = $attendanceMap[$authorityId] ?? 'pendiente';
                                                    $isExternallyApproved = in_array($authorityId, $confirmedIds, true);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($authority['nombre'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($authority['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ($attendanceStatus === 'confirmado') : ?>
                                                                <span class="badge text-bg-success">Confirmada</span>
                                                            <?php elseif ($attendanceStatus === 'rechazado') : ?>
                                                                <span class="badge text-bg-danger">Rechazada</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-warning">Pendiente</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($isExternallyApproved) : ?>
                                                                <span class="badge text-bg-success">Aprobada</span>
                                                            <?php else : ?>
                                                                <span class="badge text-bg-secondary">Pendiente</span>
                                                            <?php endif; ?>
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
        </div>

    </div>

    <?php include('partials/footer-scripts.php'); ?>
</body>

</html>
