SET @db_name := DATABASE();

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'codigo_cotizacion') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN codigo_cotizacion VARCHAR(40) NULL AFTER servicio_id',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'fecha_registro') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN fecha_registro DATE NULL AFTER codigo_cotizacion',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'tiempo_servicio') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN tiempo_servicio VARCHAR(30) NULL AFTER fecha_registro',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'fecha_vencimiento') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN fecha_vencimiento DATE NULL AFTER tiempo_servicio',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'enviar_correo') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN enviar_correo TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_vencimiento',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'nota_cotizacion') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN nota_cotizacion TEXT NULL AFTER enviar_correo',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'subtotal') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN subtotal DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER nota_cotizacion',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'descuento_porcentaje') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN descuento_porcentaje TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER subtotal',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

ALTER TABLE clientes_servicios
    MODIFY COLUMN descuento_porcentaje TINYINT UNSIGNED NOT NULL DEFAULT 0;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'descuento_monto') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN descuento_monto DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_porcentaje',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'total') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN total DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_monto',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'validez_dias') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN validez_dias INT NOT NULL DEFAULT 5 AFTER total',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := IF(
    (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db_name AND TABLE_NAME = 'clientes_servicios' AND COLUMN_NAME = 'fecha_validez') = 0,
    'ALTER TABLE clientes_servicios ADD COLUMN fecha_validez DATE NULL AFTER validez_dias',
    'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

CREATE TABLE IF NOT EXISTS email_templates (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    template_key VARCHAR(80) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    body_html MEDIUMTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email_templates_key_unique (template_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO email_templates (template_key, subject, body_html)
VALUES (
    'cotizacion_cliente',
    'Cotización {{codigo_cotizacion}} · {{municipalidad_nombre}}',
    '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f3f6fb;font-family:Arial,Helvetica,sans-serif;"><table width="100%" cellpadding="0" cellspacing="0" style="padding:22px 12px;"><tr><td align="center"><table width="680" cellpadding="0" cellspacing="0" style="max-width:680px;background:#ffffff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;"><tr><td style="padding:20px 24px;background:#1d4ed8;color:#ffffff;"><div style="font-size:20px;font-weight:700;">Cotización de servicios</div><div style="font-size:13px;opacity:.95;">{{bajada_informativa}}</div></td></tr><tr><td style="padding:18px 24px;color:#111827;font-size:14px;line-height:1.6;"><p style="margin:0 0 10px 0;">Hola <strong>{{cliente_nombre}}</strong>, te compartimos el detalle de tu cotización <strong>{{codigo_cotizacion}}</strong>.</p><table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Datos cliente</div><div><strong>Contacto:</strong> {{cliente_contacto}}</div><div><strong>Correo:</strong> {{cliente_correo}}</div><div><strong>Dirección:</strong> {{cliente_direccion}}</div></td></tr></table><table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Detalle de la cotización</div>{{detalle_servicios}}<div style="margin-top:8px;"><strong>Total:</strong> {{total_cotizacion}}</div><div><strong>Válida por:</strong> {{validez_dias}} días (hasta {{fecha_validez}})</div></td></tr></table><table width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;margin:12px 0;"><tr><td style="padding:12px 14px;"><div style="font-weight:700;margin-bottom:6px;">Condiciones</div><div>{{nota_cotizacion}}</div></td></tr></table><p style="margin:12px 0 0 0;color:#4b5563;">Si tienes dudas, responde este correo y te ayudamos.</p></td></tr></table></td></tr></table></body></html>'
)
ON DUPLICATE KEY UPDATE subject = VALUES(subject), body_html = VALUES(body_html);
