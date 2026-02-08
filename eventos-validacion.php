<?php
require __DIR__ . '/app/bootstrap.php';

$token = trim($_GET['token'] ?? '');
$errors = [];
$success = false;
$request = null;
$event = null;
$authorities = [];
$authoritiesByGroup = [];
$confirmedAuthorityIds = [];
$allowedAuthorityIds = [];

if ($token === '') {
    $errors[] = 'El enlace de validación es inválido.';
} else {
    $stmt = db()->prepare('SELECT * FROM event_authority_requests WHERE token = ?');
    $stmt->execute([$token]);
    $request = $stmt->fetch();

    if (!$request) {
        $stmtEvent = db()->prepare('SELECT * FROM events WHERE validation_token = ?');
        $stmtEvent->execute([$token]);
        $event = $stmtEvent->fetch();
        if ($event) {
            $municipalidad = get_municipalidad();
            $placeholderEmail = $municipalidad['correo'] ?? 'validacion@municipalidad.local';
            $stmtInsert = db()->prepare('INSERT INTO event_authority_requests (event_id, destinatario_nombre, destinatario_correo, token, correo_enviado) VALUES (?, ?, ?, ?, ?)');
            $stmtInsert->execute([
                (int) $event['id'],
                'Enlace público',
                $placeholderEmail,
                $token,
                0,
            ]);
            $stmtRequest = db()->prepare('SELECT * FROM event_authority_requests WHERE token = ?');
            $stmtRequest->execute([$token]);
            $request = $stmtRequest->fetch();
        } else {
            $errors[] = 'El enlace de validación no fue encontrado o ya expiró.';
        }
    }
}

if ($request && !$event) {
    $stmt = db()->prepare('SELECT * FROM events WHERE id = ?');
    $stmt->execute([(int) $request['event_id']]);
    $event = $stmt->fetch();

    $stmt = db()->prepare(
        'SELECT a.id,
                a.nombre,
                a.tipo,
                g.id AS grupo_id,
                g.nombre AS grupo_nombre
         FROM authorities a
         INNER JOIN event_authorities ea ON ea.authority_id = a.id
         LEFT JOIN authority_groups g ON g.id = a.group_id
         WHERE ea.event_id = ?
         ORDER BY COALESCE(g.nombre, ""), a.nombre'
    );
    $stmt->execute([(int) $request['event_id']]);
    $authorities = $stmt->fetchAll();

    foreach ($authorities as $authority) {
        $allowedAuthorityIds[] = (int) $authority['id'];
        $groupId = $authority['grupo_id'] ? (int) $authority['grupo_id'] : 0;
        $groupName = $authority['grupo_nombre'] ?: 'Sin grupo';
        if (!isset($authoritiesByGroup[$groupId])) {
            $authoritiesByGroup[$groupId] = [
                'name' => $groupName,
                'items' => [],
            ];
        }
        $authoritiesByGroup[$groupId]['items'][] = $authority;
    }

    $stmt = db()->prepare('SELECT authority_id FROM event_authority_confirmations WHERE request_id = ?');
    $stmt->execute([(int) $request['id']]);
    $confirmedAuthorityIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

    if ($request['estado'] !== 'respondido') {
        $confirmedAuthorityIds = [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null) && $request) {
    $postToken = trim($_POST['token'] ?? '');
    if ($postToken !== $token) {
        $errors[] = 'El token de validación no coincide.';
    } elseif (!$event) {
        $errors[] = 'No se encontró el evento asociado a la validación.';
    } else {
        $selectedAuthorities = array_map('intval', $_POST['authorities'] ?? []);
        $allowedAuthorityIds = $allowedAuthorityIds ?: [];
        $invalidSelections = array_diff($selectedAuthorities, $allowedAuthorityIds);
        if (!empty($invalidSelections)) {
            $errors[] = 'La selección incluye autoridades no válidas para este evento.';
        }
    }

    if (empty($errors)) {
        $stmt = db()->prepare('DELETE FROM event_authority_confirmations WHERE request_id = ?');
        $stmt->execute([(int) $request['id']]);

        if (!empty($selectedAuthorities)) {
            $stmtInsert = db()->prepare('INSERT INTO event_authority_confirmations (request_id, authority_id) VALUES (?, ?)');
            foreach ($selectedAuthorities as $authorityId) {
                $stmtInsert->execute([(int) $request['id'], $authorityId]);
            }
        }

        $stmtUpdate = db()->prepare('UPDATE event_authority_requests SET estado = ?, responded_at = NOW() WHERE id = ?');
        $stmtUpdate->execute(['respondido', (int) $request['id']]);

        $confirmedAuthorityIds = $selectedAuthorities;
        $success = true;
        $request['estado'] = 'respondido';
        $request['responded_at'] = date('Y-m-d H:i:s');
    }
}

$municipalidad = get_municipalidad();
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Validación de autoridades"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="text-center mb-4">
                    <img src="<?php echo htmlspecialchars($municipalidad['logo_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="Logo municipal" height="60">
                    <h3 class="mt-3 mb-1">Validación de autoridades</h3>
                    <p class="text-muted mb-0">Confirma qué autoridades asistirán al evento.</p>
                </div>

                <?php if (!empty($errors)) : ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error) : ?>
                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success) : ?>
                    <div class="alert alert-success">
                        Gracias, la validación quedó registrada correctamente.
                    </div>
                <?php endif; ?>

                <?php if ($event && $request) : ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                                <div>
                                    <h4 class="fw-semibold mb-2"><?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <div class="text-muted">
                                        <?php echo htmlspecialchars($event['ubicacion'], ENT_QUOTES, 'UTF-8'); ?>
                                        · <?php echo htmlspecialchars($event['tipo'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <div class="text-muted">
                                        <?php echo htmlspecialchars($event['fecha_inicio'], ENT_QUOTES, 'UTF-8'); ?>
                                        - <?php echo htmlspecialchars($event['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge text-bg-<?php echo $request['estado'] === 'respondido' ? 'success' : 'warning'; ?>">
                                        <?php echo htmlspecialchars(ucfirst($request['estado']), ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <?php if (!empty($request['responded_at'])) : ?>
                                        <div class="text-muted small mt-1">Respondido: <?php echo htmlspecialchars($request['responded_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="mt-3 mb-4"><?php echo nl2br(htmlspecialchars($event['descripcion'], ENT_QUOTES, 'UTF-8')); ?></p>

                            <h6 class="mb-3">Selecciona las autoridades que asistirán</h6>
                            <?php if (empty($authoritiesByGroup)) : ?>
                                <div class="text-muted">No hay autoridades preseleccionadas para este evento.</div>
                            <?php else : ?>
                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <?php foreach ($authoritiesByGroup as $group) : ?>
                                            <?php if (empty($group['items'])) : ?>
                                                <?php continue; ?>
                                            <?php endif; ?>
                                            <div class="col-12 mt-3">
                                                <h6 class="text-uppercase text-muted small mb-2"><?php echo htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8'); ?></h6>
                                            </div>
                                            <?php foreach ($group['items'] as $authority) : ?>
                                                <?php $checked = in_array((int) $authority['id'], $confirmedAuthorityIds, true); ?>
                                                <div class="col-md-6">
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox" id="auth-<?php echo (int) $authority['id']; ?>" name="authorities[]" value="<?php echo (int) $authority['id']; ?>" <?php echo $checked ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="auth-<?php echo (int) $authority['id']; ?>">
                                                            <?php echo htmlspecialchars($authority['nombre'], ENT_QUOTES, 'UTF-8'); ?>
                                                            <span class="text-muted">· <?php echo htmlspecialchars($authority['tipo'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary">Enviar validación</button>
                                        <span class="text-muted small">Puedes actualizar esta selección si cambia la disponibilidad.</span>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include('partials/footer-scripts.php'); ?>
</body>

</html>
