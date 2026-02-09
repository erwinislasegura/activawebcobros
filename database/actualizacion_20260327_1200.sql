-- Actualización: módulo Flow (configuración y órdenes)
-- Fecha: 2026-03-27

CREATE TABLE IF NOT EXISTS flow_config (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    environment VARCHAR(20) NOT NULL DEFAULT 'sandbox',
    api_key VARCHAR(120) NOT NULL,
    secret_key VARCHAR(120) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS flow_orders (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    local_order_id VARCHAR(80) NOT NULL,
    flow_token VARCHAR(80) DEFAULT NULL,
    flow_order VARCHAR(80) DEFAULT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    status VARCHAR(40) NOT NULL DEFAULT 'pending',
    payer_email VARCHAR(120) DEFAULT NULL,
    raw_request MEDIUMTEXT DEFAULT NULL,
    raw_response MEDIUMTEXT DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_flow_orders_token (flow_token),
    KEY idx_flow_orders_local (local_order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS flow_webhook_logs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    token VARCHAR(80) NOT NULL,
    payload MEDIUMTEXT DEFAULT NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'received',
    processed_at TIMESTAMP NULL DEFAULT NULL,
    notes VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_flow_webhook_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `permissions` (`modulo`, `accion`, `descripcion`)
VALUES
  ('flow', 'view', 'Ver pagos Flow'),
  ('flow', 'create', 'Crear pagos Flow'),
  ('flow', 'edit', 'Actualizar pagos Flow')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
