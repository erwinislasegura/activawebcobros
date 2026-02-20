-- Actualización: alta de servicios por cliente (motivo + info + trazabilidad correo)
-- Fecha: 2026-03-27

ALTER TABLE `clientes_servicios`
    ADD COLUMN IF NOT EXISTS `motivo` TEXT NULL AFTER `servicio_id`,
    ADD COLUMN IF NOT EXISTS `info_importante` TEXT NULL AFTER `motivo`,
    ADD COLUMN IF NOT EXISTS `correo_enviado_at` DATETIME NULL AFTER `info_importante`;
