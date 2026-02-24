-- Actualización módulo cotización cliente-servicio
ALTER TABLE clientes_servicios
    ADD COLUMN IF NOT EXISTS codigo_cotizacion VARCHAR(40) NULL AFTER servicio_id,
    ADD COLUMN IF NOT EXISTS fecha_registro DATE NULL AFTER codigo_cotizacion,
    ADD COLUMN IF NOT EXISTS tiempo_servicio VARCHAR(30) NULL AFTER fecha_registro,
    ADD COLUMN IF NOT EXISTS fecha_vencimiento DATE NULL AFTER tiempo_servicio,
    ADD COLUMN IF NOT EXISTS enviar_correo TINYINT(1) NOT NULL DEFAULT 0 AFTER fecha_vencimiento,
    ADD COLUMN IF NOT EXISTS nota_cotizacion TEXT NULL AFTER enviar_correo,
    ADD COLUMN IF NOT EXISTS subtotal DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER nota_cotizacion,
    ADD COLUMN IF NOT EXISTS descuento_porcentaje DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER subtotal,
    ADD COLUMN IF NOT EXISTS descuento_monto DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_porcentaje,
    ADD COLUMN IF NOT EXISTS total DECIMAL(10,2) NOT NULL DEFAULT 0 AFTER descuento_monto;

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
    'Cotización {{codigo_cotizacion}} - {{municipalidad_nombre}}',
    '<!DOCTYPE html><html><body style="font-family:Arial,sans-serif;background:#f8fafc;padding:16px;"><table width="100%" cellpadding="0" cellspacing="0" style="max-width:640px;margin:0 auto;background:#fff;border:1px solid #e5e7eb;border-radius:12px;"><tr><td style="padding:18px 22px;background:#1D4ED8;color:#fff;"><strong>{{municipalidad_nombre}}</strong></td></tr><tr><td style="padding:18px 22px;color:#111827;"><p>Estimado/a <strong>{{cliente_nombre}}</strong>,</p><p>Te compartimos los servicios asociados en la cotización <strong>{{codigo_cotizacion}}</strong>.</p>{{detalle_servicios}}<p><strong>Total:</strong> {{total_cotizacion}}</p><p>{{nota_cotizacion}}</p></td></tr></table></body></html>'
)
ON DUPLICATE KEY UPDATE subject = VALUES(subject), body_html = VALUES(body_html);
