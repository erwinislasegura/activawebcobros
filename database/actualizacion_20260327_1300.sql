-- Actualización: módulo Flow (configuración, órdenes, webhooks)
-- Fecha: 2026-03-27

CREATE TABLE IF NOT EXISTS flow_config (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    environment ENUM('production','sandbox') NOT NULL DEFAULT 'production',
    api_key VARCHAR(120) NOT NULL,
    secret_key VARCHAR(200) NOT NULL,
    return_url_base VARCHAR(255) NULL,
    confirmation_url_base VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS flow_orders (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    local_order_id VARCHAR(80) NOT NULL,
    subject VARCHAR(255) NULL,
    amount INT NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'CLP',
    customer_email VARCHAR(120) NULL,
    flow_token VARCHAR(80) NULL UNIQUE,
    flow_order VARCHAR(80) NULL,
    status VARCHAR(40) NOT NULL DEFAULT 'created',
    status_detail VARCHAR(255) NULL,
    payment_url TEXT NULL,
    raw_request MEDIUMTEXT NULL,
    raw_response MEDIUMTEXT NULL,
    last_status_response MEDIUMTEXT NULL,
    created_at DATETIME NOT NULL,
    updated_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    KEY idx_flow_orders_local (local_order_id),
    KEY idx_flow_orders_status (status),
    KEY idx_flow_orders_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS flow_webhook_logs (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    flow_token VARCHAR(80) NOT NULL,
    payload MEDIUMTEXT NULL,
    processed TINYINT(1) NOT NULL DEFAULT 0,
    processing_notes VARCHAR(255) NULL,
    created_at DATETIME NOT NULL,
    processed_at DATETIME NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_flow_webhook_token (flow_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `permissions` (`modulo`, `accion`, `descripcion`)
VALUES
  ('flow', 'view', 'Ver pagos Flow'),
  ('flow', 'create', 'Crear pagos Flow'),
  ('flow', 'edit', 'Actualizar pagos Flow')
ON DUPLICATE KEY UPDATE descripcion = VALUES(descripcion);
