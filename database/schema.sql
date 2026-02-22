CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `rut` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NOT NULL,
  `cargo` VARCHAR(100) DEFAULT NULL,
  `fecha_nacimiento` DATE DEFAULT NULL,
  `correo` VARCHAR(150) NOT NULL,
  `telefono` VARCHAR(30) NOT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `username` VARCHAR(60) NOT NULL,
  `rol` VARCHAR(60) DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `avatar_path` VARCHAR(255) DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `fecha_creacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultimo_acceso` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_rut_unique` (`rut`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_correo_unique` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `unidades` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unidades_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `roles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(60) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_roles` (
  `user_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`),
  CONSTRAINT `user_roles_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `permissions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `modulo` VARCHAR(60) NOT NULL,
  `accion` VARCHAR(30) NOT NULL,
  `descripcion` VARCHAR(200) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_modulo_accion_unique` (`modulo`, `accion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `role_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `role_unit_permissions` (
  `role_id` INT UNSIGNED NOT NULL,
  `unidad_id` INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `unidad_id`, `permission_id`),
  CONSTRAINT `role_unit_permissions_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_unit_permissions_unidad_id_fk` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_unit_permissions_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `user_sessions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `session_id` VARCHAR(128) NOT NULL,
  `ip` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_activity` TIMESTAMP NULL DEFAULT NULL,
  `ended_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_sessions_session_unique` (`session_id`),
  CONSTRAINT `user_sessions_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `audit_logs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED DEFAULT NULL,
  `tabla` VARCHAR(60) NOT NULL,
  `accion` VARCHAR(20) NOT NULL,
  `registro_id` INT UNSIGNED DEFAULT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `audit_logs_user_id_idx` (`user_id`),
  CONSTRAINT `audit_logs_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `municipalidad` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `rut` VARCHAR(20) DEFAULT NULL,
  `direccion` VARCHAR(200) DEFAULT NULL,
  `telefono` VARCHAR(30) DEFAULT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `logo_path` VARCHAR(255) DEFAULT NULL,
  `logo_inicio_path` VARCHAR(255) DEFAULT NULL,
  `logo_topbar_height` INT UNSIGNED DEFAULT NULL,
  `logo_sidenav_height` INT UNSIGNED DEFAULT NULL,
  `logo_sidenav_height_sm` INT UNSIGNED DEFAULT NULL,
  `logo_auth_height` INT UNSIGNED DEFAULT NULL,
  `color_primary` VARCHAR(20) DEFAULT NULL,
  `color_secondary` VARCHAR(20) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notificacion_correos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `correo_imap` VARCHAR(150) NOT NULL,
  `password_imap` VARCHAR(255) NOT NULL,
  `host_imap` VARCHAR(150) NOT NULL,
  `puerto_imap` INT UNSIGNED NOT NULL DEFAULT 993,
  `seguridad_imap` VARCHAR(30) NOT NULL DEFAULT 'ssl',
  `from_nombre` VARCHAR(150) DEFAULT NULL,
  `from_correo` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `email_templates` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_key` VARCHAR(120) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `body_html` MEDIUMTEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_templates_key_unique` (`template_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `notification_settings` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `canal_email` TINYINT(1) NOT NULL DEFAULT 1,
  `canal_sms` TINYINT(1) NOT NULL DEFAULT 0,
  `canal_app` TINYINT(1) NOT NULL DEFAULT 1,
  `frecuencia` VARCHAR(30) NOT NULL DEFAULT 'diario',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `notification_rules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `evento` VARCHAR(120) NOT NULL,
  `destino` VARCHAR(150) NOT NULL,
  `canal` VARCHAR(50) NOT NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'activa',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_categories` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `documents` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(200) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `categoria_id` INT UNSIGNED DEFAULT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'vigente',
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `documents_categoria_id_fk` FOREIGN KEY (`categoria_id`) REFERENCES `document_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_unidad_id_fk` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_versions` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `document_id` INT UNSIGNED NOT NULL,
  `version` VARCHAR(20) NOT NULL,
  `archivo_ruta` VARCHAR(255) NOT NULL,
  `archivo_tipo` VARCHAR(50) NOT NULL,
  `vencimiento` DATE DEFAULT NULL,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `document_versions_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_versions_created_by_fk` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_tag_links` (
  `document_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`document_id`, `tag_id`),
  CONSTRAINT `document_tag_links_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_tag_links_tag_id_fk` FOREIGN KEY (`tag_id`) REFERENCES `document_tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_access` (
  `document_id` INT UNSIGNED NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`document_id`, `role_id`),
  CONSTRAINT `document_access_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_access_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `document_shares` (
  `document_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`, `user_id`),
  CONSTRAINT `document_shares_document_id_fk` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_shares_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `approval_flows` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(150) NOT NULL,
  `entidad` VARCHAR(80) NOT NULL,
  `unidad_id` INT UNSIGNED DEFAULT NULL,
  `sla_horas` INT UNSIGNED NOT NULL DEFAULT 48,
  `estado` VARCHAR(30) NOT NULL DEFAULT 'activo',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `approval_flows_unidad_id_fk` FOREIGN KEY (`unidad_id`) REFERENCES `unidades` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `approval_steps` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `flow_id` INT UNSIGNED NOT NULL,
  `orden` INT UNSIGNED NOT NULL,
  `responsable` VARCHAR(150) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `approval_steps_flow_id_fk` FOREIGN KEY (`flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (
  `rut`,
  `nombre`,
  `apellido`,
  `correo`,
  `telefono`,
  `direccion`,
  `username`,
  `rol`,
  `password_hash`,
  `estado`
) VALUES (
  '9.999.999-9',
  'Super',
  'User',
  'admin@muni.cl',
  '+56 9 1234 5678',
  'Municipalidad Central',
  'superuser',
  'SuperAdmin',
  '$2y$12$nNyFQLLuFHy7yjLILUTlIO3NQ96Vw5rS90YCDml1ZKINCPv7Lvshe',
  1
);

INSERT INTO `roles` (`nombre`, `descripcion`, `estado`)
VALUES
  ('SuperAdmin', 'Control total del sistema', 1),
  ('Admin', 'Administración general', 1),
  ('EncargadoEventos', 'Gestión de eventos', 1),
  ('Auditor', 'Revisión y auditoría', 1),
  ('Consulta', 'Acceso de solo lectura', 1);

-- Datos QA para pruebas de flujo -- Sección: unidades
INSERT INTO `unidades` (`nombre`, `descripcion`)
VALUES
  ('Administración', 'Gestión administrativa municipal'),
  ('Finanzas', 'Gestión presupuestaria y contable'),
  ('Recursos Humanos', 'Gestión de personal y bienestar'),
  ('DIDECO', 'Desarrollo comunitario'),
  ('SECPLAN', 'Planificación comunal'),
  ('Tránsito', 'Permisos y gestión vial'),
  ('Obras Municipales', 'Permisos y fiscalización de obras'),
  ('Salud', 'Coordinación de atención primaria'),
  ('Educación', 'Gestión educativa comunal'),
  ('Medio Ambiente', 'Programas y fiscalización ambiental'),
  ('Cultura', 'Actividades culturales'),
  ('Deportes', 'Programas deportivos'),
  ('Seguridad', 'Prevención y seguridad pública'),
  ('Turismo', 'Promoción turística'),
  ('Vivienda', 'Programas habitacionales'),
  ('Fomento Productivo', 'Apoyo a emprendedores'),
  ('Adulto Mayor', 'Programas para personas mayores'),
  ('Infancia', 'Programas de infancia'),
  ('Juventud', 'Programas juveniles'),
  ('Participación Ciudadana', 'Vinculación con la comunidad');

CREATE TABLE `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `codigo` VARCHAR(20) NOT NULL,
  `nombre` VARCHAR(150) NOT NULL,
  `correo` VARCHAR(150) DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `direccion` VARCHAR(180) DEFAULT NULL,
  `sitio_web` VARCHAR(180) DEFAULT NULL,
  `color_hex` VARCHAR(10) NOT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientes_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `tipos_servicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(120) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `servicios` (
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

CREATE TABLE `clientes_servicios` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cliente_id` INT NOT NULL,
  `servicio_id` INT NOT NULL,
  `motivo` TEXT DEFAULT NULL,
  `info_importante` TEXT DEFAULT NULL,
  `correo_enviado_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cliente_servicio` (`cliente_id`, `servicio_id`),
  KEY `idx_clientes_servicios_cliente` (`cliente_id`),
  KEY `idx_clientes_servicios_servicio` (`servicio_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `clientes_servicios_suspensiones` (
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

CREATE TABLE `cobros_servicios` (
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
  `aviso_1_enviado_at` DATETIME DEFAULT NULL,
  `aviso_2_enviado_at` DATETIME DEFAULT NULL,
  `aviso_3_enviado_at` DATETIME DEFAULT NULL,
  `estado` VARCHAR(40) NOT NULL DEFAULT 'Pendiente',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `cobros_servicios_servicio_idx` (`servicio_id`),
  KEY `cobros_servicios_cliente_idx` (`cliente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `pagos_clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cobro_id` INT NOT NULL,
  `cliente_id` INT DEFAULT NULL,
  `servicio_id` INT DEFAULT NULL,
  `monto` DECIMAL(10,2) NOT NULL DEFAULT 0,
  `fecha_pago` DATE NOT NULL,
  `metodo` VARCHAR(60) DEFAULT NULL,
  `referencia_pago` VARCHAR(120) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_pagos_cobro` (`cobro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE `email_accounts` (
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

CREATE TABLE `email_messages` (
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

INSERT INTO `email_accounts` (`in_email`, `in_password`, `in_host`, `in_port`, `in_security`, `out_email`, `out_name`, `out_password`, `out_host`, `out_port`, `out_security`)
VALUES ('email.entrada@municipalidad.cl', 'cambiar_password', 'imap.gmail.com', 993, 'ssl', 'email.salida@municipalidad.cl', 'Sistema Municipal', 'cambiar_password', 'smtp.gmail.com', 587, 'tls');

INSERT INTO `municipalidad` (`nombre`, `rut`, `direccion`, `telefono`, `correo`, `logo_path`, `logo_inicio_path`, `color_primary`, `color_secondary`)
VALUES ('Go Cobros', NULL, NULL, NULL, NULL, 'assets/images/logo.png', 'assets/images/logo.png', '#6658dd', '#4a81d4');

INSERT INTO `notificacion_correos` (`correo_imap`, `password_imap`, `host_imap`, `puerto_imap`, `seguridad_imap`, `from_nombre`, `from_correo`)
VALUES ('notificaciones@municipalidad.cl', 'cambiar_password', 'imap.municipalidad.cl', 993, 'ssl', 'Sistema Municipal', 'notificaciones@municipalidad.cl');

INSERT INTO `notification_settings` (`canal_email`, `canal_sms`, `canal_app`, `frecuencia`)
VALUES (1, 0, 1, 'diario');

INSERT INTO `permissions` (`modulo`, `accion`, `descripcion`)
VALUES
  ('usuarios', 'ver', 'Ver usuarios'),
  ('usuarios', 'crear', 'Crear usuarios'),
  ('usuarios', 'editar', 'Editar usuarios'),
  ('usuarios', 'eliminar', 'Deshabilitar usuarios'),
  ('roles', 'ver', 'Ver roles'),
  ('roles', 'crear', 'Crear roles'),
  ('roles', 'editar', 'Editar roles'),
  ('roles', 'eliminar', 'Deshabilitar roles'),
  ('clientes', 'view', 'Ver clientes'),
  ('clientes', 'create', 'Crear clientes'),
  ('clientes', 'edit', 'Editar clientes'),
  ('clientes', 'delete', 'Deshabilitar clientes'),
  ('servicios', 'view', 'Ver servicios'),
  ('servicios', 'create', 'Crear servicios'),
  ('servicios', 'edit', 'Editar servicios'),
  ('servicios', 'delete', 'Deshabilitar servicios'),
  ('cobros', 'view', 'Ver cobros'),
  ('cobros', 'create', 'Crear cobros'),
  ('cobros', 'edit', 'Editar cobros'),
  ('cobros', 'delete', 'Deshabilitar cobros'),
  ('avisos', 'view', 'Ver avisos'),
  ('avisos', 'send', 'Enviar avisos'),
  ('adjuntos', 'subir', 'Subir adjuntos'),
  ('adjuntos', 'eliminar', 'Eliminar adjuntos'),
  ('adjuntos', 'descargar', 'Descargar adjuntos');
