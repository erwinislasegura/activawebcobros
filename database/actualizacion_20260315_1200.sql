-- Actualización: branding Go Muni
-- Fecha: 2026-03-15

UPDATE `municipalidad`
SET `nombre` = 'Go Muni'
WHERE `nombre` IS NULL OR `nombre` = '' OR `nombre` = 'Municipalidad';
