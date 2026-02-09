-- Actualización: branding Go Cobros
-- Fecha: 2026-03-15

UPDATE `municipalidad`
SET `nombre` = 'Go Cobros'
WHERE `nombre` IS NULL OR `nombre` = '' OR `nombre` = 'Municipalidad';
