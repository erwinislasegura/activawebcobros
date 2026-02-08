<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS notificacion_whatsapp (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT,
            phone_number_id VARCHAR(80) NOT NULL,
            access_token TEXT NOT NULL,
            numero_envio VARCHAR(30) DEFAULT NULL,
            country_code VARCHAR(6) DEFAULT NULL,
            template_name VARCHAR(120) DEFAULT NULL,
            template_language VARCHAR(10) DEFAULT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
} catch (Error $e) {
}

$stmt = db()->query('SELECT * FROM notificacion_whatsapp LIMIT 1');
$whatsappConfig = $stmt->fetch() ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $phoneNumberId = trim($_POST['phone_number_id'] ?? '');
    $accessToken = trim($_POST['access_token'] ?? '');
    $numeroEnvio = trim($_POST['numero_envio'] ?? '');
    $countryCode = trim($_POST['country_code'] ?? '');
    $templateName = trim($_POST['template_name'] ?? '');
    $templateLanguage = trim($_POST['template_language'] ?? '');

    if ($phoneNumberId === '' || $accessToken === '') {
        $errors[] = 'Completa los campos obligatorios de WhatsApp.';
    }

    if (empty($errors)) {
        $stmt = db()->query('SELECT id FROM notificacion_whatsapp LIMIT 1');
        $id = $stmt->fetchColumn();

        if ($id) {
            $stmtUpdate = db()->prepare('UPDATE notificacion_whatsapp SET phone_number_id = ?, access_token = ?, numero_envio = ?, country_code = ?, template_name = ?, template_language = ? WHERE id = ?');
            $stmtUpdate->execute([
                $phoneNumberId,
                $accessToken,
                $numeroEnvio !== '' ? $numeroEnvio : null,
                $countryCode !== '' ? $countryCode : null,
                $templateName !== '' ? $templateName : null,
                $templateLanguage !== '' ? $templateLanguage : null,
                $id,
            ]);
        } else {
            $stmtInsert = db()->prepare('INSERT INTO notificacion_whatsapp (phone_number_id, access_token, numero_envio, country_code, template_name, template_language) VALUES (?, ?, ?, ?, ?, ?)');
            $stmtInsert->execute([
                $phoneNumberId,
                $accessToken,
                $numeroEnvio !== '' ? $numeroEnvio : null,
                $countryCode !== '' ? $countryCode : null,
                $templateName !== '' ? $templateName : null,
                $templateLanguage !== '' ? $templateLanguage : null,
            ]);
        }

        redirect('notificaciones-whatsapp.php');
    }

    $whatsappConfig = [
        'phone_number_id' => $phoneNumberId,
        'access_token' => $accessToken,
        'numero_envio' => $numeroEnvio,
        'country_code' => $countryCode,
        'template_name' => $templateName,
        'template_language' => $templateLanguage,
    ];
}
?>
<?php include('partials/html.php'); ?>

<head>
    <?php $title = "WhatsApp de notificaciones"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">

                <?php $subtitle = "Mantenedores"; $title = "WhatsApp de notificaciones"; include('partials/page-title.php'); ?>

                <div class="row">
                    <div class="col-12">
                        <div class="card gm-section">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h5 class="card-title mb-0">WhatsApp Business API</h5>
                                    <p class="text-muted mb-0">Configura el número y credenciales para enviar enlaces por WhatsApp.</p>
                                </div>
                                <button type="submit" form="whatsapp-config-form" class="btn btn-primary">Guardar configuración</button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($errors)) : ?>
                                    <div class="alert alert-danger">
                                        <?php foreach ($errors as $error) : ?>
                                            <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>

                                <form id="whatsapp-config-form" method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="phone-number-id">Phone Number ID</label>
                                            <input type="text" id="phone-number-id" name="phone_number_id" class="form-control" value="<?php echo htmlspecialchars($whatsappConfig['phone_number_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="access-token">Access Token</label>
                                            <input type="password" id="access-token" name="access_token" class="form-control" value="<?php echo htmlspecialchars($whatsappConfig['access_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="numero-envio">Número de envío (visible)</label>
                                            <input type="text" id="numero-envio" name="numero_envio" class="form-control" value="<?php echo htmlspecialchars($whatsappConfig['numero_envio'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="+56 9 1234 5678">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="country-code">Código de país por defecto</label>
                                            <input type="text" id="country-code" name="country_code" class="form-control" value="<?php echo htmlspecialchars($whatsappConfig['country_code'] ?? '56', ENT_QUOTES, 'UTF-8'); ?>" placeholder="56">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="template-name">Nombre de plantilla (opcional)</label>
                                            <input type="text" id="template-name" name="template_name" class="form-control" value="<?php echo htmlspecialchars($whatsappConfig['template_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <div class="form-text">Deja vacío para enviar mensajes en texto libre dentro de la ventana de 24 horas.</div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label" for="template-language">Idioma de plantilla (opcional)</label>
                                            <input type="text" id="template-language" name="template_language" class="form-control" value="<?php echo htmlspecialchars($whatsappConfig['template_language'] ?? 'es', ENT_QUOTES, 'UTF-8'); ?>" placeholder="es">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

</body>

</html>
