-- Agrega fecha de registro, periodicidad y fecha de vencimiento para servicios asociados a clientes
ALTER TABLE clientes_servicios
    ADD COLUMN fecha_registro DATE NULL AFTER servicio_id,
    ADD COLUMN tiempo_servicio VARCHAR(30) NULL AFTER fecha_registro,
    ADD COLUMN fecha_vencimiento DATE NULL AFTER tiempo_servicio;

UPDATE clientes_servicios
SET tiempo_servicio = 'Mensual'
WHERE tiempo_servicio IS NULL OR TRIM(tiempo_servicio) = '';

UPDATE clientes_servicios
SET fecha_registro = DATE(created_at)
WHERE fecha_registro IS NULL;

UPDATE clientes_servicios
SET fecha_vencimiento = CASE LOWER(TRIM(tiempo_servicio))
    WHEN 'mensual' THEN DATE_ADD(fecha_registro, INTERVAL 1 MONTH)
    WHEN 'bimestral' THEN DATE_ADD(fecha_registro, INTERVAL 2 MONTH)
    WHEN 'trimestral' THEN DATE_ADD(fecha_registro, INTERVAL 3 MONTH)
    WHEN 'semestral' THEN DATE_ADD(fecha_registro, INTERVAL 6 MONTH)
    WHEN 'anual' THEN DATE_ADD(fecha_registro, INTERVAL 1 YEAR)
    ELSE fecha_vencimiento
END
WHERE fecha_registro IS NOT NULL AND fecha_vencimiento IS NULL;
