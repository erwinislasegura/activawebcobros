CREATE TABLE IF NOT EXISTS `authority_groups` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(120) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `authority_groups_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `authorities`
  ADD COLUMN `group_id` INT UNSIGNED DEFAULT NULL;

ALTER TABLE `authorities`
  ADD CONSTRAINT `authorities_group_id_fk`
  FOREIGN KEY (`group_id`) REFERENCES `authority_groups` (`id`) ON DELETE SET NULL;
