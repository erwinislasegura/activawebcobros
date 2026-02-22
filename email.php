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

if (isset($_GET['action'], $_GET['uid']) && in_array($folder, ['inbox', 'sent', 'spam'], true)) {
    $uid = (int) $_GET['uid'];
    if ($_GET['action'] === 'mark_read') {
        $warning = email_mark_seen($config, $folder, $uid, true);
        if ($warning) {
            $warnings[] = $warning;
        }
    }
    if ($_GET['action'] === 'mark_unread') {
        $warning = email_mark_seen($config, $folder, $uid, false);
        if ($warning) {
            $warnings[] = $warning;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'save_inbox') {
        $config['in_email'] = trim($_POST['in_email'] ?? '');
        $config['in_password'] = trim($_POST['in_password'] ?? '');
        $config['in_host'] = trim($_POST['in_host'] ?? '');
        $config['in_port'] = (int) ($_POST['in_port'] ?? 993);
        $config['in_security'] = trim($_POST['in_security'] ?? 'ssl');
        if ($config['in_email'] === '' || $config['in_password'] === '' || $config['in_host'] === '') {
            $errors[] = 'Completa correo, contraseña y host para buzón de entrada.';
        }
        if (empty($errors)) {
            email_save_config($config);
            $success = 'Configuración de entrada guardada.';
        }
    }

    if ($action === 'save_outbox') {
        $config['out_email'] = trim($_POST['out_email'] ?? '');
        $config['out_name'] = trim($_POST['out_name'] ?? '');
        $config['out_password'] = trim($_POST['out_password'] ?? '');
        $config['out_host'] = trim($_POST['out_host'] ?? '');
        $config['out_port'] = (int) ($_POST['out_port'] ?? 587);
        $config['out_security'] = trim($_POST['out_security'] ?? 'tls');
        if ($config['out_email'] === '' || $config['out_host'] === '') {
            $errors[] = 'Completa correo y host SMTP para buzón de salida.';
        }
        if (empty($errors)) {
            email_save_config($config);
            $success = 'Configuración de salida guardada.';
        }
    }

    if ($action === 'compose_send' || $action === 'compose_draft') {
        $recipient = trim($_POST['recipient'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $subject = $subject !== '' ? $subject : '(Sin asunto)';

        if ($action === 'compose_draft') {
            email_local_store('draft', $recipient, $subject, $body, 'draft');
            $success = 'Guardado en borradores.';
            $folder = 'outbox';
        } else {
            if ($recipient === '') {
                $errors[] = 'Debes indicar un destinatario.';
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
                    $warnings[] = 'No se pudo enviar; quedó en Buzón salida.';
                    $folder = 'outbox';
                }
            }
        }
    }
}

$folderLabels = [
    'inbox' => 'Buzón entrada',
    'outbox' => 'Buzón salida',
    'sent' => 'Enviados',
    'spam' => 'Spam',
    'compose' => 'Redactar',
];

$search = trim($_GET['q'] ?? '');
$selectedUid = isset($_GET['uid']) ? (int) $_GET['uid'] : 0;
$selectedLocalId = isset($_GET['local_id']) ? (int) $_GET['local_id'] : 0;

$imapData = ['emails' => [], 'warning' => null];
$localMessages = [];
$selectedMessage = null;

if (in_array($folder, ['inbox', 'sent', 'spam'], true)) {
    $imapData = email_fetch_messages($config, $folder);
    if ($imapData['warning']) {
        $warnings[] = $imapData['warning'];
    }

    if ($search !== '') {
        $imapData['emails'] = array_values(array_filter($imapData['emails'], static function ($message) use ($search) {
            $haystack = mb_strtolower($message['subject'] . ' ' . $message['from'] . ' ' . $message['to'], 'UTF-8');
            return mb_strpos($haystack, mb_strtolower($search, 'UTF-8')) !== false;
        }));
    }

    if ($selectedUid > 0) {
        $detail = email_fetch_message_detail($config, $folder, $selectedUid);
        if ($detail['warning']) {
            $warnings[] = $detail['warning'];
        }
        $selectedMessage = $detail['message'];
    }
}

if ($folder === 'outbox') {
    $localMessages = array_merge(email_local_list('outbox'), email_local_list('draft'));
    if ($search !== '') {
        $localMessages = array_values(array_filter($localMessages, static function ($row) use ($search) {
            $haystack = mb_strtolower(($row['subject'] ?? '') . ' ' . ($row['recipient'] ?? ''), 'UTF-8');
            return mb_strpos($haystack, mb_strtolower($search, 'UTF-8')) !== false;
        }));
    }
    if ($selectedLocalId > 0) {
        $selectedMessage = email_local_get($selectedLocalId);
    }
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = 'Email - ' . $folderLabels[$folder]; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
    <style>
        .email-rendered img{max-width:100%;height:auto}
        .email-rendered table{width:100%;border-collapse:collapse}
        .email-rendered td,.email-rendered th{border:1px solid #e5e7eb;padding:6px}
        .message-list .list-group-item.active{background:#f1f5f9;border-color:#e2e8f0;color:#0f172a}
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>

    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Módulo Buzón'; $title = $folderLabels[$folder]; include('partials/page-title.php'); ?>

            <?php if ($success) : ?><div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php foreach ($errors as $e) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
            <?php foreach ($warnings as $w) : ?><div class="alert alert-warning"><?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>

            <div class="card mb-3">
                <div class="card-body d-flex flex-wrap gap-2">
                    <a class="btn btn-sm <?php echo $folder === 'inbox' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=inbox">Entrada</a>
                    <a class="btn btn-sm <?php echo $folder === 'outbox' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=outbox">Salida</a>
                    <a class="btn btn-sm <?php echo $folder === 'sent' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=sent">Enviados</a>
                    <a class="btn btn-sm <?php echo $folder === 'spam' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=spam">Spam</a>
                    <a class="btn btn-sm <?php echo $folder === 'compose' ? 'btn-primary' : 'btn-outline-primary'; ?>" href="email.php?folder=compose">Redactar</a>
                </div>
            </div>

            <?php if ($folder === 'compose') : ?>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Redactar correo</h5></div>
                    <div class="card-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="mb-3"><label class="form-label">Para</label><input class="form-control" type="email" name="recipient"></div>
                            <div class="mb-3"><label class="form-label">Asunto</label><input class="form-control" type="text" name="subject"></div>
                            <div class="mb-3"><label class="form-label">Mensaje</label><textarea class="form-control" rows="12" name="body"></textarea></div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary" type="submit" name="action" value="compose_send">Enviar</button>
                                <button class="btn btn-outline-secondary" type="submit" name="action" value="compose_draft">Guardar borrador</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php else : ?>
                <div class="row">
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <form method="get" class="d-flex gap-2">
                                    <input type="hidden" name="folder" value="<?php echo htmlspecialchars($folder, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input class="form-control" type="text" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar...">
                                    <button class="btn btn-light" type="submit">Buscar</button>
                                </form>
                            </div>
                            <div class="list-group list-group-flush message-list" style="max-height:65vh;overflow:auto;">
                                <?php if (in_array($folder, ['inbox','sent','spam'], true)) : ?>
                                    <?php if (empty($imapData['emails'])) : ?>
                                        <div class="p-3 text-muted">No hay correos en esta carpeta.</div>
                                    <?php else : foreach ($imapData['emails'] as $mail) : ?>
                                        <a class="list-group-item list-group-item-action <?php echo $selectedUid === (int) $mail['uid'] ? 'active' : ''; ?>" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $mail['uid']; ?>&q=<?php echo urlencode($search); ?>">
                                            <div class="fw-semibold text-truncate"><?php echo htmlspecialchars($mail['subject'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="small text-muted text-truncate"><?php echo htmlspecialchars($mail['from'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        </a>
                                    <?php endforeach; endif; ?>
                                <?php else : ?>
                                    <?php if (empty($localMessages)) : ?>
                                        <div class="p-3 text-muted">Sin pendientes ni borradores.</div>
                                    <?php else : foreach ($localMessages as $msg) : ?>
                                        <a class="list-group-item list-group-item-action <?php echo $selectedLocalId === (int) $msg['id'] ? 'active' : ''; ?>" href="email.php?folder=outbox&local_id=<?php echo (int) $msg['id']; ?>&q=<?php echo urlencode($search); ?>">
                                            <div class="fw-semibold text-truncate"><?php echo htmlspecialchars((string) $msg['subject'], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <div class="small text-muted text-truncate"><?php echo htmlspecialchars((string) ($msg['recipient'] ?: 'Sin destinatario'), ENT_QUOTES, 'UTF-8'); ?></div>
                                        </a>
                                    <?php endforeach; endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-8">
                        <div class="card mb-3">
                            <div class="card-header"><h5 class="card-title mb-0">Detalle</h5></div>
                            <div class="card-body">
                                <?php if ($selectedMessage) : ?>
                                    <h5><?php echo htmlspecialchars((string) ($selectedMessage['subject'] ?? '(Sin asunto)'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <div class="text-muted mb-2">De: <?php echo htmlspecialchars((string) ($selectedMessage['from'] ?? ($selectedMessage['recipient'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if (isset($selectedMessage['uid'])) : ?>
                                        <div class="mb-3 d-flex gap-2">
                                            <a class="btn btn-sm btn-outline-secondary" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $selectedMessage['uid']; ?>&action=mark_read">Marcar leído</a>
                                            <a class="btn btn-sm btn-outline-secondary" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $selectedMessage['uid']; ?>&action=mark_unread">Marcar no leído</a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="email-rendered"><?php echo $selectedMessage['body_html'] ?? nl2br(htmlspecialchars((string) ($selectedMessage['body'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></div>
                                <?php else : ?>
                                    <div class="text-muted">Selecciona un correo para ver su contenido.</div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($folder === 'inbox') : ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Configuración IMAP (Entrada)</h5>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="preset-gmail-in">Preset Gmail</button>
                                </div>
                                <div class="card-body">
                                    <form method="post" class="row g-3">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="action" value="save_inbox">
                                        <div class="col-md-6"><input class="form-control" name="in_email" type="email" placeholder="Correo" value="<?php echo htmlspecialchars($config['in_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-6"><input class="form-control" name="in_password" type="password" placeholder="Contraseña" value="<?php echo htmlspecialchars($config['in_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-4"><input class="form-control" id="in-host" name="in_host" type="text" placeholder="Host" value="<?php echo htmlspecialchars($config['in_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-2"><input class="form-control" id="in-port" name="in_port" type="number" value="<?php echo htmlspecialchars((string) ($config['in_port'] ?? 993), ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-3"><?php $is = $config['in_security'] ?? 'ssl'; ?><select class="form-select" id="in-security" name="in_security"><option value="ssl" <?php echo $is === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="tls" <?php echo $is === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="none" <?php echo $is === 'none' ? 'selected' : ''; ?>>None</option></select></div>
                                        <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">Guardar entrada</button></div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if ($folder === 'outbox') : ?>
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Configuración SMTP (Salida)</h5>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="preset-gmail-out">Preset Gmail</button>
                                </div>
                                <div class="card-body">
                                    <form method="post" class="row g-3">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="action" value="save_outbox">
                                        <div class="col-md-4"><input class="form-control" name="out_email" type="email" placeholder="Correo" value="<?php echo htmlspecialchars($config['out_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-4"><input class="form-control" name="out_name" type="text" placeholder="Nombre remitente" value="<?php echo htmlspecialchars($config['out_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-4"><input class="form-control" name="out_password" type="password" placeholder="Contraseña" value="<?php echo htmlspecialchars($config['out_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-4"><input class="form-control" id="out-host" name="out_host" type="text" placeholder="Host SMTP" value="<?php echo htmlspecialchars($config['out_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-2"><input class="form-control" id="out-port" name="out_port" type="number" value="<?php echo htmlspecialchars((string) ($config['out_port'] ?? 587), ENT_QUOTES, 'UTF-8'); ?>"></div>
                                        <div class="col-md-3"><?php $os = $config['out_security'] ?? 'tls'; ?><select class="form-select" id="out-security" name="out_security"><option value="tls" <?php echo $os === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="ssl" <?php echo $os === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="none" <?php echo $os === 'none' ? 'selected' : ''; ?>>None</option></select></div>
                                        <div class="col-md-3"><button class="btn btn-primary w-100" type="submit">Guardar salida</button></div>
                                    </form>
                                </div>
                            </div>
                        <?php endif; ?>
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
