-- Actualización: permisos para clientes, servicios, cobros y avisos
-- Fecha: 2026-03-24

INSERT INTO `permissions` (`modulo`, `accion`, `descripcion`)
VALUES
  ('clientes', 'ver', 'Ver clientes'),
  ('clientes', 'crear', 'Crear clientes'),
  ('clientes', 'editar', 'Editar clientes'),
  ('clientes', 'eliminar', 'Deshabilitar clientes'),
  ('servicios', 'ver', 'Ver servicios'),
  ('servicios', 'crear', 'Crear servicios'),
  ('servicios', 'editar', 'Editar servicios'),
  ('servicios', 'eliminar', 'Deshabilitar servicios'),
  ('cobros', 'ver', 'Ver cobros'),
  ('cobros', 'crear', 'Crear cobros'),
  ('cobros', 'editar', 'Editar cobros'),
  ('cobros', 'eliminar', 'Deshabilitar cobros'),
  ('avisos', 'ver', 'Ver avisos'),
  ('avisos', 'enviar', 'Enviar avisos')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
