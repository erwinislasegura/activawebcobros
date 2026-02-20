-- Actualización: alta de servicios por cliente (motivo + info + trazabilidad correo)
-- Fecha: 2026-03-27

SET @db_name = DATABASE();

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @db_name
              AND TABLE_NAME = 'clientes_servicios'
              AND COLUMN_NAME = 'motivo'
        ),
        'SELECT 1',
        'ALTER TABLE `clientes_servicios` ADD COLUMN `motivo` TEXT NULL AFTER `servicio_id`'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @db_name
              AND TABLE_NAME = 'clientes_servicios'
              AND COLUMN_NAME = 'info_importante'
        ),
        'SELECT 1',
        'ALTER TABLE `clientes_servicios` ADD COLUMN `info_importante` TEXT NULL AFTER `motivo`'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (
    SELECT IF(
        EXISTS(
            SELECT 1
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @db_name
              AND TABLE_NAME = 'clientes_servicios'
              AND COLUMN_NAME = 'correo_enviado_at'
        ),
        'SELECT 1',
        'ALTER TABLE `clientes_servicios` ADD COLUMN `correo_enviado_at` DATETIME NULL AFTER `info_importante`'
    )
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
