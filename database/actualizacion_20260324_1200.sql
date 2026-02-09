-- Actualización: permisos para clientes, servicios, cobros y avisos
-- Fecha: 2026-03-24

INSERT INTO `permissions` (`modulo`, `accion`, `descripcion`)
VALUES
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
  ('avisos', 'send', 'Enviar avisos')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
