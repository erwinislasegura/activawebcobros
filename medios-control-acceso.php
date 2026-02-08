<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$notice = null;
$lastScanResult = null;
$selectedEventId = isset($_GET['event_id']) ? (int) $_GET['event_id'] : 0;
$events = db()->query('SELECT id, titulo, fecha_inicio, fecha_fin FROM events WHERE habilitado = 1 ORDER BY fecha_inicio DESC')->fetchAll();
$insideMedia = [];
$outsideMedia = [];
$cooldownSeconds = 2;
$sameActionCooldownSeconds = 300;

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

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS media_accreditation_access_logs (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            event_id INT UNSIGNED NOT NULL,
            request_id INT UNSIGNED NOT NULL,
            accion ENUM("ingreso", "salida") NOT NULL,
            scanned_by INT UNSIGNED DEFAULT NULL,
            scanned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY media_accreditation_access_logs_event_idx (event_id),
            KEY media_accreditation_access_logs_request_idx (request_id),
            CONSTRAINT media_accreditation_access_logs_event_fk FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE,
            CONSTRAINT media_accreditation_access_logs_request_fk FOREIGN KEY (request_id) REFERENCES media_accreditation_requests (id) ON DELETE CASCADE,
            CONSTRAINT media_accreditation_access_logs_scanned_by_fk FOREIGN KEY (scanned_by) REFERENCES users (id) ON DELETE SET NULL
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $tokenInput = trim($_POST['qr_token'] ?? '');
    $selectedEventId = isset($_POST['event_id']) ? (int) $_POST['event_id'] : $selectedEventId;

    if ($selectedEventId <= 0) {
        $errors[] = 'Selecciona un evento para registrar accesos.';
    } elseif ($tokenInput === '') {
        $errors[] = 'Escanea o ingresa el QR del medio.';
    } else {
        $stmtRequest = db()->prepare('SELECT * FROM media_accreditation_requests WHERE qr_token = ? AND event_id = ? LIMIT 1');
        $stmtRequest->execute([$tokenInput, $selectedEventId]);
        $request = $stmtRequest->fetch();

        if (!$request) {
            $errors[] = 'El QR no corresponde a una solicitud válida para este evento.';
        } elseif (($request['estado'] ?? '') !== 'aprobado') {
            $errors[] = 'El medio no está autorizado. Estado actual: ' . ($request['estado'] ?? 'pendiente');
        } else {
            $inside = (int) ($request['inside_estado'] ?? 0) === 1;
            $action = $inside ? 'salida' : 'ingreso';
            $userId = isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;

            $stmtLastLog = db()->prepare(
                'SELECT accion, scanned_at FROM media_accreditation_access_logs WHERE request_id = ? ORDER BY scanned_at DESC LIMIT 1'
            );
            $stmtLastLog->execute([(int) $request['id']]);
            $lastLog = $stmtLastLog->fetch();

            $lastScanAt = $request['last_scan_at'] ?? null;
            $lastScanTime = $lastScanAt ? strtotime($lastScanAt) : null;
            $secondsSinceLastScan = $lastScanTime ? (time() - $lastScanTime) : null;
            $lastActionAt = $lastLog['scanned_at'] ?? null;
            $lastActionTime = $lastActionAt ? strtotime($lastActionAt) : null;
            $secondsSinceLastAction = $lastActionTime ? (time() - $lastActionTime) : null;

            if ($secondsSinceLastScan !== null && $secondsSinceLastScan < $cooldownSeconds) {
                $errors[] = 'Espera unos segundos antes de volver a escanear este QR.';
            } elseif ($lastLog && ($lastLog['accion'] ?? '') === $action && $secondsSinceLastAction !== null && $secondsSinceLastAction < $sameActionCooldownSeconds) {
                $errors[] = 'Para registrar otro ' . $action . ', espera al menos 5 minutos.';
            } else {
                $newInsideEstado = $action === 'ingreso' ? 1 : 0;
                $stmtLog = db()->prepare(
                    'INSERT INTO media_accreditation_access_logs (event_id, request_id, accion, scanned_by)
                     VALUES (?, ?, ?, ?)'
                );
                $stmtLog->execute([$selectedEventId, (int) $request['id'], $action, $userId]);

                $stmtUpdate = db()->prepare(
                    'UPDATE media_accreditation_requests SET inside_estado = ?, last_scan_at = NOW() WHERE id = ?'
                );
                $stmtUpdate->execute([$newInsideEstado, (int) $request['id']]);

                $notice = $inside
                    ? 'Salida registrada para ' . $request['medio'] . '.'
                    : 'Ingreso registrado para ' . $request['medio'] . '.';
                $lastScanResult = [
                    'accion' => $action,
                    'medio' => $request['medio'] ?? '',
                    'tipo_medio' => $request['tipo_medio'] ?? '',
                    'nombre' => trim(($request['nombre'] ?? '') . ' ' . ($request['apellidos'] ?? '')),
                    'rut' => $request['rut'] ?? '',
                    'correo' => $request['correo'] ?? '',
                ];
            }
        }
    }
}

if ($selectedEventId > 0) {
    $stmtInside = db()->prepare(
        'SELECT * FROM media_accreditation_requests
         WHERE event_id = ? AND estado = "aprobado" AND inside_estado = 1
         ORDER BY medio'
    );
    $stmtInside->execute([$selectedEventId]);
    $insideMedia = $stmtInside->fetchAll();

    $stmtOutside = db()->prepare(
        'SELECT * FROM media_accreditation_requests
         WHERE event_id = ? AND estado = "aprobado" AND inside_estado = 0
         ORDER BY medio'
    );
    $stmtOutside->execute([$selectedEventId]);
    $outsideMedia = $stmtOutside->fetchAll();
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Control de acceso medios'; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
    <style>
        .gm-access-table .table {
            white-space: nowrap;
        }
        .gm-access-table th,
        .gm-access-table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Medios de comunicación'; $title = 'Control de acceso'; include('partials/page-title.php'); ?>

                <?php if ($notice) : ?>
                    <div class="alert alert-success">
                        <?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php endif; ?>

                <div
                    class="modal fade"
                    id="scan-result-modal"
                    tabindex="-1"
                    aria-hidden="true"
                    data-has-result="<?php echo $lastScanResult ? '1' : '0'; ?>"
                    data-accion="<?php echo htmlspecialchars($lastScanResult['accion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    data-medio="<?php echo htmlspecialchars($lastScanResult['medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    data-tipo="<?php echo htmlspecialchars($lastScanResult['tipo_medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    data-nombre="<?php echo htmlspecialchars($lastScanResult['nombre'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    data-rut="<?php echo htmlspecialchars($lastScanResult['rut'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                    data-correo="<?php echo htmlspecialchars($lastScanResult['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header" id="scan-result-header">
                                <h5 class="modal-title" id="scan-result-title">Registro de acceso</h5>
                            </div>
                            <div class="modal-body">
                                <p class="mb-3" id="scan-result-status"></p>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>Medio:</strong> <span id="scan-result-medio"></span></li>
                                    <li><strong>Tipo:</strong> <span id="scan-result-tipo"></span></li>
                                    <li><strong>Nombre:</strong> <span id="scan-result-nombre"></span></li>
                                    <li><strong>RUT:</strong> <span id="scan-result-rut"></span></li>
                                    <li><strong>Correo:</strong> <span id="scan-result-correo"></span></li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" id="scan-modal-accept">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="modal fade"
                    id="scan-error-modal"
                    tabindex="-1"
                    aria-hidden="true"
                    data-has-error="<?php echo !empty($errors) ? '1' : '0'; ?>"
                    data-error-message="<?php echo htmlspecialchars($errors[0] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                >
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border border-danger">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">QR no válido</h5>
                            </div>
                            <div class="modal-body">
                                <p class="mb-0" id="scan-error-message"></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" id="scan-error-accept">Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-5">
                        <div class="card gm-section">
                            <div class="card-header">
                                <h5 class="card-title mb-1">Escaneo de QR</h5>
                                <p class="text-muted mb-0">Escanea el QR del medio para registrar la entrada o salida.</p>
                            </div>
                            <div class="card-body">
                                <form method="post" id="scan-form">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" id="qr-token" name="qr_token">
                                    <div class="mb-3">
                                        <label class="form-label" for="event-id">Evento</label>
                                        <select id="event-id" name="event_id" class="form-select" required>
                                            <option value="">Selecciona un evento</option>
                                            <?php foreach ($events as $event) : ?>
                                                <option value="<?php echo (int) $event['id']; ?>" <?php echo $selectedEventId === (int) $event['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($event['titulo'], ENT_QUOTES, 'UTF-8'); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div>
                                        <h6 class="mb-2">Escaneo desde celular</h6>
                                        <p class="text-muted small mb-3">Activa la cámara para leer el QR y completar el campo automáticamente.</p>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="start-scan">Iniciar escaneo</button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm" id="stop-scan" disabled>Detener</button>
                                        </div>
                                        <div class="ratio ratio-4x3 bg-light border rounded">
                                            <video id="qr-video" autoplay muted playsinline style="object-fit: cover;"></video>
                                        </div>
                                        <p id="scan-status" class="text-muted small mt-2 mb-0">Cámara detenida.</p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <div class="d-flex justify-content-end mb-2">
                            <a class="btn btn-sm btn-outline-primary" href="medios-control-acceso.php?event_id=<?php echo (int) $selectedEventId; ?>">
                                <i class="ti ti-refresh"></i> Actualizar
                            </a>
                        </div>
                        <div class="card gm-section mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">Medios dentro del evento</h5>
                                    <p class="text-muted mb-0">Listado actualizado de medios con ingreso activo.</p>
                                </div>
                                <?php if ($selectedEventId) : ?>
                                    <span class="badge text-bg-primary">Total: <?php echo count($insideMedia); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!$selectedEventId) : ?>
                                    <div class="text-muted">Selecciona un evento para ver los accesos.</div>
                                <?php elseif (empty($insideMedia)) : ?>
                                    <div class="text-muted">No hay medios dentro del evento en este momento.</div>
                                <?php else : ?>
                                    <div class="table-responsive gm-access-table">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Medio</th>
                                                    <th>Tipo</th>
                                                    <th>Tipo otro</th>
                                                    <th>Ciudad</th>
                                                    <th>Nombre</th>
                                                    <th>RUT</th>
                                                    <th>Correo</th>
                                                    <th>Celular</th>
                                                    <th>Cargo</th>
                                                    <th>Último escaneo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($insideMedia as $media) : ?>
                                                    <tr>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['tipo_medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['tipo_medio_otro'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['ciudad'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars(trim(($media['nombre'] ?? '') . ' ' . ($media['apellidos'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['rut'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['celular'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['cargo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['last_scan_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card gm-section">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title mb-1">Medios fuera del evento</h5>
                                    <p class="text-muted mb-0">Medios acreditados que aún no han ingresado o ya salieron.</p>
                                </div>
                                <?php if ($selectedEventId) : ?>
                                    <span class="badge text-bg-secondary">Total: <?php echo count($outsideMedia); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <?php if (!$selectedEventId) : ?>
                                    <div class="text-muted">Selecciona un evento para ver los accesos.</div>
                                <?php elseif (empty($outsideMedia)) : ?>
                                    <div class="text-muted">No hay medios fuera del evento en este momento.</div>
                                <?php else : ?>
                                    <div class="table-responsive gm-access-table">
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Medio</th>
                                                    <th>Tipo</th>
                                                    <th>Tipo otro</th>
                                                    <th>Ciudad</th>
                                                    <th>Nombre</th>
                                                    <th>RUT</th>
                                                    <th>Correo</th>
                                                    <th>Celular</th>
                                                    <th>Cargo</th>
                                                    <th>Último escaneo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($outsideMedia as $media) : ?>
                                                    <tr>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['tipo_medio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['tipo_medio_otro'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['ciudad'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars(trim(($media['nombre'] ?? '') . ' ' . ($media['apellidos'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['rut'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['correo'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['celular'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['cargo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td class="text-nowrap"><?php echo htmlspecialchars($media['last_scan_at'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
    <script>
        const videoElement = document.getElementById('qr-video');
        const startButton = document.getElementById('start-scan');
        const stopButton = document.getElementById('stop-scan');
        const statusLabel = document.getElementById('scan-status');
        const qrInput = document.getElementById('qr-token');
        const eventSelect = document.getElementById('event-id');
        const scanResultModal = document.getElementById('scan-result-modal');
        const scanModalAccept = document.getElementById('scan-modal-accept');
        const scanErrorModal = document.getElementById('scan-error-modal');
        const scanErrorAccept = document.getElementById('scan-error-accept');
        let currentStream = null;
        let scanning = false;
        let detector = null;
        let submitted = false;
        let lastDetectedAt = 0;
        let lastDetectedValue = '';
        const scanThrottleMs = 800;
        let resultModalInstance = null;
        let errorModalInstance = null;

        function updateStatus(message) {
            statusLabel.textContent = message;
        }

        function playErrorTone() {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();
                oscillator.type = 'sine';
                oscillator.frequency.value = 440;
                gainNode.gain.value = 0.1;
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                oscillator.start();
                setTimeout(() => {
                    oscillator.stop();
                    audioContext.close();
                }, 200);
            } catch (error) {
                // Ignore audio errors.
            }
        }

        async function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                updateStatus('El navegador no permite acceder a la cámara.');
                return false;
            }
            if (currentStream) {
                currentStream.getTracks().forEach((track) => track.stop());
            }
            const constraintAttempts = [
                { video: { facingMode: { ideal: 'environment' } }, audio: false },
                { video: { facingMode: 'environment' }, audio: false },
                { video: true, audio: false },
            ];
            let lastError = null;
            for (let i = 0; i < constraintAttempts.length; i += 1) {
                try {
                    currentStream = await navigator.mediaDevices.getUserMedia(constraintAttempts[i]);
                    break;
                } catch (error) {
                    lastError = error;
                }
            }
            if (!currentStream) {
                if (lastError) {
                    updateStatus('No se pudo iniciar la cámara.');
                }
                return false;
            }
            videoElement.srcObject = currentStream;
            await videoElement.play();
            return true;
        }

        async function stopCamera() {
            if (currentStream) {
                currentStream.getTracks().forEach((track) => track.stop());
                currentStream = null;
            }
            scanning = false;
            stopButton.disabled = true;
            startButton.disabled = false;
            updateStatus('Cámara detenida.');
        }

        async function ensureCameraReady() {
            if (currentStream) {
                return true;
            }
            return startCamera();
        }

        function setModalField(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value || '-';
            }
        }

        function showScanResultModal() {
            if (!scanResultModal || scanResultModal.dataset.hasResult !== '1') {
                return false;
            }
            const accion = scanResultModal.dataset.accion || '';
            const statusText = accion === 'ingreso'
                ? 'Se registró el ingreso del medio.'
                : 'Se registró la salida del medio.';
            const displayAction = accion === 'ingreso' ? 'Ingreso' : 'Salida';
            const statusMessage = `${displayAction} registrada correctamente.`;
            const statusElement = document.getElementById('scan-result-status');
            if (statusElement) {
                statusElement.textContent = `${statusMessage} ${statusText}`;
            }
            const resultHeader = document.getElementById('scan-result-header');
            const resultTitle = document.getElementById('scan-result-title');
            if (resultHeader) {
                resultHeader.classList.remove('bg-primary', 'bg-success', 'text-white');
                if (accion === 'ingreso') {
                    resultHeader.classList.add('bg-primary', 'text-white');
                } else {
                    resultHeader.classList.add('bg-success', 'text-white');
                }
            }
            if (resultTitle) {
                resultTitle.textContent = `${displayAction} registrada`;
            }
            setModalField('scan-result-medio', scanResultModal.dataset.medio);
            setModalField('scan-result-tipo', scanResultModal.dataset.tipo);
            setModalField('scan-result-nombre', scanResultModal.dataset.nombre);
            setModalField('scan-result-rut', scanResultModal.dataset.rut);
            setModalField('scan-result-correo', scanResultModal.dataset.correo);
            if (window.bootstrap && window.bootstrap.Modal) {
                resultModalInstance = resultModalInstance || new window.bootstrap.Modal(scanResultModal, {
                    backdrop: 'static',
                    keyboard: false,
                });
                resultModalInstance.show();
            } else {
                scanResultModal.classList.add('show');
                scanResultModal.style.display = 'block';
                scanResultModal.removeAttribute('aria-hidden');
            }
            return true;
        }

        function hideScanResultModal() {
            if (!scanResultModal) {
                return;
            }
            if (resultModalInstance) {
                resultModalInstance.hide();
            } else {
                scanResultModal.classList.remove('show');
                scanResultModal.style.display = 'none';
                scanResultModal.setAttribute('aria-hidden', 'true');
            }
            scanResultModal.dataset.hasResult = '0';
        }

        function showScanErrorModal() {
            if (!scanErrorModal || scanErrorModal.dataset.hasError !== '1') {
                return false;
            }
            const errorMessage = scanErrorModal.dataset.errorMessage || 'El QR no es válido para este evento.';
            const errorElement = document.getElementById('scan-error-message');
            if (errorElement) {
                errorElement.textContent = errorMessage;
            }
            if (window.bootstrap && window.bootstrap.Modal) {
                errorModalInstance = errorModalInstance || new window.bootstrap.Modal(scanErrorModal, {
                    backdrop: 'static',
                    keyboard: false,
                });
                errorModalInstance.show();
            } else {
                scanErrorModal.classList.add('show');
                scanErrorModal.style.display = 'block';
                scanErrorModal.removeAttribute('aria-hidden');
            }
            return true;
        }

        function hideScanErrorModal() {
            if (!scanErrorModal) {
                return;
            }
            if (errorModalInstance) {
                errorModalInstance.hide();
            } else {
                scanErrorModal.classList.remove('show');
                scanErrorModal.style.display = 'none';
                scanErrorModal.setAttribute('aria-hidden', 'true');
            }
            scanErrorModal.dataset.hasError = '0';
        }

        const canvasElement = document.createElement('canvas');
        const canvasContext = canvasElement.getContext('2d');
        let useBarcodeDetector = false;
        let useJsQr = false;

        function isScannerSupported() {
            return useBarcodeDetector || useJsQr;
        }

        function updateDetectorSupport() {
            useBarcodeDetector = 'BarcodeDetector' in window;
            useJsQr = !!window.jsQR;
        }

        function decodeWithJsQr() {
            if (!canvasContext || videoElement.readyState < 2) {
                return null;
            }
            const width = videoElement.videoWidth;
            const height = videoElement.videoHeight;
            if (!width || !height) {
                return null;
            }
            canvasElement.width = width;
            canvasElement.height = height;
            canvasContext.drawImage(videoElement, 0, 0, width, height);
            const imageData = canvasContext.getImageData(0, 0, width, height);
            const code = window.jsQR?.(imageData.data, width, height, { inversionAttempts: 'dontInvert' });
            return code?.data || null;
        }

        async function scanLoop() {
            if (!scanning || !detector) {
                return;
            }
            try {
                let code = null;
                if (useBarcodeDetector) {
                    const barcodes = await detector.detect(videoElement);
                    if (barcodes.length > 0) {
                        code = barcodes[0].rawValue;
                    }
                } else if (useJsQr) {
                    code = decodeWithJsQr();
                }
                if (code && !submitted) {
                    const now = Date.now();
                    if (code === lastDetectedValue && now - lastDetectedAt < scanThrottleMs) {
                        requestAnimationFrame(scanLoop);
                        return;
                    }
                    lastDetectedValue = code;
                    lastDetectedAt = now;
                    qrInput.value = code;
                    updateStatus('QR leído. Enviando registro...');
                    if (!eventSelect.value) {
                        updateStatus('Selecciona un evento antes de registrar.');
                        playErrorTone();
                        requestAnimationFrame(scanLoop);
                        return;
                    }
                    submitted = true;
                    qrInput.form?.submit();
                    return;
                }
            } catch (error) {
                updateStatus('No se pudo leer el QR. Intenta nuevamente.');
            }
            requestAnimationFrame(scanLoop);
        }

        async function beginScan() {
            updateDetectorSupport();
            if (!isScannerSupported()) {
                updateStatus('Tu navegador no soporta lectura automática de QR.');
                return;
            }
            detector = detector || (useBarcodeDetector ? new BarcodeDetector({ formats: ['qr_code'] }) : true);
            startButton.disabled = true;
            stopButton.disabled = false;
            scanning = true;
            submitted = false;
            try {
                const started = await startCamera();
                if (!started) {
                    startButton.disabled = false;
                    stopButton.disabled = true;
                    return;
                }
                updateStatus('Cámara activa. Apunta al QR.');
                requestAnimationFrame(scanLoop);
            } catch (error) {
                updateStatus('No se pudo iniciar la cámara.');
                startButton.disabled = false;
                stopButton.disabled = true;
            }
        }

        startButton?.addEventListener('click', async () => {
            await beginScan();
        });

        stopButton?.addEventListener('click', () => {
            stopCamera();
        });

        eventSelect?.addEventListener('change', () => {
            const selectedValue = eventSelect.value;
            if (!selectedValue) {
                return;
            }
            window.location.href = `medios-control-acceso.php?event_id=${encodeURIComponent(selectedValue)}`;
        });

        document.addEventListener('DOMContentLoaded', () => {
            updateDetectorSupport();
            if (!isScannerSupported()) {
                updateStatus('Tu navegador no soporta lectura automática de QR.');
                startButton.disabled = true;
                stopButton.disabled = true;
                return;
            }
            detector = detector || (useBarcodeDetector ? new BarcodeDetector({ formats: ['qr_code'] }) : true);
            scanning = false;
            submitted = false;
            const hasErrorModal = showScanErrorModal();
            if (hasErrorModal) {
                updateStatus('QR no válido. Confirma para continuar.');
                playErrorTone();
                startButton.disabled = true;
                stopButton.disabled = true;
                ensureCameraReady();
                return;
            }
            const hasModal = showScanResultModal();
            if (hasModal) {
                updateStatus('Registro completado. Confirma para continuar.');
                startButton.disabled = true;
                stopButton.disabled = true;
                ensureCameraReady();
                return;
            }
            startButton.disabled = false;
            stopButton.disabled = true;
            updateStatus('Cámara lista. Iniciando escaneo automáticamente.');
            beginScan();
        });

        scanModalAccept?.addEventListener('click', async () => {
            hideScanResultModal();
            updateStatus('Listo para escanear otro medio.');
            await beginScan();
        });

        scanErrorAccept?.addEventListener('click', async () => {
            hideScanErrorModal();
            updateStatus('Listo para escanear otro medio.');
            await beginScan();
        });
    </script>

    <?php include('partials/footer-scripts.php'); ?>
    <?php include('partials/footer.php'); ?>
</body>
</html>
