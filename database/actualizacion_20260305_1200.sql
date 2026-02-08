-- Actualización: plantillas de correo e invitaciones a autoridades
-- Fecha: 2026-03-05

CREATE TABLE IF NOT EXISTS `email_templates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(120) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `body_html` MEDIUMTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_key_unique` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `event_authority_invitations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `destinatario_correo` VARCHAR(150) DEFAULT NULL,
  `correo_enviado` TINYINT(1) NOT NULL DEFAULT 0,
  `sent_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_authority_invitations_unique` (`event_id`, `authority_id`),
  CONSTRAINT `event_authority_invitations_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authority_invitations_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `event_authority_attendance` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` INT UNSIGNED NOT NULL,
  `authority_id` INT UNSIGNED NOT NULL,
  `token` VARCHAR(64) NOT NULL,
  `status` ENUM('pendiente', 'confirmado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  `responded_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_authority_attendance_unique` (`event_id`, `authority_id`),
  UNIQUE KEY `event_authority_attendance_token_unique` (`token`),
  CONSTRAINT `event_authority_attendance_event_id_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `event_authority_attendance_authority_id_fk` FOREIGN KEY (`authority_id`) REFERENCES `authorities` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `email_templates` (`template_key`, `subject`, `body_html`)
SELECT 'invitacion_autoridades', 'Invitación al evento: {{evento_titulo}}', '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Invitación institucional</title></head><body style="margin:0;padding:0;background-color:#eef2f7;font-family:Arial,sans-serif;color:#1f2937;"><table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 0;background-color:#eef2f7;"><tr><td align="center"><table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:18px;overflow:hidden;box-shadow:0 12px 28px rgba(15,23,42,0.08);"><tr><td style="background:linear-gradient(120deg,#0f4c81,#163a6b);padding:24px 32px;color:#ffffff;"><img src="{{municipalidad_logo}}" alt="Logo" style="height:28px;vertical-align:middle;"><span style="font-size:18px;font-weight:bold;margin-left:12px;vertical-align:middle;">{{municipalidad_nombre}}</span></td></tr><tr><td style="padding:32px;"><p style="margin:0 0 12px;font-size:16px;">Estimado(a) {{destinatario_nombre}},</p><p style="margin:0 0 16px;font-size:14px;line-height:1.6;">Junto con saludar, la Municipalidad de {{municipalidad_nombre}} le extiende una cordial invitación para participar en el evento institucional <strong>{{evento_titulo}}</strong>.</p><div style="background-color:#f8fafc;border-radius:14px;padding:16px 20px;margin-bottom:18px;"><p style="margin:0 0 6px;font-size:12px;color:#64748b;">Detalles del evento</p><p style="margin:0;font-size:15px;font-weight:bold;color:#0f172a;">{{evento_titulo}}</p><p style="margin:6px 0 0;font-size:13px;color:#475569;">{{evento_ubicacion}} · {{evento_tipo}}</p><p style="margin:6px 0 0;font-size:13px;color:#475569;">{{evento_fecha_inicio}} - {{evento_fecha_fin}}</p><p style="margin:10px 0 0;font-size:13px;color:#475569;">{{evento_descripcion}}</p></div><p style="margin:0 0 12px;font-size:13px;color:#475569;">Agradecemos confirmar su disponibilidad con su equipo de coordinación. Si tiene observaciones o requiere apoyo logístico, puede responder directamente a este correo.</p><p style="margin:0;font-size:12px;color:#94a3b8;">Este mensaje fue generado automáticamente por el sistema municipal.</p></td></tr><tr><td style="background-color:#f8fafc;padding:16px 32px;text-align:center;font-size:12px;color:#94a3b8;">Municipalidad de {{municipalidad_nombre}} · Invitación oficial</td></tr></table></td></tr></table></body></html>'
WHERE NOT EXISTS (
  SELECT 1 FROM `email_templates` WHERE `template_key` = 'invitacion_autoridades'
);
