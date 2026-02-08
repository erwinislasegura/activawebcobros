-- Actualización: clientes, servicios y cobros
-- Fecha: 2026-03-20

CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `direccion` VARCHAR(180) DEFAULT NULL,
  `color_hex` VARCHAR(10) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tipos_servicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(120) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `servicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo_servicio_id` INT DEFAULT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `monto` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `servicios_tipo_idx` (`tipo_servicio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `cobros_servicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `servicio_id` INT NOT NULL,
  `cliente_id` INT DEFAULT NULL,
  `cliente` VARCHAR(150) NOT NULL,
  `referencia` VARCHAR(120) DEFAULT NULL,
  `monto` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `fecha_cobro` DATE NOT NULL,
  `fecha_primer_aviso` DATE DEFAULT NULL,
  `fecha_segundo_aviso` DATE DEFAULT NULL,
  `fecha_tercer_aviso` DATE DEFAULT NULL,
  `estado` VARCHAR(40) NOT NULL DEFAULT 'Pendiente',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cobros_servicios_servicio_idx` (`servicio_id`),
  KEY `cobros_servicios_cliente_idx` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `servicios`
  ADD COLUMN IF NOT EXISTS `tipo_servicio_id` INT DEFAULT NULL;

ALTER TABLE `cobros_servicios`
  ADD COLUMN IF NOT EXISTS `cliente_id` INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_primer_aviso` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_segundo_aviso` DATE DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS `fecha_tercer_aviso` DATE DEFAULT NULL;
