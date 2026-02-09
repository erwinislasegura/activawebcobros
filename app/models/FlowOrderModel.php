<?php

declare(strict_types=1);

class FlowOrderModel
{
    public function ensureTable(): void
    {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS flow_orders (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                local_order_id VARCHAR(80) NOT NULL,
                subject VARCHAR(255) NULL,
                amount INT NOT NULL,
                currency VARCHAR(10) NOT NULL DEFAULT "CLP",
                customer_email VARCHAR(120) NULL,
                flow_token VARCHAR(80) NULL UNIQUE,
                flow_order VARCHAR(80) NULL,
                status VARCHAR(40) NOT NULL DEFAULT "created",
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function create(array $data): int
    {
        $this->ensureTable();
        $stmt = db()->prepare(
            'INSERT INTO flow_orders (local_order_id, subject, amount, currency, customer_email, flow_token, flow_order, status, status_detail, payment_url, raw_request, raw_response, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())'
        );
        $stmt->execute([
            $data['local_order_id'],
            $data['subject'],
            $data['amount'],
            $data['currency'],
            $data['customer_email'],
            $data['flow_token'],
            $data['flow_order'],
            $data['status'],
            $data['status_detail'],
            $data['payment_url'],
            $data['raw_request'],
            $data['raw_response'],
        ]);

        return (int) db()->lastInsertId();
    }

    public function updateStatusByToken(string $token, array $data): void
    {
        $this->ensureTable();
        $stmt = db()->prepare(
            'UPDATE flow_orders SET status = ?, status_detail = ?, last_status_response = ?, updated_at = NOW() WHERE flow_token = ?'
        );
        $stmt->execute([
            $data['status'],
            $data['status_detail'],
            $data['last_status_response'],
            $token,
        ]);
    }

    public function updateStatusById(int $orderId, array $data): void
    {
        $this->ensureTable();
        $stmt = db()->prepare(
            'UPDATE flow_orders SET status = ?, status_detail = ?, last_status_response = ?, updated_at = NOW() WHERE id = ?'
        );
        $stmt->execute([
            $data['status'],
            $data['status_detail'],
            $data['last_status_response'],
            $orderId,
        ]);
    }

    public function findById(int $orderId): ?array
    {
        $this->ensureTable();
        $stmt = db()->prepare('SELECT * FROM flow_orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        return is_array($order) ? $order : null;
    }

    public function findByToken(string $token): ?array
    {
        $this->ensureTable();
        $stmt = db()->prepare('SELECT * FROM flow_orders WHERE flow_token = ?');
        $stmt->execute([$token]);
        $order = $stmt->fetch();

        return is_array($order) ? $order : null;
    }

    public function list(array $filters): array
    {
        $this->ensureTable();
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['from'])) {
            $conditions[] = 'created_at >= ?';
            $params[] = $filters['from'] . ' 00:00:00';
        }

        if (!empty($filters['to'])) {
            $conditions[] = 'created_at <= ?';
            $params[] = $filters['to'] . ' 23:59:59';
        }

        $sql = 'SELECT * FROM flow_orders';
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY created_at DESC';

        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
