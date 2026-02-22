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
        .gmail-shell{background:#111827;border-radius:18px;overflow:hidden;color:#e5e7eb}
        .gmail-top{background:#1f2937;padding:12px 16px;border-bottom:1px solid #374151}
        .gmail-search{background:#374151;border:none;color:#fff;border-radius:999px;padding:10px 16px;width:100%}
        .gmail-main{display:grid;grid-template-columns:260px 390px 1fr;min-height:70vh}
        .gmail-left{background:#0f172a;padding:14px;border-right:1px solid #374151}
        .gmail-list{background:#1f2937;border-right:1px solid #374151}
        .gmail-read{background:#111827;padding:18px}
        .gmail-btn-compose{background:#e5e7eb;color:#111827;border-radius:14px;font-weight:600}
        .gmail-menu a{display:flex;justify-content:space-between;gap:8px;color:#e5e7eb;padding:7px 10px;border-radius:999px;text-decoration:none}
        .gmail-menu a.active,.gmail-menu a:hover{background:#334155}
        .gmail-row{display:block;color:#e5e7eb;text-decoration:none;padding:10px 12px;border-bottom:1px solid #374151}
        .gmail-row:hover,.gmail-row.active{background:#334155}
        .gmail-subject{font-weight:600}
        .gmail-meta{font-size:12px;color:#9ca3af}
        .gmail-empty{color:#9ca3af;padding:24px}
        .gmail-card{background:#0f172a;border:1px solid #374151;border-radius:14px;padding:14px;margin-bottom:14px}
        .gmail-read-body{background:#0b1220;border:1px solid #374151;border-radius:10px;padding:12px;color:#d1d5db;min-height:220px}
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = 'Mantenedores'; $title = 'Email'; include('partials/page-title.php'); ?>

            <?php if ($success) : ?><div class="alert alert-success"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php foreach ($errors as $e) : ?><div class="alert alert-danger"><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>
            <?php foreach ($warnings as $w) : ?><div class="alert alert-warning"><?php echo htmlspecialchars($w, ENT_QUOTES, 'UTF-8'); ?></div><?php endforeach; ?>

            <div class="gmail-shell">
                <div class="gmail-top">
                    <form method="get" class="row g-2 align-items-center">
                        <input type="hidden" name="folder" value="<?php echo htmlspecialchars($folder, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="col-md-10"><input class="gmail-search" type="text" name="q" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Buscar correo"></div>
                        <div class="col-md-2 text-end"><button class="btn btn-sm btn-light" type="submit">Buscar</button></div>
                    </form>
                </div>

                <div class="gmail-main">
                    <aside class="gmail-left">
                        <a href="email.php?folder=compose" class="btn gmail-btn-compose w-100 mb-3">✎ Redactar</a>
                        <div class="gmail-menu">
                            <a class="<?php echo $folder === 'inbox' ? 'active' : ''; ?>" href="email.php?folder=inbox"><span>Recibidos</span></a>
                            <a class="<?php echo $folder === 'outbox' ? 'active' : ''; ?>" href="email.php?folder=outbox"><span>Buzón salida</span></a>
                            <a class="<?php echo $folder === 'sent' ? 'active' : ''; ?>" href="email.php?folder=sent"><span>Enviados</span></a>
                            <a class="<?php echo $folder === 'spam' ? 'active' : ''; ?>" href="email.php?folder=spam"><span>Spam</span></a>
                            <a class="<?php echo $folder === 'compose' ? 'active' : ''; ?>" href="email.php?folder=compose"><span>Redactar</span></a>
                        </div>

                        <?php if ($folder === 'inbox') : ?>
                            <div class="gmail-card mt-3">
                                <h6>Config. Entrada (IMAP)</h6>
                                <form method="post" class="row g-2">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="save_inbox">
                                    <div class="col-12"><input class="form-control form-control-sm" name="in_email" type="email" placeholder="Correo" value="<?php echo htmlspecialchars($config['in_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-12"><input class="form-control form-control-sm" name="in_password" type="password" placeholder="Contraseña" value="<?php echo htmlspecialchars($config['in_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-12"><input class="form-control form-control-sm" id="in-host" name="in_host" type="text" placeholder="Host" value="<?php echo htmlspecialchars($config['in_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-6"><input class="form-control form-control-sm" id="in-port" name="in_port" type="number" value="<?php echo htmlspecialchars((string) ($config['in_port'] ?? 993), ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-6">
                                        <?php $is = $config['in_security'] ?? 'ssl'; ?>
                                        <select class="form-select form-select-sm" id="in-security" name="in_security"><option value="ssl" <?php echo $is === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="tls" <?php echo $is === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="none" <?php echo $is === 'none' ? 'selected' : ''; ?>>None</option></select>
                                    </div>
                                    <div class="col-6"><button class="btn btn-sm btn-outline-light w-100" type="button" id="preset-gmail-in">Gmail</button></div>
                                    <div class="col-6"><button class="btn btn-sm btn-primary w-100" type="submit">Guardar</button></div>
                                </form>
                            </div>
                        <?php endif; ?>

                        <?php if ($folder === 'outbox') : ?>
                            <div class="gmail-card mt-3">
                                <h6>Config. Salida (SMTP)</h6>
                                <form method="post" class="row g-2">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="action" value="save_outbox">
                                    <div class="col-12"><input class="form-control form-control-sm" name="out_email" type="email" placeholder="Correo" value="<?php echo htmlspecialchars($config['out_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-12"><input class="form-control form-control-sm" name="out_name" type="text" placeholder="Nombre" value="<?php echo htmlspecialchars($config['out_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-12"><input class="form-control form-control-sm" name="out_password" type="password" placeholder="Contraseña" value="<?php echo htmlspecialchars($config['out_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-12"><input class="form-control form-control-sm" id="out-host" name="out_host" type="text" placeholder="Host" value="<?php echo htmlspecialchars($config['out_host'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-6"><input class="form-control form-control-sm" id="out-port" name="out_port" type="number" value="<?php echo htmlspecialchars((string) ($config['out_port'] ?? 587), ENT_QUOTES, 'UTF-8'); ?>"></div>
                                    <div class="col-6">
                                        <?php $os = $config['out_security'] ?? 'tls'; ?>
                                        <select class="form-select form-select-sm" id="out-security" name="out_security"><option value="tls" <?php echo $os === 'tls' ? 'selected' : ''; ?>>TLS</option><option value="ssl" <?php echo $os === 'ssl' ? 'selected' : ''; ?>>SSL</option><option value="none" <?php echo $os === 'none' ? 'selected' : ''; ?>>None</option></select>
                                    </div>
                                    <div class="col-6"><button class="btn btn-sm btn-outline-light w-100" type="button" id="preset-gmail-out">Gmail</button></div>
                                    <div class="col-6"><button class="btn btn-sm btn-primary w-100" type="submit">Guardar</button></div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </aside>

                    <section class="gmail-list">
                        <?php if (in_array($folder, ['inbox', 'sent', 'spam'], true)) : ?>
                            <?php if (empty($imapData['emails'])) : ?>
                                <div class="gmail-empty">Sin correos en <?php echo htmlspecialchars($folderLabels[$folder], ENT_QUOTES, 'UTF-8'); ?>.</div>
                            <?php else : foreach ($imapData['emails'] as $mail) : ?>
                                <a class="gmail-row <?php echo $selectedUid === (int) $mail['uid'] ? 'active' : ''; ?>" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $mail['uid']; ?>&q=<?php echo urlencode($search); ?>">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div class="gmail-subject"><?php echo htmlspecialchars($mail['subject'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <small><?php echo htmlspecialchars(substr((string) $mail['date'], 0, 5), ENT_QUOTES, 'UTF-8'); ?></small>
                                    </div>
                                    <div class="gmail-meta"><?php echo htmlspecialchars($mail['from'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="gmail-meta"><?php echo !empty($mail['seen']) ? 'Leído' : 'No leído'; ?></div>
                                </a>
                            <?php endforeach; endif; ?>
                        <?php elseif ($folder === 'outbox') : ?>
                            <?php if (empty($localMessages)) : ?>
                                <div class="gmail-empty">No hay pendientes ni borradores.</div>
                            <?php else : foreach ($localMessages as $msg) : ?>
                                <a class="gmail-row <?php echo $selectedLocalId === (int) $msg['id'] ? 'active' : ''; ?>" href="email.php?folder=outbox&local_id=<?php echo (int) $msg['id']; ?>&q=<?php echo urlencode($search); ?>">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div class="gmail-subject"><?php echo htmlspecialchars((string) $msg['subject'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <small><?php echo htmlspecialchars(substr((string) $msg['created_at'], 11, 5), ENT_QUOTES, 'UTF-8'); ?></small>
                                    </div>
                                    <div class="gmail-meta"><?php echo htmlspecialchars((string) ($msg['recipient'] ?: 'Sin destinatario'), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="gmail-meta">Estado: <?php echo htmlspecialchars((string) $msg['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                                </a>
                            <?php endforeach; endif; ?>
                        <?php else : ?>
                            <div class="gmail-empty">Usa Redactar para crear un correo.</div>
                        <?php endif; ?>
                    </section>

                    <section class="gmail-read">
                        <?php if ($folder === 'compose') : ?>
                            <h5 class="mb-3">Redactar</h5>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="mb-2"><input class="form-control" type="email" name="recipient" placeholder="Para"></div>
                                <div class="mb-2"><input class="form-control" type="text" name="subject" placeholder="Asunto"></div>
                                <div class="mb-2"><textarea class="form-control" name="body" rows="14" placeholder="Escribe tu mensaje (HTML permitido)"></textarea></div>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary" type="submit" name="action" value="compose_send">Enviar</button>
                                    <button class="btn btn-outline-light" type="submit" name="action" value="compose_draft">Guardar borrador</button>
                                </div>
                            </form>
                        <?php elseif ($selectedMessage) : ?>
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars((string) ($selectedMessage['subject'] ?? '(Sin asunto)'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <div class="gmail-meta">De: <?php echo htmlspecialchars((string) ($selectedMessage['from'] ?? ($selectedMessage['recipient'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="gmail-meta">Para: <?php echo htmlspecialchars((string) ($selectedMessage['to'] ?? '-'), ENT_QUOTES, 'UTF-8'); ?></div>
                                </div>
                                <?php if (isset($selectedMessage['uid'])) : ?>
                                    <div class="d-flex gap-2">
                                        <a class="btn btn-sm btn-outline-light" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $selectedMessage['uid']; ?>&action=mark_read">Marcar leído</a>
                                        <a class="btn btn-sm btn-outline-light" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $selectedMessage['uid']; ?>&action=mark_unread">Marcar no leído</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="gmail-read-body">
                                <?php if (isset($selectedMessage['body_html'])) : ?>
                                    <?php echo $selectedMessage['body_html']; ?>
                                <?php else : ?>
                                    <pre style="white-space:pre-wrap;color:#d1d5db"><?php echo htmlspecialchars((string) ($selectedMessage['body'] ?? 'Sin contenido'), ENT_QUOTES, 'UTF-8'); ?></pre>
                                <?php endif; ?>
                            </div>
                        <?php else : ?>
                            <div class="gmail-empty">No se ha seleccionado ninguna conversación.</div>
                        <?php endif; ?>
                    </section>
                </div>
            </div>
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
