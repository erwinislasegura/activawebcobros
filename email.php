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
        .email-rendered{max-width:100%;overflow-wrap:anywhere;word-break:break-word}
        .email-rendered img{max-width:100%;height:auto}
        .email-rendered pre{white-space:pre-wrap;word-break:break-word}
        .email-rendered table{display:block;max-width:100%;overflow-x:auto;border-collapse:collapse}
        .email-rendered td,.email-rendered th{border:1px solid #e5e7eb;padding:6px;vertical-align:top}
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
                        <div class="card">
                            <div class="card-header"><h5 class="card-title mb-0">Detalle</h5></div>
                            <div class="card-body" style="overflow:hidden;">
                                <?php if ($selectedMessage) : ?>
                                    <h5><?php echo htmlspecialchars((string) ($selectedMessage['subject'] ?? '(Sin asunto)'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <div class="text-muted mb-2">De: <?php echo htmlspecialchars((string) ($selectedMessage['from'] ?? ($selectedMessage['recipient'] ?? '-')), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if (isset($selectedMessage['uid'])) : ?>
                                        <div class="mb-3 d-flex gap-2">
                                            <a class="btn btn-sm btn-outline-secondary" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $selectedMessage['uid']; ?>&action=mark_read">Marcar leído</a>
                                            <a class="btn btn-sm btn-outline-secondary" href="email.php?folder=<?php echo urlencode($folder); ?>&uid=<?php echo (int) $selectedMessage['uid']; ?>&action=mark_unread">Marcar no leído</a>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($selectedMessage['attachments'])) : ?>
                                        <div class="mb-3">
                                            <div class="small text-muted mb-1">Adjuntos</div>
                                            <div class="d-flex flex-wrap gap-2">
                                                <?php foreach ($selectedMessage['attachments'] as $adj) : ?>
                                                    <span class="badge bg-light text-dark border"><?php echo htmlspecialchars((string) $adj['filename'], ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars((string) $adj['mime'], ENT_QUOTES, 'UTF-8'); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="email-rendered"><?php echo $selectedMessage['body_html'] ?? nl2br(htmlspecialchars((string) ($selectedMessage['body'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></div>
                                <?php else : ?>
                                    <div class="text-muted">Selecciona un correo para ver su contenido.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php include('partials/footer.php'); ?>
    </div>
</div>

<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
</body>
</html>
