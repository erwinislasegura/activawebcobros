-- Actualización: sitio web cliente y plantillas de avisos
-- Fecha: 2026-03-23

ALTER TABLE `clientes`
  ADD COLUMN IF NOT EXISTS `sitio_web` VARCHAR(180) DEFAULT NULL;

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
VALUES
  ('aviso_1', 'Aviso 1: {{servicio_nombre}} - {{cliente_nombre}}', '<p style=\"font-family:Arial,sans-serif;\">Hola {{cliente_nombre}}, este es el aviso 1 del servicio {{servicio_nombre}} con fecha {{fecha_aviso}} y monto {{monto}}. Referencia: {{referencia}}.</p>'),
  ('aviso_2', 'Aviso 2: {{servicio_nombre}} - {{cliente_nombre}}', '<p style=\"font-family:Arial,sans-serif;\">Hola {{cliente_nombre}}, este es el aviso 2 del servicio {{servicio_nombre}} con fecha {{fecha_aviso}} y monto {{monto}}. Referencia: {{referencia}}.</p>'),
  ('aviso_3', 'Aviso 3: {{servicio_nombre}} - {{cliente_nombre}}', '<p style=\"font-family:Arial,sans-serif;\">Hola {{cliente_nombre}}, este es el aviso 3 del servicio {{servicio_nombre}} con fecha {{fecha_aviso}} y monto {{monto}}. Referencia: {{referencia}}.</p>')
ON DUPLICATE KEY UPDATE
  subject = VALUES(subject),
  body_html = VALUES(body_html);
