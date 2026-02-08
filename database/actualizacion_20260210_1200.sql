CREATE TABLE IF NOT EXISTS `notificacion_whatsapp` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `phone_number_id` VARCHAR(80) NOT NULL,
  `access_token` TEXT NOT NULL,
  `numero_envio` VARCHAR(30) DEFAULT NULL,
  `country_code` VARCHAR(6) DEFAULT NULL,
  `template_name` VARCHAR(120) DEFAULT NULL,
  `template_language` VARCHAR(10) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
