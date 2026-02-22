-- Módulo Email: configuración separada entrada/salida y bandejas locales
CREATE TABLE IF NOT EXISTS `email_accounts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `in_email` VARCHAR(150) NOT NULL,
  `in_password` VARCHAR(255) NOT NULL,
  `in_host` VARCHAR(150) NOT NULL,
  `in_port` INT UNSIGNED NOT NULL DEFAULT 993,
  `in_security` VARCHAR(20) NOT NULL DEFAULT 'ssl',
  `out_email` VARCHAR(150) NOT NULL,
  `out_name` VARCHAR(150) DEFAULT NULL,
  `out_password` VARCHAR(255) NOT NULL,
  `out_host` VARCHAR(150) NOT NULL,
  `out_port` INT UNSIGNED NOT NULL DEFAULT 587,
  `out_security` VARCHAR(20) NOT NULL DEFAULT 'tls',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `email_messages` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `box` VARCHAR(30) NOT NULL,
  `recipient` VARCHAR(150) DEFAULT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `body_html` MEDIUMTEXT NOT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'draft',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email_messages_box` (`box`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `email_accounts`
(`in_email`, `in_password`, `in_host`, `in_port`, `in_security`, `out_email`, `out_name`, `out_password`, `out_host`, `out_port`, `out_security`)
SELECT
  nc.correo_imap,
  nc.password_imap,
  nc.host_imap,
  nc.puerto_imap,
  nc.seguridad_imap,
  COALESCE(nc.from_correo, nc.correo_imap),
  nc.from_nombre,
  nc.password_imap,
  'smtp.gmail.com',
  587,
  'tls'
FROM notificacion_correos nc
WHERE NOT EXISTS (SELECT 1 FROM email_accounts);
