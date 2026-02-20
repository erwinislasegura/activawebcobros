-- Actualización: módulo de suspensión de servicios por no pago
-- Fecha: 2026-03-27

CREATE TABLE IF NOT EXISTS `clientes_servicios_suspensiones` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cliente_servicio_id` INT NOT NULL,
  `cobro_id` INT DEFAULT NULL,
  `motivo` TEXT NOT NULL,
  `detalle` TEXT DEFAULT NULL,
  `correo_destinatario` VARCHAR(180) DEFAULT NULL,
  `correo_enviado_at` DATETIME DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_css_cliente_servicio` (`cliente_servicio_id`),
  KEY `idx_css_cobro` (`cobro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(80) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `body_html` MEDIUMTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_key_unique` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `email_templates` (`template_key`, `subject`, `body_html`)
VALUES (
  'suspension_servicio_urgente',
  'URGENTE: Suspensión de servicio {{servicio_nombre}}',
  '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Suspensión urgente</title></head><body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;"><table width="100%" cellpadding="0" cellspacing="0" bgcolor="#f4f6fb"><tr><td align="center" style="padding:24px 12px;"><table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#fff;border:1px solid #e6ebf2;border-radius:12px;overflow:hidden;"><tr><td style="padding:14px 18px;background:#7f1d1d;color:#fff;font-weight:700;">{{municipalidad_nombre}} · Aviso urgente</td></tr><tr><td style="height:4px;background:#ef4444;line-height:4px;font-size:0;">&nbsp;</td></tr><tr><td style="padding:22px;color:#1f2937;font-size:14px;line-height:1.65;"><p>Estimado/a <strong>{{cliente_nombre}}</strong>,</p><p>Informamos la <strong style="color:#b91c1c;">SUSPENSIÓN INMEDIATA</strong> de su servicio <strong>{{servicio_nombre}}</strong> por no pago.</p><p><strong>Motivo:</strong> {{motivo_suspension}}<br><strong>Detalle:</strong> {{detalle_suspension}}<br><strong>Monto pendiente:</strong> {{monto_pendiente}}</p><p>Esta situación puede dejar sin funcionamiento su <strong>sitio web y correos corporativos</strong>, afectando su continuidad operacional, su <strong>seriedad comercial</strong> y su <strong>posicionamiento en internet</strong>.</p><p>Le solicitamos regularizar con urgencia.</p><p>Atentamente,<br><strong>{{municipalidad_nombre}}</strong></p></td></tr></table></td></tr></table></body></html>'
)
ON DUPLICATE KEY UPDATE
  subject = VALUES(subject),
  body_html = VALUES(body_html);
