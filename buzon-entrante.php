<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$warnings = [];
$successMessage = null;

$stmt = db()->query('SELECT * FROM notificacion_correos LIMIT 1');
$correoConfig = $stmt->fetch() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $correoImap = trim($_POST['correo_imap'] ?? '');
    $passwordImap = trim($_POST['password_imap'] ?? '');
    $hostImap = trim($_POST['host_imap'] ?? '');
    $puertoImap = (int) ($_POST['puerto_imap'] ?? 993);
    $seguridadImap = trim($_POST['seguridad_imap'] ?? 'ssl');

    if ($correoImap === '' || $passwordImap === '' || $hostImap === '') {
        $errors[] = 'Completa correo, contraseña y host IMAP para guardar el buzón.';
    }

    if (!in_array($seguridadImap, ['ssl', 'tls', 'none'], true)) {
        $seguridadImap = 'ssl';
    }

    if ($puertoImap <= 0 || $puertoImap > 65535) {
        $puertoImap = 993;
    }

    if (empty($errors)) {
        $stmt = db()->query('SELECT id FROM notificacion_correos LIMIT 1');
        $id = $stmt->fetchColumn();

        if ($id) {
            $stmtUpdate = db()->prepare('UPDATE notificacion_correos SET correo_imap = ?, password_imap = ?, host_imap = ?, puerto_imap = ?, seguridad_imap = ? WHERE id = ?');
            $stmtUpdate->execute([$correoImap, $passwordImap, $hostImap, $puertoImap, $seguridadImap, $id]);
        } else {
            $stmtInsert = db()->prepare('INSERT INTO notificacion_correos (correo_imap, password_imap, host_imap, puerto_imap, seguridad_imap) VALUES (?, ?, ?, ?, ?)');
            $stmtInsert->execute([$correoImap, $passwordImap, $hostImap, $puertoImap, $seguridadImap]);
        }

        $successMessage = 'Configuración del buzón entrante guardada.';
    }

    $correoConfig = [
        'correo_imap' => $correoImap,
        'password_imap' => $passwordImap,
        'host_imap' => $hostImap,
        'puerto_imap' => $puertoImap,
        'seguridad_imap' => $seguridadImap,
    ];
}

$emails = [];
$imapEnabled = function_exists('imap_open');

if (!$imapEnabled) {
    $warnings[] = 'La extensión IMAP de PHP no está habilitada en este servidor. Solo se muestra la configuración del buzón.';
} elseif (!empty($correoConfig['correo_imap']) && !empty($correoConfig['password_imap']) && !empty($correoConfig['host_imap'])) {
    $flags = '/imap';
    $seguridad = strtolower((string) ($correoConfig['seguridad_imap'] ?? 'ssl'));
    if ($seguridad === 'ssl') {
        $flags .= '/ssl';
    } elseif ($seguridad === 'tls') {
        $flags .= '/tls';
    } elseif ($seguridad === 'none') {
        $flags .= '/notls';
    }

    $mailboxString = '{' . $correoConfig['host_imap'] . ':' . (int) ($correoConfig['puerto_imap'] ?? 993) . $flags . '}INBOX';

    $imap = @imap_open($mailboxString, (string) $correoConfig['correo_imap'], (string) $correoConfig['password_imap']);

    if ($imap === false) {
        $warnings[] = 'No fue posible conectar al buzón entrante con los datos configurados.';
    } else {
        $uids = imap_search($imap, 'ALL', SE_UID) ?: [];
        rsort($uids);
        $uids = array_slice($uids, 0, 15);

        foreach ($uids as $uid) {
            $overviewList = imap_fetch_overview($imap, (string) $uid, FT_UID);
            $overview = $overviewList[0] ?? null;
            if (!$overview) {
                continue;
            }

            $subject = isset($overview->subject) ? imap_utf8((string) $overview->subject) : '(Sin asunto)';
            $from = isset($overview->from) ? imap_utf8((string) $overview->from) : '-';

            $emails[] = [
                'uid' => $uid,
                'subject' => $subject !== '' ? $subject : '(Sin asunto)',
                'from' => $from,
                'date' => $overview->date ?? '-',
                'seen' => !empty($overview->seen),
            ];
        }

        imap_close($imap);
    }
} else {
    $warnings[] = 'Configura el buzón para habilitar la revisión de correos entrantes.';
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = 'Buzón entrante'; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <div class="wrapper">
        <?php include('partials/menu.php'); ?>

        <div class="content-page">
            <div class="container-fluid">
                <?php $subtitle = 'Mantenedores'; $title = 'Buzón entrante'; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">Configuración de buzón tipo Gmail</h5>
                                    <p class="text-muted mb-0">Permite revisar correos entrantes del buzón configurado.</p>
                                </div>
                                <button type="submit" form="buzon-form" class="btn btn-primary">Guardar buzón</button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($successMessage !== null) : ?>
                                    <div class="alert alert-success"><?php echo htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endif; ?>

                                <form id="buzon-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="correo-imap">Correo</label>
                                            <input type="email" id="correo-imap" name="correo_imap" class="form-control" value="<?php echo htmlspecialchars($correoConfig['correo_imap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-lg-6 mb-3">
                                            <label class="form-label" for="password-imap">Contraseña de aplicación</label>
                                            <input type="password" id="password-imap" name="password_imap" class="form-control" value="<?php echo htmlspecialchars($correoConfig['password_imap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-lg-4 mb-3">
                                            <label class="form-label" for="host-imap">Host IMAP</label>
                                            <input type="text" id="host-imap" name="host_imap" class="form-control" value="<?php echo htmlspecialchars($correoConfig['host_imap'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="imap.gmail.com">
                                        </div>
                                        <div class="col-lg-2 mb-3">
                                            <label class="form-label" for="puerto-imap">Puerto</label>
                                            <input type="number" id="puerto-imap" name="puerto_imap" class="form-control" value="<?php echo htmlspecialchars((string) ($correoConfig['puerto_imap'] ?? 993), ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-lg-3 mb-3">
                                            <label class="form-label" for="seguridad-imap">Seguridad</label>
                                            <?php $seguridad = $correoConfig['seguridad_imap'] ?? 'ssl'; ?>
                                            <select id="seguridad-imap" name="seguridad_imap" class="form-select">
                                                <option value="ssl" <?php echo $seguridad === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                                <option value="tls" <?php echo $seguridad === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                                <option value="none" <?php echo $seguridad === 'none' ? 'selected' : ''; ?>>Sin cifrado</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 mb-3 d-flex align-items-end">
                                            <button type="button" class="btn btn-outline-secondary w-100" id="btn-gmail">Usar Gmail</button>
                                        </div>
                                    </div>
                                    <div class="form-text">Tip Gmail: habilita verificación en dos pasos y usa una contraseña de aplicación.</div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Últimos correos entrantes (INBOX)</h5>
                            </div>
                            <div class="card-body">
                                <?php foreach ($warnings as $warning) : ?>
                                    <div class="alert alert-warning mb-3"><?php echo htmlspecialchars($warning, ENT_QUOTES, 'UTF-8'); ?></div>
                                <?php endforeach; ?>

                                <?php if (empty($emails)) : ?>
                                    <p class="text-muted mb-0">No hay correos para mostrar.</p>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>UID</th>
                                                    <th>Asunto</th>
                                                    <th>Remitente</th>
                                                    <th>Fecha</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($emails as $email) : ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars((string) $email['uid'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($email['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars($email['from'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td><?php echo htmlspecialchars((string) $email['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                        <td>
                                                            <?php if ($email['seen']) : ?>
                                                                <span class="badge bg-light text-dark">Leído</span>
                                                            <?php else : ?>
                                                                <span class="badge bg-primary">No leído</span>
                                                            <?php endif; ?>
                                                        </td>
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

            <?php include('partials/footer.php'); ?>
        </div>
    </div>

    <?php include('partials/customizer.php'); ?>
    <?php include('partials/footer-scripts.php'); ?>

    <script>
        document.getElementById('btn-gmail')?.addEventListener('click', function () {
            document.getElementById('host-imap').value = 'imap.gmail.com';
            document.getElementById('puerto-imap').value = '993';
            document.getElementById('seguridad-imap').value = 'ssl';
        });
    </script>
</body>

</html>
