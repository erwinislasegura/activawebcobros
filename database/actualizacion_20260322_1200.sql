-- Actualización: limpiar eventos/autoridades y permisos asociados
-- Fecha: 2026-03-22

DELETE FROM `role_unit_permissions`
WHERE `permission_id` IN (
  SELECT `id` FROM `permissions` WHERE `modulo` IN ('eventos', 'autoridades')
);

DELETE FROM `role_permissions`
WHERE `permission_id` IN (
  SELECT `id` FROM `permissions` WHERE `modulo` IN ('eventos', 'autoridades')
);

DELETE FROM `permissions`
WHERE `modulo` IN ('eventos', 'autoridades');

DROP TABLE IF EXISTS `event_authority_attendance`;
DROP TABLE IF EXISTS `event_authority_invitations`;
DROP TABLE IF EXISTS `authority_attachments`;
DROP TABLE IF EXISTS `media_accreditation_access_logs`;
DROP TABLE IF EXISTS `media_accreditation_requests`;
DROP TABLE IF EXISTS `event_media_accreditation_links`;
DROP TABLE IF EXISTS `event_authority_confirmations`;
DROP TABLE IF EXISTS `event_authority_requests`;
DROP TABLE IF EXISTS `event_authorities`;
DROP TABLE IF EXISTS `authorities`;
DROP TABLE IF EXISTS `event_attachments`;
DROP TABLE IF EXISTS `events`;
