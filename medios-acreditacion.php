<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$success = false;
$token = trim($_GET['token'] ?? '');
$event = null;
$municipalidad = get_municipalidad();
$requestId = null;

$formData = [
    'medio' => '',
    'tipo_medio' => '',
    'tipo_medio_otro' => '',
    'ciudad' => '',
    'nombre' => '',
    'apellidos' => '',
    'rut' => '',
    'correo' => '',
    'celular' => '',
    'cargo' => '',
];

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS event_media_accreditation_links (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id INT UNSIGNED NOT NULL,
            token VARCHAR(64) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY event_media_accreditation_links_event_unique (event_id),
            UNIQUE KEY event_media_accreditation_links_token_unique (token),
            CONSTRAINT event_media_accreditation_links_event_fk FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS media_accreditation_requests (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id INT UNSIGNED NOT NULL,
            medio VARCHAR(200) NOT NULL,
            tipo_medio VARCHAR(80) NOT NULL,
            tipo_medio_otro VARCHAR(120) DEFAULT NULL,
            ciudad VARCHAR(120) DEFAULT NULL,
            nombre VARCHAR(120) NOT NULL,
            apellidos VARCHAR(160) NOT NULL,
            rut VARCHAR(30) NOT NULL,
            correo VARCHAR(180) NOT NULL,
            celular VARCHAR(40) DEFAULT NULL,
            cargo VARCHAR(120) DEFAULT NULL,
            estado ENUM("pendiente", "aprobado", "rechazado") NOT NULL DEFAULT "pendiente",
            qr_token VARCHAR(64) DEFAULT NULL,
            correo_enviado TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            aprobado_at TIMESTAMP NULL DEFAULT NULL,
            rechazado_at TIMESTAMP NULL DEFAULT NULL,
            last_scan_at TIMESTAMP NULL DEFAULT NULL,
            inside_estado TINYINT(1) NOT NULL DEFAULT 0,
            sent_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY media_accreditation_requests_qr_unique (qr_token),
            KEY media_accreditation_requests_event_idx (event_id),
            CONSTRAINT media_accreditation_requests_event_fk FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

$migrationStatements = [
    'ALTER TABLE media_accreditation_requests ADD COLUMN estado ENUM("pendiente", "aprobado", "rechazado") NOT NULL DEFAULT "pendiente"',
    'ALTER TABLE media_accreditation_requests ADD COLUMN qr_token VARCHAR(64) DEFAULT NULL',
    'ALTER TABLE media_accreditation_requests ADD COLUMN aprobado_at TIMESTAMP NULL DEFAULT NULL',
    'ALTER TABLE media_accreditation_requests ADD COLUMN rechazado_at TIMESTAMP NULL DEFAULT NULL',
    'ALTER TABLE media_accreditation_requests ADD COLUMN last_scan_at TIMESTAMP NULL DEFAULT NULL',
    'ALTER TABLE media_accreditation_requests ADD COLUMN inside_estado TINYINT(1) NOT NULL DEFAULT 0',
    'ALTER TABLE media_accreditation_requests ADD UNIQUE KEY media_accreditation_requests_qr_unique (qr_token)',
];

foreach ($migrationStatements as $statement) {
    try {
        db()->exec($statement);
    } catch (Exception $e) {
    } catch (Error $e) {
    }
}

if ($token === '') {
    $errors[] = 'El enlace de acreditación es inválido o incompleto.';
} else {
    $stmt = db()->prepare(
        'SELECT e.*
         FROM event_media_accreditation_links l
         INNER JOIN events e ON e.id = l.event_id
         WHERE l.token = ?
         LIMIT 1'
    );
    $stmt->execute([$token]);
    $event = $stmt->fetch();

    if (!$event) {
        $errors[] = 'El enlace de acreditación no fue encontrado o ya no está disponible.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null) && $event) {
    $tokenPost = trim($_POST['token'] ?? '');
    if ($tokenPost !== $token) {
        $errors[] = 'El token de acreditación no coincide.';
    }

    foreach ($formData as $key => $value) {
        $formData[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if ($formData['medio'] === '') {
        $errors[] = 'El nombre del medio es obligatorio.';
    }
    if ($formData['tipo_medio'] === '') {
        $errors[] = 'Debes seleccionar el tipo de medio.';
    }
    if ($formData['tipo_medio'] === 'Otro' && $formData['tipo_medio_otro'] === '') {
        $errors[] = 'Indica el tipo de medio cuando seleccionas "Otro".';
    }
    if ($formData['nombre'] === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    if ($formData['apellidos'] === '') {
        $errors[] = 'Los apellidos son obligatorios.';
    }
    if ($formData['rut'] === '') {
        $errors[] = 'El RUT es obligatorio.';
    }
    if ($formData['correo'] === '' || !filter_var($formData['correo'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Ingresa un correo válido.';
    }

    if (empty($errors)) {
        $stmtInsert = db()->prepare(
            'INSERT INTO media_accreditation_requests
                (event_id, medio, tipo_medio, tipo_medio_otro, ciudad, nombre, apellidos, rut, correo, celular, cargo)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmtInsert->execute([
            (int) $event['id'],
            $formData['medio'],
            $formData['tipo_medio'],
            $formData['tipo_medio_otro'] ?: null,
            $formData['ciudad'] ?: null,
            $formData['nombre'],
            $formData['apellidos'],
            $formData['rut'],
            $formData['correo'],
            $formData['celular'] ?: null,
            $formData['cargo'] ?: null,
        ]);

        $requestId = (int) db()->lastInsertId();

        $correoConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch();
        $fromEmail = $correoConfig['from_correo'] ?? $correoConfig['correo_imap'] ?? null;
        $fromName = $correoConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? 'Municipalidad');

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        if ($fromEmail) {
            $headers .= 'From: ' . ($fromName ? $fromName . ' <' . $fromEmail . '>' : $fromEmail) . "\r\n";
        }

        $subject = 'Solicitud de acreditación recibida - ' . ($event['titulo'] ?? 'Evento');
        $eventTitle = htmlspecialchars($event['titulo'] ?? 'Evento', ENT_QUOTES, 'UTF-8');
        $eventDates = htmlspecialchars(($event['fecha_inicio'] ?? '') . ' al ' . ($event['fecha_fin'] ?? ''), ENT_QUOTES, 'UTF-8');
        $eventLocation = htmlspecialchars($event['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8');
        $recipientName = htmlspecialchars($formData['nombre'] . ' ' . $formData['apellidos'], ENT_QUOTES, 'UTF-8');

        $bodyHtml = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Solicitud recibida</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6fb;font-family:Arial,sans-serif;color:#1f2b3a;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6fb;padding:24px 0;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e6ebf2;">
          <tr>
            <td style="padding:24px;">
              <h2 style="margin:0 0 12px 0;">¡Solicitud recibida!</h2>
              <p style="margin:0 0 10px 0;">Hola <strong>{$recipientName}</strong>,</p>
              <p style="margin:0 0 10px 0;">Confirmamos la recepción de tu solicitud de acreditación para <strong>{$eventTitle}</strong>.</p>
              <p style="margin:0 0 10px 0;">Fecha del evento: {$eventDates}</p>
              <p style="margin:0 0 10px 0;">Lugar: {$eventLocation}</p>
              <p style="margin:0;">Pronto te notificaremos vía correo electrónico sobre el estado de la solicitud.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        $mailSent = mail($formData['correo'], $subject, $bodyHtml, $headers);
        if ($mailSent && $requestId) {
            $stmtUpdate = db()->prepare('UPDATE media_accreditation_requests SET correo_enviado = 1, sent_at = NOW() WHERE id = ?');
            $stmtUpdate->execute([$requestId]);
        }

        $success = true;
        foreach ($formData as $key => $value) {
            $formData[$key] = '';
        }
    }
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Acreditación de medios'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="text-center mb-4">
                    <img src="<?php echo htmlspecialchars($municipalidad['logo_path'] ?? 'assets/images/logo.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Logo municipal" height="60">
                    <h2 class="mt-3 mb-2">Solicitud Acreditación <?php echo htmlspecialchars($event['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p class="text-muted mb-0">
                        Se ha dado inicio al proceso de acreditación para <?php echo htmlspecialchars($event['titulo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>,
                        a realizarse los días <?php echo htmlspecialchars($event['fecha_inicio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                        <?php if (!empty($event['fecha_fin'])) : ?>al <?php echo htmlspecialchars($event['fecha_fin'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>.
                    </p>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <p class="mb-2"><strong>Plazo máximo de postulación:</strong> 10 de febrero de 2026. No se aceptarán solicitudes fuera de este plazo.</p>
                        <p class="mb-2"><strong>Postulación Individual:</strong> Las solicitudes son una por persona. Cada persona debe completar su propia solicitud de acuerdo con la asignación que el medio determine.</p>
                        <p class="mb-2"><strong>Cupos limitados:</strong> El envío de la solicitud no garantiza la aprobación, debido a la alta demanda, respetando también la línea editorial de la Ilustre Municipalidad.</p>
                        <p class="mb-2 text-uppercase fw-semibold">Se notificará a través de correo electrónico quienes serán parte de la cobertura de este festival.</p>
                        <p class="mb-0 text-uppercase fw-semibold">En el correo de aprobación se enviará la credencial para imprimir. Es responsabilidad del medio portar la credencial visible (gafete colgante) durante el evento.</p>
                    </div>
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
                        Gracias, tu solicitud fue enviada correctamente. Te confirmaremos vía correo electrónico.
                    </div>
                <?php endif; ?>

                <?php if ($event) : ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-start">
                                <div>
                                    <h5 class="mb-1">Formulario de solicitud</h5>
                                    <p class="text-muted mb-0">* Indica que la pregunta es obligatoria.</p>
                                </div>
                                <a class="btn btn-outline-primary btn-sm" href="<?php echo htmlspecialchars(base_url() . '/medios-acreditacion.php?token=' . urlencode($token), ENT_QUOTES, 'UTF-8'); ?>">
                                    Copiar enlace
                                </a>
                            </div>
                            <form class="mt-4" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="medio">MEDIO *</label>
                                        <input type="text" class="form-control" id="medio" name="medio" required value="<?php echo htmlspecialchars($formData['medio'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="tipo-medio">TIPO DE MEDIO *</label>
                                        <select class="form-select" id="tipo-medio" name="tipo_medio" required>
                                            <option value="">Selecciona una opción</option>
                                            <?php
                                            $tipos = ['IMPRESO', 'ONLINE', 'RADIO', 'TELEVISIÓN', 'MEDIO DIGITAL', 'Otro'];
                                            foreach ($tipos as $tipo) :
                                                $selected = $formData['tipo_medio'] === $tipo ? 'selected' : '';
                                                ?>
                                                <option value="<?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8'); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3" id="tipo-medio-otro" style="display: none;">
                                        <label class="form-label" for="tipo-medio-otro-input">Otro:</label>
                                        <input type="text" class="form-control" id="tipo-medio-otro-input" name="tipo_medio_otro" value="<?php echo htmlspecialchars($formData['tipo_medio_otro'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="ciudad">CIUDAD</label>
                                        <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($formData['ciudad'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="nombre">NOMBRE *</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required value="<?php echo htmlspecialchars($formData['nombre'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="apellidos">APELLIDOS *</label>
                                        <input type="text" class="form-control" id="apellidos" name="apellidos" required value="<?php echo htmlspecialchars($formData['apellidos'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="rut">RUT *</label>
                                        <input type="text" class="form-control" id="rut" name="rut" required value="<?php echo htmlspecialchars($formData['rut'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="correo">MAIL *</label>
                                        <input type="email" class="form-control" id="correo" name="correo" required value="<?php echo htmlspecialchars($formData['correo'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <div class="form-text">Ingresar el correo de la persona que solicita.</div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="celular">CELULAR</label>
                                        <input type="text" class="form-control" id="celular" name="celular" value="<?php echo htmlspecialchars($formData['celular'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label" for="cargo">CARGO</label>
                                        <input type="text" class="form-control" id="cargo" name="cargo" value="<?php echo htmlspecialchars($formData['cargo'], ENT_QUOTES, 'UTF-8'); ?>">
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Enviar solicitud</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include('partials/vendor.php'); ?>
    <?php include('partials/footer.php'); ?>

    <script>
        const tipoSelect = document.getElementById('tipo-medio');
        const otroField = document.getElementById('tipo-medio-otro');
        const otroInput = document.getElementById('tipo-medio-otro-input');

        const toggleOtro = () => {
            if (!tipoSelect || !otroField || !otroInput) {
                return;
            }
            const isOtro = tipoSelect.value === 'Otro';
            otroField.style.display = isOtro ? 'block' : 'none';
            if (!isOtro) {
                otroInput.value = '';
            }
        };

        if (tipoSelect) {
            tipoSelect.addEventListener('change', toggleOtro);
            toggleOtro();
        }
    </script>
</body>
</html>
