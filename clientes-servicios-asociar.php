<?php
require __DIR__ . '/app/bootstrap.php';

$errors = [];
$errorMessage = '';
$success = $_GET['success'] ?? '';
$asociacionEdit = null;
$filtroTexto = trim((string) ($_GET['q'] ?? ''));


function normalizar_periodicidad_servicio(string $periodicidad): string
{
    $periodicidad = strtolower(trim($periodicidad));
    $periodicidad = str_replace(['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'], ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'], $periodicidad);
    return $periodicidad;
}

function calcular_fecha_vencimiento(string $fechaRegistro, string $periodicidad): ?string
{
    if ($fechaRegistro === '') {
        return null;
    }

    $norm = normalizar_periodicidad_servicio($periodicidad);
    $intervalos = [
        'mensual' => '+1 month',
        'bimestral' => '+2 months',
        'trimestral' => '+3 months',
        'semestral' => '+6 months',
        'anual' => '+1 year',
        'un pago' => '+0 day',
        'unpago' => '+0 day',
    ];

    if (!isset($intervalos[$norm])) {
        return null;
    }

    return date('Y-m-d', strtotime($fechaRegistro . ' ' . $intervalos[$norm]));
}

function calcular_dias_faltantes(?string $fechaVencimiento): ?int
{
    $fechaVencimiento = trim((string) $fechaVencimiento);
    if ($fechaVencimiento === '') {
        return null;
    }

    try {
        $hoy = new DateTime('today');
        $venc = new DateTime($fechaVencimiento);
        $diff = $hoy->diff($venc);
        return (int) $diff->format('%r%a');
    } catch (Exception $e) {
        return null;
    }
}

function ensure_column(string $table, string $column, string $definition): void
{
    $dbName = $GLOBALS['config']['db']['name'] ?? '';
    if ($dbName === '') {
        return;
    }
    $stmt = db()->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->execute([$dbName, $table, $column]);
    if ((int) $stmt->fetchColumn() === 0) {
        db()->exec(sprintf('ALTER TABLE %s ADD COLUMN %s %s', $table, $column, $definition));
    }
}


function normalize_email_list(string $raw): array
{
    $parts = preg_split('/[;,\s]+/', $raw) ?: [];
    $emails = [];
    foreach ($parts as $email) {
        $email = mb_strtolower(trim($email), 'UTF-8');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        if (!in_array($email, $emails, true)) {
            $emails[] = $email;
        }
    }
    return $emails;
}

function enviar_correo_cotizacion(array $cliente, array $lineas, string $codigoCotizacion, string $nota, int $validezDias, string $fechaValidez, string $correoCco = ''): bool
{
    if (empty($cliente['correo'])) {
        return false;
    }

    $destinatarios = normalize_email_list((string) $cliente['correo']);
    if (empty($destinatarios)) {
        return false;
    }

    $municipalidad = get_municipalidad();
    $logoPath = $municipalidad['logo_path'] ?? 'assets/images/logo.png';
    $logoUrl = preg_match('/^https?:\/\//', $logoPath) ? $logoPath : base_url() . '/' . ltrim($logoPath, '/');
    $fromConfig = db()->query('SELECT * FROM notificacion_correos LIMIT 1')->fetch() ?: [];
    $fromEmail = $fromConfig['from_correo'] ?? $fromConfig['correo_imap'] ?? '';
    $fromName = $fromConfig['from_nombre'] ?? ($municipalidad['nombre'] ?? 'Notificaciones');

    $defaultSubject = 'Cotización {{codigo_cotizacion}} · {{municipalidad_nombre}}';
    $defaultBody = '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;"><table width="100%" cellpadding="0" cellspacing="0" style="padding:22px 12px;"><tr><td align="center"><table width="680" cellpadding="0" cellspacing="0" style="max-width:680px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;"><tr><td style="padding:20px 24px;background:#1d4ed8;color:#ffffff;"><div style="font-size:20px;font-weight:700;">Cotización de servicios</div><div style="font-size:13px;opacity:.95;">{{bajada_informativa}}</div></td></tr><tr><td style="padding:18px 24px;color:#111827;font-size:14px;line-height:1.6;"><p style="margin:0 0 10px 0;">Hola <strong>{{cliente_nombre}}</strong>, te compartimos el detalle de tu cotización <strong>{{codigo_cotizacion}}</strong>.</p><table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Datos cliente</div><div><strong>Contacto:</strong> {{cliente_contacto}}</div><div><strong>Correo:</strong> {{cliente_correo}}</div><div><strong>Dirección:</strong> {{cliente_direccion}}</div></td></tr></table><table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Detalle de la cotización</div>{{detalle_servicios}}<div style="margin-top:8px;"><strong>Total:</strong> {{total_cotizacion}}</div><div><strong>Válida por:</strong> {{validez_dias}} días (hasta {{fecha_validez}})</div></td></tr></table><table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Condiciones</div><div>{{nota_cotizacion}}</div></td></tr></table><p style="margin:12px 0 0 0;color:#4b5563;">Si tienes dudas, responde este correo y te ayudamos.</p></td></tr></table></td></tr></table></body></html>';

    try {
        db()->exec('CREATE TABLE IF NOT EXISTS email_templates (id INT UNSIGNED NOT NULL AUTO_INCREMENT, template_key VARCHAR(80) NOT NULL, subject VARCHAR(200) NOT NULL, body_html MEDIUMTEXT NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id), UNIQUE KEY email_templates_key_unique (template_key)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
        $stmtTpl = db()->prepare('SELECT subject, body_html FROM email_templates WHERE template_key = ? LIMIT 1');
        $stmtTpl->execute(['cotizacion_cliente']);
        $tpl = $stmtTpl->fetch() ?: ['subject' => $defaultSubject, 'body_html' => $defaultBody];
    } catch (Exception $e) {
        $tpl = ['subject' => $defaultSubject, 'body_html' => $defaultBody];
    } catch (Error $e) {
        $tpl = ['subject' => $defaultSubject, 'body_html' => $defaultBody];
    }

    $rows = '';
    $total = 0.0;
    foreach ($lineas as $linea) {
        $total += (float) ($linea['total'] ?? 0);
        $rows .= '<tr><td style="padding:6px;border-bottom:1px solid #e5e7eb;">' . htmlspecialchars((string) ($linea['servicio_nombre'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">' . htmlspecialchars((string) ($linea['tiempo_servicio'] ?? ''), ENT_QUOTES, 'UTF-8') . '</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">' . number_format((float) ($linea['descuento_porcentaje'] ?? 0), 0, ',', '.') . '%</td><td style="padding:6px;border-bottom:1px solid #e5e7eb;">$' . number_format((float) ($linea['total'] ?? 0), 2, ',', '.') . '</td></tr>';
    }

    $detalleServicios = '<table width="100%" cellpadding="0" cellspacing="0" style="font-size:13px;border-collapse:collapse;margin:12px 0;"><thead><tr><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Servicio</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Periodicidad</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Desc. %</th><th align="left" style="padding:6px;border-bottom:1px solid #d1d5db;">Total</th></tr></thead><tbody>' . $rows . '</tbody></table>';

    $repl = [
        '{{municipalidad_nombre}}' => htmlspecialchars($municipalidad['nombre'] ?? 'Municipalidad', ENT_QUOTES, 'UTF-8'),
        '{{municipalidad_logo}}' => htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8'),
        '{{cliente_nombre}}' => htmlspecialchars((string) ($cliente['nombre'] ?? 'Cliente'), ENT_QUOTES, 'UTF-8'),
        '{{codigo_cotizacion}}' => htmlspecialchars($codigoCotizacion, ENT_QUOTES, 'UTF-8'),
        '{{bajada_informativa}}' => 'Propuesta comercial informativa con condiciones y vigencia.',
        '{{detalle_servicios}}' => $detalleServicios,
        '{{nota_cotizacion}}' => htmlspecialchars($nota !== '' ? $nota : 'Valores sujetos a confirmación comercial.', ENT_QUOTES, 'UTF-8'),
        '{{total_cotizacion}}' => '$' . number_format($total, 2, ',', '.'),
        '{{validez_dias}}' => (string) $validezDias,
        '{{fecha_validez}}' => htmlspecialchars(date('d/m/Y', strtotime($fechaValidez)), ENT_QUOTES, 'UTF-8'),
        '{{cliente_contacto}}' => htmlspecialchars((string) ($cliente['telefono'] ?? '-'), ENT_QUOTES, 'UTF-8'),
        '{{cliente_correo}}' => htmlspecialchars((string) ($cliente['correo'] ?? '-'), ENT_QUOTES, 'UTF-8'),
        '{{cliente_direccion}}' => htmlspecialchars((string) ($cliente['direccion'] ?? '-'), ENT_QUOTES, 'UTF-8'),
    ];

    $subject = strtr((string) ($tpl['subject'] ?? $defaultSubject), $repl);
    $body = strtr((string) ($tpl['body_html'] ?? $defaultBody), $repl);

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
    ];
    if ($fromEmail !== '') {
        $headers[] = 'From: ' . ($fromName !== '' ? $fromName . ' <' . $fromEmail . '>' : $fromEmail);
        $headers[] = 'Reply-To: ' . $fromEmail;
    }

    $bccList = normalize_email_list($correoCco);
    if (!empty($bccList)) {
        $headers[] = 'Bcc: ' . implode(', ', $bccList);
    }

    $ok = true;
    foreach ($destinatarios as $correo) {
        $ok = @mail($correo, $subject, $body, implode("\r\n", $headers)) && $ok;
    }

    return $ok;
}

try {
    db()->exec(
        'CREATE TABLE IF NOT EXISTS clientes_servicios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cliente_id INT NOT NULL,
            servicio_id INT NOT NULL,
            motivo TEXT NULL,
            info_importante TEXT NULL,
            correo_enviado_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_cliente_servicio (cliente_id, servicio_id),
            INDEX idx_clientes_servicios_cliente (cliente_id),
            INDEX idx_clientes_servicios_servicio (servicio_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
    );
} catch (Exception $e) {
    $errorMessage = 'No se pudo preparar la tabla de asociaciones cliente-servicio.';
} catch (Error $e) {
    $errorMessage = 'No se pudo preparar la tabla de asociaciones cliente-servicio.';
}

try {
    ensure_column('clientes_servicios', 'fecha_registro', 'DATE NULL AFTER servicio_id');
    ensure_column('clientes_servicios', 'tiempo_servicio', 'VARCHAR(30) NULL AFTER fecha_registro');
    ensure_column('clientes_servicios', 'fecha_vencimiento', 'DATE NULL AFTER tiempo_servicio');
    ensure_column('clientes_servicios', 'codigo_cotizacion', 'VARCHAR(40) NULL AFTER servicio_id');
    ensure_column('clientes_servicios', 'enviar_correo', 'TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_vencimiento');
    ensure_column('clientes_servicios', 'nota_cotizacion', 'TEXT NULL AFTER enviar_correo');
    ensure_column('clientes_servicios', 'correo_cco', 'VARCHAR(255) NULL AFTER nota_cotizacion');
    ensure_column('clientes_servicios', 'subtotal', 'DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER nota_cotizacion');
    ensure_column('clientes_servicios', 'descuento_porcentaje', 'TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER subtotal');
    ensure_column('clientes_servicios', 'descuento_monto', 'DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_porcentaje');
    ensure_column('clientes_servicios', 'total', 'DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_monto');
    ensure_column('clientes_servicios', 'validez_dias', 'INT NOT NULL DEFAULT 5 AFTER total');
    ensure_column('clientes_servicios', 'fecha_validez', 'DATE NULL AFTER validez_dias');

    db()->exec('UPDATE clientes_servicios SET tiempo_servicio = "Mensual" WHERE tiempo_servicio IS NULL OR TRIM(tiempo_servicio) = ""');
    db()->exec('UPDATE clientes_servicios SET fecha_registro = DATE(created_at) WHERE fecha_registro IS NULL');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 1 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "mensual"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 2 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "bimestral"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 3 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "trimestral"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 6 MONTH) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "semestral"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = DATE_ADD(fecha_registro, INTERVAL 1 YEAR) WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND LOWER(tiempo_servicio) = "anual"');
    db()->exec('UPDATE clientes_servicios SET fecha_vencimiento = fecha_registro WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL AND (LOWER(tiempo_servicio) = "un pago" OR LOWER(REPLACE(tiempo_servicio, " ", "")) = "unpago")');
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la estructura de asociaciones.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudo actualizar la estructura de asociaciones.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf($_POST['csrf_token'] ?? null)) {
    $action = trim((string) ($_POST['action'] ?? ''));

    if ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $errors[] = 'No se pudo identificar la asociación a eliminar.';
        } else {
            try {
                $stmt = db()->prepare('DELETE FROM clientes_servicios WHERE id = ? LIMIT 1');
                $stmt->execute([$id]);
                redirect('clientes-servicios-asociar.php?success=deleted');
            } catch (Exception $e) {
                $errorMessage = 'No se pudo eliminar la asociación.';
            } catch (Error $e) {
                $errorMessage = 'No se pudo eliminar la asociación.';
            }
        }
    }

    if ($action === 'create_quote') {
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $fechaRegistro = trim((string) ($_POST['fecha_registro'] ?? ''));
        $enviarCorreo = isset($_POST['enviar_correo']) ? 1 : 0;
        $notaCotizacion = trim((string) ($_POST['nota_cotizacion'] ?? ''));
        $correoCco = trim((string) ($_POST['correo_cco'] ?? ''));
        $serviciosIds = $_POST['servicio_id'] ?? [];
        $periodicidades = $_POST['tiempo_servicio'] ?? [];
        $descuentos = $_POST['descuento'] ?? [];
        $validezDias = (int) ($_POST['validez_dias'] ?? 5);

        if ($clienteId <= 0) {
            $errors[] = 'Selecciona un cliente válido.';
        }
        if ($fechaRegistro === '') {
            $errors[] = 'Debes indicar la fecha de cotización.';
        }
        if (!is_array($serviciosIds) || count($serviciosIds) === 0) {
            $errors[] = 'Agrega al menos un servicio a la cotización.';
        }
        if (!in_array($validezDias, [1, 5, 10], true)) {
            $errors[] = 'Selecciona una validez de cotización válida.';
        }
        if ($correoCco !== '' && empty(normalize_email_list($correoCco))) {
            $errors[] = 'Ingresa correos CCO válidos separados por coma.';
        }

        $lineas = [];

        if (empty($errors)) {
            try {
                $stmtServicio = db()->prepare('SELECT id, nombre, monto FROM servicios WHERE id = ? LIMIT 1');

                foreach ($serviciosIds as $idx => $servicioRaw) {
                    $servicioId = (int) $servicioRaw;
                    $periodicidad = trim((string) ($periodicidades[$idx] ?? 'Mensual'));
                    $descuentoPorcentaje = (int) round((float) ($descuentos[$idx] ?? 0));

                    if ($servicioId <= 0) {
                        $errors[] = 'Hay un servicio inválido en la cotización.';
                        continue;
                    }

                    $stmtServicio->execute([$servicioId]);
                    $servicio = $stmtServicio->fetch() ?: null;
                    if (!$servicio) {
                        $errors[] = 'Uno de los servicios seleccionados no existe.';
                        continue;
                    }

                    $fechaVencimiento = calcular_fecha_vencimiento($fechaRegistro, $periodicidad);
                    if ($fechaVencimiento === null) {
                        $errors[] = 'Periodo inválido para el servicio ' . ($servicio['nombre'] ?? '#'.$servicioId) . '.';
                        continue;
                    }

                    $monto = (float) ($servicio['monto'] ?? 0);
                    if ($descuentoPorcentaje < 0) {
                        $descuentoPorcentaje = 0;
                    }
                    if ($descuentoPorcentaje > 100) {
                        $descuentoPorcentaje = 100;
                    }
                    $descuentoLinea = round(($monto * $descuentoPorcentaje) / 100, 2);
                    $totalLinea = $monto - $descuentoLinea;

                    $lineas[] = [
                        'servicio_id' => $servicioId,
                        'servicio_nombre' => (string) ($servicio['nombre'] ?? 'Servicio'),
                        'fecha_vencimiento' => $fechaVencimiento,
                        'tiempo_servicio' => $periodicidad,
                        'subtotal' => $monto,
                        'descuento_porcentaje' => $descuentoPorcentaje,
                        'descuento_monto' => $descuentoLinea,
                        'total' => $totalLinea,
                    ];
                }
            } catch (Exception $e) {
                $errors[] = 'No se pudieron validar los servicios seleccionados.';
            } catch (Error $e) {
                $errors[] = 'No se pudieron validar los servicios seleccionados.';
            }
        }

        if (empty($errors) && !empty($lineas)) {
            try {
                $fechaValidez = date('Y-m-d', strtotime($fechaRegistro . ' +' . $validezDias . ' days'));
                $codigoCotizacion = 'COT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                db()->beginTransaction();
                $stmtInsert = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id, codigo_cotizacion, fecha_registro, tiempo_servicio, fecha_vencimiento, enviar_correo, nota_cotizacion, correo_cco, subtotal, descuento_porcentaje, descuento_monto, total, validez_dias, fecha_validez) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE codigo_cotizacion = VALUES(codigo_cotizacion), fecha_registro = VALUES(fecha_registro), tiempo_servicio = VALUES(tiempo_servicio), fecha_vencimiento = VALUES(fecha_vencimiento), enviar_correo = VALUES(enviar_correo), nota_cotizacion = VALUES(nota_cotizacion), correo_cco = VALUES(correo_cco), subtotal = VALUES(subtotal), descuento_porcentaje = VALUES(descuento_porcentaje), descuento_monto = VALUES(descuento_monto), total = VALUES(total), validez_dias = VALUES(validez_dias), fecha_validez = VALUES(fecha_validez)');
                foreach ($lineas as $linea) {
                    $stmtInsert->execute([
                        $clienteId,
                        $linea['servicio_id'],
                        $codigoCotizacion,
                        $fechaRegistro,
                        $linea['tiempo_servicio'],
                        $linea['fecha_vencimiento'],
                        $enviarCorreo,
                        $notaCotizacion !== '' ? $notaCotizacion : null,
                        $correoCco !== '' ? $correoCco : null,
                        $linea['subtotal'],
                        $linea['descuento_porcentaje'],
                        $linea['descuento_monto'],
                        $linea['total'],
                        $validezDias,
                        $fechaValidez,
                    ]);
                }
                db()->commit();

                if ($enviarCorreo === 1) {
                    try {
                        $stmtClienteCorreo = db()->prepare('SELECT nombre, correo, telefono, direccion FROM clientes WHERE id = ? LIMIT 1');
                        $stmtClienteCorreo->execute([$clienteId]);
                        $clienteCorreo = $stmtClienteCorreo->fetch() ?: [];
                        enviar_correo_cotizacion($clienteCorreo, $lineas, $codigoCotizacion, $notaCotizacion, $validezDias, $fechaValidez, $correoCco);
                    } catch (Exception $e) {
                    } catch (Error $e) {
                    }
                }

                redirect('clientes-servicios-asociar.php?success=quote');
            } catch (Exception $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errorMessage = 'No se pudo guardar la cotización.';
            } catch (Error $e) {
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $errorMessage = 'No se pudo guardar la cotización.';
            }
        }
    }

    if ($action === 'create' || $action === 'update') {
        $asociacionId = (int) ($_POST['id'] ?? 0);
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $servicioId = (int) ($_POST['servicio_id'] ?? 0);
        $fechaRegistro = trim((string) ($_POST['fecha_registro'] ?? ''));
        $tiempoServicio = trim((string) ($_POST['tiempo_servicio'] ?? 'Mensual'));

        if ($action === 'update' && $asociacionId <= 0) {
            $errors[] = 'No se pudo identificar la asociación a editar.';
        }

        if ($clienteId <= 0) {
            $errors[] = 'Selecciona un cliente válido.';
        }
        if ($servicioId <= 0) {
            $errors[] = 'Selecciona un servicio válido.';
        }
        if ($fechaRegistro === '') {
            $errors[] = 'Debes indicar la fecha de registro.';
        }

        $fechaVencimiento = calcular_fecha_vencimiento($fechaRegistro, $tiempoServicio);
        if ($fechaVencimiento === null) {
            $errors[] = 'Selecciona un tiempo de servicio válido para calcular el vencimiento.';
        }

        if (empty($errors)) {
            try {
                if ($action === 'update') {
                    $stmtExists = db()->prepare('SELECT COUNT(*) FROM clientes_servicios WHERE cliente_id = ? AND servicio_id = ? AND id <> ?');
                    $stmtExists->execute([$clienteId, $servicioId, $asociacionId]);
                } else {
                    $stmtExists = db()->prepare('SELECT COUNT(*) FROM clientes_servicios WHERE cliente_id = ? AND servicio_id = ?');
                    $stmtExists->execute([$clienteId, $servicioId]);
                }
                if ((int) $stmtExists->fetchColumn() > 0) {
                    $errors[] = 'Esta asociación ya existe.';
                } else {
                    if ($action === 'update') {
                        $stmtUpdate = db()->prepare('UPDATE clientes_servicios SET cliente_id = ?, servicio_id = ?, fecha_registro = ?, tiempo_servicio = ?, fecha_vencimiento = ? WHERE id = ? LIMIT 1');
                        $stmtUpdate->execute([$clienteId, $servicioId, $fechaRegistro, $tiempoServicio, $fechaVencimiento, $asociacionId]);
                        redirect('clientes-servicios-asociar.php?success=updated');
                    } else {
                        $stmtInsert = db()->prepare('INSERT INTO clientes_servicios (cliente_id, servicio_id, fecha_registro, tiempo_servicio, fecha_vencimiento) VALUES (?, ?, ?, ?, ?)');
                        $stmtInsert->execute([$clienteId, $servicioId, $fechaRegistro, $tiempoServicio, $fechaVencimiento]);
                        redirect('clientes-servicios-asociar.php?success=1');
                    }
                }
            } catch (Exception $e) {
                $errorMessage = 'No se pudo guardar la asociación.';
            } catch (Error $e) {
                $errorMessage = 'No se pudo guardar la asociación.';
            }
        }
    }
}

if (isset($_GET['id'])) {
    $editId = (int) $_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = db()->prepare('SELECT id, cliente_id, servicio_id, fecha_registro, tiempo_servicio FROM clientes_servicios WHERE id = ? LIMIT 1');
            $stmt->execute([$editId]);
            $asociacionEdit = $stmt->fetch() ?: null;
            if (!$asociacionEdit) {
                $errorMessage = 'No se encontró la asociación a editar.';
            }
        } catch (Exception $e) {
            $errorMessage = 'No se pudo cargar la asociación para edición.';
        } catch (Error $e) {
            $errorMessage = 'No se pudo cargar la asociación para edición.';
        }
    }
}

$clientes = [];
$servicios = [];
$asociaciones = [];
try {
    $clientes = db()->query('SELECT id, codigo, nombre, correo, telefono, direccion FROM clientes WHERE estado = 1 ORDER BY nombre')->fetchAll();
    $servicios = db()->query(
        'SELECT s.id,
                s.nombre,
                s.descripcion,
                s.monto,
                s.link_boton_pago,
                s.estado,
                ts.nombre AS tipo_servicio
         FROM servicios s
         LEFT JOIN tipos_servicios ts ON ts.id = s.tipo_servicio_id
         ORDER BY s.nombre'
    )->fetchAll();
    $sqlAsociaciones = 'SELECT cs.id,
                               c.codigo AS cliente_codigo,
                               c.nombre AS cliente,
                               s.nombre AS servicio,
                               cs.codigo_cotizacion,
                               cs.fecha_registro,
                               cs.tiempo_servicio,
                               cs.fecha_vencimiento,
                               cs.subtotal,
                               cs.descuento_porcentaje,
                               cs.descuento_monto,
                               cs.total,
                               cs.validez_dias,
                               cs.fecha_validez,
                               cs.enviar_correo,
                               cs.nota_cotizacion,
                               cs.correo_cco
                        FROM clientes_servicios cs
                        JOIN clientes c ON c.id = cs.cliente_id
                        JOIN servicios s ON s.id = cs.servicio_id';

    if ($filtroTexto !== '') {
        $sqlAsociaciones .= ' WHERE c.nombre LIKE :q OR c.codigo LIKE :q OR s.nombre LIKE :q';
    }
    $sqlAsociaciones .= ' ORDER BY cs.id DESC';

    $stmtAsociaciones = db()->prepare($sqlAsociaciones);
    if ($filtroTexto !== '') {
        $stmtAsociaciones->execute(['q' => '%' . $filtroTexto . '%']);
    } else {
        $stmtAsociaciones->execute();
    }
    $asociaciones = $stmtAsociaciones->fetchAll();
} catch (Exception $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los datos.';
} catch (Error $e) {
    $errorMessage = $errorMessage !== '' ? $errorMessage : 'No se pudieron cargar los datos.';
}
?>
<?php include('partials/html.php'); ?>
<head>
    <?php $title = "Cotización"; include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>
<body>
<div class="wrapper">
    <?php include('partials/menu.php'); ?>
    <div class="content-page">
        <div class="container-fluid">
            <?php $subtitle = "Clientes"; $title = "Cotización"; include('partials/page-title.php'); ?>

                <?php $flowCurrentStep = 'asociaciones'; include('partials/flow-quick-nav.php'); ?>

            <?php if ($success === '1') : ?><div class="alert alert-success">Asociación creada correctamente.</div><?php endif; ?>
            <?php if ($success === 'deleted') : ?><div class="alert alert-success">Asociación eliminada correctamente.</div><?php endif; ?>
            <?php if ($success === 'updated') : ?><div class="alert alert-success">Asociación actualizada correctamente.</div><?php endif; ?>
            <?php if ($success === 'quote') : ?><div class="alert alert-success">Cotización guardada con servicios asociados.</div><?php endif; ?>
            <?php if ($errorMessage !== '') : ?><div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?></div><?php endif; ?>
            <?php if (!empty($errors)) : ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error) : ?>
                        <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-header"><h5 class="mb-0">Nueva cotización de servicios</h5></div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="action" value="create_quote">
                        <div class="col-md-6">
                            <label class="form-label">Cliente</label>
                            <select id="cliente-id" name="cliente_id" class="form-select" required>
                                <option value="">Selecciona un cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo (int) $cliente['id']; ?>" <?php echo ((int) ($_POST['cliente_id'] ?? ($asociacionEdit['cliente_id'] ?? 0)) === (int) $cliente['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars(($cliente['codigo'] ?? '') . ' - ' . $cliente['nombre'], ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="fecha-registro">Fecha de cotización</label>
                            <input type="date" id="fecha-registro" name="fecha_registro" class="form-control" value="<?php echo htmlspecialchars($_POST['fecha_registro'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="validez-dias">Validez cotización</label>
                            <select id="validez-dias" name="validez_dias" class="form-select">
                                <?php $validezActual = (int) ($_POST['validez_dias'] ?? 5); ?>
                                <option value="1" <?php echo $validezActual === 1 ? 'selected' : ''; ?>>24 horas</option>
                                <option value="5" <?php echo $validezActual === 5 ? 'selected' : ''; ?>>5 días</option>
                                <option value="10" <?php echo $validezActual === 10 ? 'selected' : ''; ?>>10 días</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="enviar-correo" name="enviar_correo" value="1" <?php echo isset($_POST['enviar_correo']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="enviar-correo">Enviar correo al cliente con servicios asociados</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="correo-cco">Correo CCO</label>
                            <input type="text" id="correo-cco" name="correo_cco" class="form-control" placeholder="copia1@dominio.cl, copia2@dominio.cl" value="<?php echo htmlspecialchars($_POST['correo_cco'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="form-text">Opcional. Se enviará copia oculta al enviar la cotización.</div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-2">Datos del cliente</h6>
                                <div class="small text-muted"><strong>Nombre:</strong> <span id="cliente-detalle-nombre">-</span></div>
                                <div class="small text-muted"><strong>Correo:</strong> <span id="cliente-detalle-correo">-</span></div>
                                <div class="small text-muted"><strong>Teléfono:</strong> <span id="cliente-detalle-telefono">-</span></div>
                                <div class="small text-muted"><strong>Dirección:</strong> <span id="cliente-detalle-direccion">-</span></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Servicios cotizados</label>
                            <div class="table-responsive border rounded">
                                <table class="table table-sm align-middle mb-0" id="tabla-cotizacion">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Servicio</th>
                                            <th>Periodicidad</th>
                                            <th>Precio</th>
                                            <th>Descuento %</th>
                                            <th>Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cotizacion-body"></tbody>
                                </table>
                            </div>
                            <button type="button" id="agregar-linea" class="btn btn-outline-primary btn-sm mt-2">Agregar servicio</button>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="nota-cotizacion">Nota</label>
                            <textarea id="nota-cotizacion" name="nota_cotizacion" class="form-control" rows="3" placeholder="Condiciones, observaciones o términos de la cotización..."><?php echo htmlspecialchars($_POST['nota_cotizacion'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <strong>Total cotización:</strong> <span id="total-cotizacion">$0,00</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Guardar cotización</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Asociaciones registradas</h5>
                    <span class="badge text-bg-primary"><?php echo count($asociaciones); ?> registros</span>
                </div>
                <div class="card-body border-bottom">
                    <form method="get" class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="q" class="form-control" placeholder="Buscar por cliente, código o servicio" value="<?php echo htmlspecialchars($filtroTexto, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                            <a href="clientes-servicios-asociar.php" class="btn btn-outline-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Cotización</th><th>Cliente</th><th>Servicio</th><th>Tiempo</th><th>Subtotal</th><th>Descuento %</th><th>Desc. $</th><th>Total</th><th>Validez</th><th class="text-end">Acción</th></tr></thead>
                        <tbody>
                        <?php if (empty($asociaciones)) : ?>
                            <tr><td colspan="10" class="text-center text-muted">No hay asociaciones registradas.</td></tr>
                        <?php else : foreach ($asociaciones as $item) : ?>
                            <tr>
                                <td>
                                    <div><?php echo htmlspecialchars($item['codigo_cotizacion'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small class="text-muted"><?php echo !empty($item['fecha_registro']) ? htmlspecialchars(date('d/m/Y', strtotime((string) $item['fecha_registro'])), ENT_QUOTES, 'UTF-8') : '-'; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars(($item['cliente_codigo'] ?? '') . ' - ' . $item['cliente'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['servicio'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($item['tiempo_servicio'] ?: '-', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format((float) ($item['subtotal'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars(number_format((float) ($item['descuento_porcentaje'] ?? 0), 0, ',', '.'), ENT_QUOTES, 'UTF-8'); ?>%</td>
                                <td>$<?php echo htmlspecialchars(number_format((float) ($item['descuento_monto'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div>$<?php echo htmlspecialchars(number_format((float) ($item['total'] ?? 0), 2, ',', '.'), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if ((int) ($item['enviar_correo'] ?? 0) === 1) : ?><small class="text-success">Correo marcado</small><?php endif; ?>
                                    <?php if (!empty($item['correo_cco'])) : ?><br><small class="text-muted">CCO: <?php echo htmlspecialchars((string) $item['correo_cco'], ENT_QUOTES, 'UTF-8'); ?></small><?php endif; ?>
                                </td>
                                <td><?php echo (int) ($item['validez_dias'] ?? 0); ?> días<br><small class="text-muted"><?php echo !empty($item['fecha_validez']) ? htmlspecialchars(date('d/m/Y', strtotime((string) $item['fecha_validez'])), ENT_QUOTES, 'UTF-8') : '-'; ?></small></td>
                                <td class="text-end">
                                    <div class="dropdown d-inline-block">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Acciones
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="clientes-servicios-asociar.php?id=<?php echo (int) $item['id']; ?>">Editar</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="post" onsubmit="return confirm('¿Eliminar esta asociación?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                                                    <button type="submit" class="dropdown-item text-danger">Eliminar</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <?php include('partials/footer.php'); ?>
    </div>
</div>
<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>
<script>
(function () {
    const servicios = <?php echo json_encode(array_map(static function ($servicio) {
        return [
            'id' => (int) ($servicio['id'] ?? 0),
            'nombre' => (string) ($servicio['nombre'] ?? 'Servicio'),
            'monto' => (float) ($servicio['monto'] ?? 0),
        ];
    }, $servicios), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    const clientes = <?php echo json_encode(array_map(static function ($cliente) {
        return [
            'id' => (int) ($cliente['id'] ?? 0),
            'nombre' => (string) ($cliente['nombre'] ?? ''),
            'correo' => (string) ($cliente['correo'] ?? '-'),
            'telefono' => (string) ($cliente['telefono'] ?? '-'),
            'direccion' => (string) ($cliente['direccion'] ?? '-'),
        ];
    }, $clientes), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    const body = document.getElementById('cotizacion-body');
    const btnAgregar = document.getElementById('agregar-linea');
    const totalCotizacion = document.getElementById('total-cotizacion');
    const clienteSelect = document.getElementById('cliente-id');

    const detalleCliente = {
        nombre: document.getElementById('cliente-detalle-nombre'),
        correo: document.getElementById('cliente-detalle-correo'),
        telefono: document.getElementById('cliente-detalle-telefono'),
        direccion: document.getElementById('cliente-detalle-direccion'),
    };

    if (!body || !btnAgregar || !totalCotizacion) {
        return;
    }


    function syncClienteDetalle() {
        if (!clienteSelect) {
            return;
        }
        const id = Number(clienteSelect.value || 0);
        const cliente = clientes.find((item) => Number(item.id) === id);
        detalleCliente.nombre.textContent = cliente?.nombre || '-';
        detalleCliente.correo.textContent = cliente?.correo || '-';
        detalleCliente.telefono.textContent = cliente?.telefono || '-';
        detalleCliente.direccion.textContent = cliente?.direccion || '-';
    }

    function formatoMoneda(valor) {
        return new Intl.NumberFormat('es-CL', { style: 'currency', currency: 'CLP', maximumFractionDigits: 2 }).format(Number(valor || 0));
    }

    function construirOpcionesServicio() {
        let html = '<option value="">Selecciona un servicio</option>';
        servicios.forEach((servicio) => {
            html += `<option value="${servicio.id}" data-monto="${servicio.monto}">${servicio.nombre} · ${formatoMoneda(servicio.monto)}</option>`;
        });
        return html;
    }

    function recalcularFila(row) {
        const selectServicio = row.querySelector('.js-servicio');
        const inputDescuento = row.querySelector('.js-descuento');
        const totalEl = row.querySelector('.js-total-linea');
        const precioEl = row.querySelector('.js-precio');

        const option = selectServicio.options[selectServicio.selectedIndex];
        const monto = Number(option?.dataset?.monto || 0);
        let descuento = Number(inputDescuento.value || 0);

        if (descuento < 0) descuento = 0;
        if (descuento > 100) descuento = 100;
        descuento = Math.round(descuento);
        inputDescuento.value = descuento;

        const descuentoMonto = (monto * descuento) / 100;
        const total = monto - descuentoMonto;
        precioEl.textContent = formatoMoneda(monto);
        totalEl.textContent = formatoMoneda(total);
    }

    function recalcularTotales() {
        let total = 0;
        body.querySelectorAll('tr').forEach((row) => {
            const selectServicio = row.querySelector('.js-servicio');
            const inputDescuento = row.querySelector('.js-descuento');
            const option = selectServicio.options[selectServicio.selectedIndex];
            const monto = Number(option?.dataset?.monto || 0);
            const descuento = Number(inputDescuento.value || 0);
            const descuentoMonto = (monto * descuento) / 100;
            total += Math.max(0, monto - descuentoMonto);
        });
        totalCotizacion.textContent = formatoMoneda(total);
    }

    function agregarLinea() {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="servicio_id[]" class="form-select form-select-sm js-servicio" required>
                    ${construirOpcionesServicio()}
                </select>
            </td>
            <td>
                <select name="tiempo_servicio[]" class="form-select form-select-sm" required>
                    <option value="Mensual">Mensual</option>
                    <option value="Bimestral">Bimestral</option>
                    <option value="Trimestral">Trimestral</option>
                    <option value="Semestral">Semestral</option>
                    <option value="Anual">Anual</option>
                    <option value="Un pago">Un pago</option>
                </select>
            </td>
            <td class="js-precio">$0</td>
            <td><div class="input-group input-group-sm"><input type="number" name="descuento[]" class="form-control js-descuento" min="0" max="100" step="1" value="0"><span class="input-group-text">%</span></div></td>
            <td class="js-total-linea">$0</td>
            <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger js-eliminar">Quitar</button></td>
        `;

        const selectServicio = row.querySelector('.js-servicio');
        const inputDescuento = row.querySelector('.js-descuento');
        const btnEliminar = row.querySelector('.js-eliminar');

        const sync = () => {
            recalcularFila(row);
            recalcularTotales();
        };

        selectServicio.addEventListener('change', sync);
        inputDescuento.addEventListener('input', sync);
        btnEliminar.addEventListener('click', () => {
            row.remove();
            recalcularTotales();
        });

        body.appendChild(row);
        sync();
    }

    btnAgregar.addEventListener('click', agregarLinea);
    if (clienteSelect) {
        clienteSelect.addEventListener('change', syncClienteDetalle);
        syncClienteDetalle();
    }
    agregarLinea();
})();
</script>
</body>
</html>
