-- Migración: módulo de notificación de eliminación de servicios suspendidos
-- Fecha: 2026-02-27
-- Objetivo:
--   1) Crear tabla de plantillas de correo (si no existe).
--   2) Crear tabla de log de notificaciones de eliminación (si no existe).
--   3) Sembrar plantilla base "eliminacion_servicio_suspendido" (si no existe).

START TRANSACTION;

CREATE TABLE IF NOT EXISTS email_templates (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    template_key VARCHAR(80) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    body_html MEDIUMTEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email_templates_key_unique (template_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS clientes_servicios_eliminaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suspension_id INT NOT NULL,
    cliente_servicio_id INT NOT NULL,
    cobro_id INT DEFAULT NULL,
    correo_destinatario VARCHAR(180) NULL,
    correo_enviado_at DATETIME NULL,
    created_by INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_css_eliminacion (suspension_id),
    INDEX idx_cse_cliente_servicio (cliente_servicio_id),
    INDEX idx_cse_cobro (cobro_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO email_templates (template_key, subject, body_html)
SELECT
    'eliminacion_servicio_suspendido',
    'Aviso de eliminación definitiva: {{servicio_nombre}}',
    '<!DOCTYPE html>\n<html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Eliminación de servicio</title></head>\n<body style="margin:0;padding:0;background:#f8fafc;font-family:Arial,Helvetica,sans-serif;">\n<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f8fafc"><tr><td align="center" style="padding:24px 12px;">\n<table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;">\n<tr><td style="padding:0;background:#111827;"><table width="100%" cellpadding="0" cellspacing="0"><tr><td style="padding:16px 20px;color:#fff;font-weight:700;">{{municipalidad_nombre}}</td><td align="right" style="padding:16px 20px;color:#e5e7eb;font-size:12px;white-space:nowrap;">Aviso importante</td></tr></table></td></tr>\n<tr><td style="height:4px;background:#EF4444;line-height:4px;font-size:0;">&nbsp;</td></tr>\n<tr><td style="padding:24px;color:#1f2937;font-size:14px;line-height:1.65;">\n<p style="margin:0 0 12px 0;">Estimado/a <strong>{{cliente_nombre}}</strong>,</p>\n<p style="margin:0 0 14px 0;color:#374151;">Le informamos que el servicio <strong>{{servicio_nombre}}</strong>, actualmente en estado de suspensión, será <strong style="color:#b91c1c;">eliminado de forma definitiva</strong>.</p>\n<table width="100%" cellpadding="0" cellspacing="0" style="margin:14px 0 18px 0;background:#fff5f5;border:1px solid #fecaca;border-radius:12px;"><tr><td style="padding:14px;">\n<div style="margin-bottom:6px;"><strong>Motivo de suspensión:</strong> {{motivo_suspension}}</div>\n<div style="margin-bottom:6px;"><strong>Detalle:</strong> {{detalle_suspension}}</div>\n<div><strong>Fecha de eliminación informada:</strong> {{fecha_notificacion}}</div>\n</td></tr></table>\n<p style="margin:0 0 12px 0;color:#4B5563;">Si requiere revisar antecedentes o regularizar su situación, favor contactar al equipo de soporte y cobranzas a la brevedad.</p>\n<p style="margin:0;">Atentamente,<br><strong>Departamento de Soporte y Servicios Digitales</strong><br>{{municipalidad_nombre}}</p>\n</td></tr></table>\n</td></tr></table>\n</body></html>'
WHERE NOT EXISTS (
    SELECT 1
    FROM email_templates
    WHERE template_key = 'eliminacion_servicio_suspendido'
);

COMMIT;
