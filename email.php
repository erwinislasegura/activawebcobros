<?php
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/email_module.php';

$allowedFolders = ['inbox', 'outbox', 'sent', 'spam', 'compose'];
$folder = strtolower(trim($_GET['folder'] ?? 'inbox'));
if (!in_array($folder, $allowedFolders, true)) {
    $folder = 'inbox';
}

$config = email_get_account_config();
$errors = [];
$warnings = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_inbox') {
        $config['in_email'] = trim($_POST['in_email'] ?? '');
        $config['in_password'] = trim($_POST['in_password'] ?? '');
        $config['in_host'] = trim($_POST['in_host'] ?? '');
        $config['in_port'] = (int) ($_POST['in_port'] ?? 993);
        $config['in_security'] = trim($_POST['in_security'] ?? 'ssl');

        if ($config['in_email'] === '' || $config['in_password'] === '' || $config['in_host'] === '') {
            $errors[] = 'Para buzón de entrada debes completar correo, contraseña y host.';
        }

        if (empty($errors)) {
            email_save_config($config);
            $success = 'Configuración de buzón de entrada guardada.';
        }
    }

    if ($action === 'save_outbox') {
        $config['out_email'] = trim($_POST['out_email'] ?? '');
        $config['out_name'] = trim($_POST['out_name'] ?? '');
        $config['out_password'] = trim($_POST['out_password'] ?? '');
        $config['out_host'] = trim($_POST['out_host'] ?? '');
        $config['out_port'] = (int) ($_POST['out_port'] ?? 587);
        $config['out_security'] = trim($_POST['out_security'] ?? 'tls');

        if ($config['out_email'] === '') {
            $errors[] = 'El correo saliente es obligatorio.';
        }

        if (empty($errors)) {
            email_save_config($config);
            $success = 'Configuración de buzón de salida guardada.';
        }
    }

    if ($action === 'compose_send' || $action === 'compose_draft') {
        $recipient = trim($_POST['recipient'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');

        if ($subject === '') {
            $subject = '(Sin asunto)';
        }

        if ($action === 'compose_draft') {
            email_local_store('draft', $recipient, $subject, $body, 'draft');
            $success = 'Correo guardado en borradores (Buzón salida).';
            $folder = 'outbox';
        } else {
            if ($recipient === '') {
                $errors[] = 'Debes indicar un destinatario para enviar.';
            }

            if (empty($errors)) {
                $fromEmail = $config['out_email'] ?: ($config['in_email'] ?? '');
                $fromName = $config['out_name'] ?: 'Sistema';

                $headers = [
                    'MIME-Version: 1.0',
                    'Content-type: text/html; charset=UTF-8',
                    'From: ' . $fromName . ' <' . $fromEmail . '>',
                ];

                $sent = @mail($recipient, $subject, $body, implode("\r\n", $headers));
                if ($sent) {
                    email_local_store('sent', $recipient, $subject, $body, 'sent');
                    $success = 'Correo enviado correctamente.';
                    $folder = 'sent';
                } else {
                    email_local_store('outbox', $recipient, $subject, $body, 'error_envio');
                    $warnings[] = 'No se pudo enviar con mail() en este entorno. Se guardó en Buzón salida.';
                    $folder = 'outbox';
                }
            }
        }
    }
}

$imapData = ['emails' => [], 'warning' => null];
$localMessages = [];

if (in_array($folder, ['inbox', 'sent', 'spam'], true)) {
    $imapData = email_fetch_messages($config, $folder);
    if ($imapData['warning']) {
        $warnings[] = $imapData['warning'];
    }
}

if ($folder === 'outbox') {
    $localMessages = array_merge(email_local_list('outbox'), email_local_list('draft'));
}

$pageTitles = [
    'inbox' => 'Buzón entrada',
    'outbox' => 'Buzón salida',
    'sent' => 'Enviados',
    'spam' => 'Spam',
    'compose' => 'Redactar',
];
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = 'Email - ' . $pageTitles[$folder]; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>

    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Mantenedores'; $title = 'Email · ' . $pageTitles[$folder]; include('partials/page-title.php'); ?>

            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex gap-2 flex-wrap">
                        <a class="btn btn-sm <?php echo $folder === 'inbox' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=inbox">Buzón entrada</a>
                        <a class="btn btn-sm <?php echo $folder === 'outbox' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=outbox">Buzón salida</a>
                        <a class="btn btn-sm <?php echo $folder === 'sent' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=sent">Enviados</a>
                        <a class="btn btn-sm <?php echo $folder === 'spam' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=spam">Spam</a>
                        <a class="btn btn-sm <?php echo $folder === 'compose' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=compose">Redactar</a>
                    </div>
                </div>
            </div>

            <?php if ($success) : ?><div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php foreach ($errors as $e) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
            <?php foreach ($warnings as $w) : ?><div class="alert alert-warning"><?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>

            <?php if ($folder === 'inbox') : ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Configuración Buzón entrada (independiente)</h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="preset-gmail-in">Preset Gmail</button>
                    </div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="save_inbox">
                            <div class="col-md-4"><label class="form-label">Correo entrada</label><input class="form-control" type="email" name="in_email" value="<?php echo htmlspecialchars($config['in_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-4"><label class="form-label">Contraseña app</label><input class="form-control" type="password" name="in_password" value="<?php echo htmlspecialchars($config['in_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-4"><label class="form-label">Host IMAP</label><input class="form-control" id="in-host" type="text" name="in_host" value="<?php echo htmlspecialchars($config['in_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Puerto</label><input class="form-control" id="in-port" type="number" name="in_port" value="<?php echo htmlspecialchars((string) ($config['in_port'] ?? 993), ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Seguridad</label>
                                <select class="form-select" id="in-security" name="in_security">
                                    <?php $s = $config['in_security'] ?? 'ssl'; ?>
                                    <option value="ssl" <?php echo $s === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="tls" <?php echo $s === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="none" <?php echo $s === 'none' ? 'selected' : ''; ?>>None</option>
                                </select>
                            </div>
                            <div class="col-12"><button class="btn btn-primary" type="submit">Guardar entrada</button></div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($folder === 'outbox') : ?>
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Configuración Buzón salida (independiente)</h5>
                        <button class="btn btn-sm btn-outline-secondary" type="button" id="preset-gmail-out">Preset Gmail SMTP</button>
                    </div>
                    <div class="card-body">
                        <form method="post" class="row g-3">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="save_outbox">
                            <div class="col-md-4"><label class="form-label">Correo salida</label><input class="form-control" type="email" name="out_email" value="<?php echo htmlspecialchars($config['out_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-4"><label class="form-label">Nombre remitente</label><input class="form-control" type="text" name="out_name" value="<?php echo htmlspecialchars($config['out_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-4"><label class="form-label">Contraseña app</label><input class="form-control" type="password" name="out_password" value="<?php echo htmlspecialchars($config['out_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-4"><label class="form-label">Host SMTP</label><input class="form-control" id="out-host" type="text" name="out_host" value="<?php echo htmlspecialchars($config['out_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Puerto</label><input class="form-control" id="out-port" type="number" name="out_port" value="<?php echo htmlspecialchars((string) ($config['out_port'] ?? 587), ENT_QUOTES, 'UTF-8'); ?>"></div>
                            <div class="col-md-2"><label class="form-label">Seguridad</label>
                                <?php $so = $config['out_security'] ?? 'tls'; ?>
                                <select class="form-select" id="out-security" name="out_security">
                                    <option value="tls" <?php echo $so === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="ssl" <?php echo $so === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="none" <?php echo $so === 'none' ? 'selected' : ''; ?>>None</option>
                                </select>
                            </div>
                            <div class="col-12"><button class="btn btn-primary" type="submit">Guardar salida</button></div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (in_array($folder, ['inbox', 'sent', 'spam'], true)) : ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Listado <?php echo htmlspecialchars($pageTitles[$folder], ENT_QUOTES, 'UTF-8'); ?></h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead><tr><th>UID</th><th>Asunto</th><th>De</th><th>Para</th><th>Fecha</th><th>Estado</th></tr></thead>
                                <tbody>
                                <?php if (empty($imapData['emails'])) : ?>
                                    <tr><td colspan="6" class="text-muted">Sin mensajes para mostrar.</td></tr>
                                <?php else : foreach ($imapData['emails'] as $m) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string) $m['uid'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($m['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($m['from'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($m['to'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $m['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo !empty($m['seen']) ? '<span class="badge bg-light text-dark">Leído</span>' : '<span class="badge bg-primary">No leído</span>'; ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($folder === 'outbox') : ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Pendientes / Borradores locales</h5></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead><tr><th>Destinatario</th><th>Asunto</th><th>Estado</th><th>Fecha</th></tr></thead>
                                <tbody>
                                <?php if (empty($localMessages)) : ?>
                                    <tr><td colspan="4" class="text-muted">No hay registros.</td></tr>
                                <?php else : foreach ($localMessages as $msg) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars((string) ($msg['recipient'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $msg['subject'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $msg['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars((string) $msg['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($folder === 'compose') : ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Redactar correo</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="mb-3"><label class="form-label">Para</label><input type="email" class="form-control" name="recipient" placeholder="destinatario@dominio.cl"></div>
                            <div class="mb-3"><label class="form-label">Asunto</label><input type="text" class="form-control" name="subject"></div>
                            <div class="mb-3"><label class="form-label">Mensaje (HTML)</label><textarea class="form-control" name="body" rows="10"></textarea></div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" type="submit" name="action" value="compose_send">Enviar</button>
                                <button class="btn btn-outline-secondary" type="submit" name="action" value="compose_draft">Guardar borrador</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php include('partials/footer.php'); ?>
    </div>
</div>

<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
<script>
    document.getElementById('preset-gmail-in')?.addEventListener('click', function () {
        document.getElementById('in-host').value = 'imap.gmail.com';
        document.getElementById('in-port').value = '993';
        document.getElementById('in-security').value = 'ssl';
    });
    document.getElementById('preset-gmail-out')?.addEventListener('click', function () {
        document.getElementById('out-host').value = 'smtp.gmail.com';
        document.getElementById('out-port').value = '587';
        document.getElementById('out-security').value = 'tls';
    });
</script>
</body>
</html>
