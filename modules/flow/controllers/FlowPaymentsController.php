<?php

declare(strict_types=1);

class FlowPaymentsController
{
    public function ensureTables(): void
    {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS flow_orders (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                local_order_id VARCHAR(80) NOT NULL,
                flow_token VARCHAR(80) DEFAULT NULL,
                flow_order VARCHAR(80) DEFAULT NULL,
                amount DECIMAL(12,2) NOT NULL,
                currency VARCHAR(10) NOT NULL DEFAULT "CLP",
                status VARCHAR(40) NOT NULL DEFAULT "pending",
                payer_email VARCHAR(120) DEFAULT NULL,
                raw_request MEDIUMTEXT DEFAULT NULL,
                raw_response MEDIUMTEXT DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (id),
                KEY idx_flow_orders_token (flow_token),
                KEY idx_flow_orders_local (local_order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );

        db()->exec(
            'CREATE TABLE IF NOT EXISTS flow_webhook_logs (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                token VARCHAR(80) NOT NULL,
                payload MEDIUMTEXT DEFAULT NULL,
                status VARCHAR(40) NOT NULL DEFAULT "received",
                processed_at TIMESTAMP NULL DEFAULT NULL,
                notes VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (id),
                KEY idx_flow_webhook_token (token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function createPayment(FlowClient $client, array $payload): array
    {
        $this->ensureTables();
        $requestData = $this->sanitizeRequest($payload);
        $response = $client->post('payment/create', $payload);

        $status = $response['success'] ? 'pending' : 'error';
        $flowToken = null;
        $flowOrder = null;
        if ($response['success']) {
            $data = $response['data'] ?? [];
            $flowToken = $data['token'] ?? null;
            $flowOrder = $data['flowOrder'] ?? null;
        }

        $stmt = db()->prepare(
            'INSERT INTO flow_orders (local_order_id, flow_token, flow_order, amount, currency, status, payer_email, raw_request, raw_response, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([
            $payload['commerceOrder'] ?? '',
            $flowToken,
            $flowOrder,
            $payload['amount'] ?? 0,
            $payload['currency'] ?? 'CLP',
            $status,
            $payload['email'] ?? null,
            json_encode($requestData, JSON_UNESCAPED_UNICODE),
            json_encode($response, JSON_UNESCAPED_UNICODE),
        ]);

        $orderId = (int) db()->lastInsertId();

        return [
            'order_id' => $orderId,
            'response' => $response,
        ];
    }

    public function getOrderById(int $orderId): ?array
    {
        $this->ensureTables();
        $stmt = db()->prepare('SELECT * FROM flow_orders WHERE id = ?');
        $stmt->execute([$orderId]);
        $order = $stmt->fetch();

        return is_array($order) ? $order : null;
    }

    public function getOrderByLocalId(string $localOrderId): ?array
    {
        $this->ensureTables();
        $stmt = db()->prepare('SELECT * FROM flow_orders WHERE local_order_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$localOrderId]);
        $order = $stmt->fetch();

        return is_array($order) ? $order : null;
    }

    public function listOrders(array $filters = []): array
    {
        $this->ensureTables();
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

    public function updateStatusFromFlow(FlowClient $client, string $token): array
    {
        $this->ensureTables();
        $response = $client->get('payment/getStatus', [
            'apiKey' => $this->getApiKey(),
            'token' => $token,
        ]);

        if ($response['success']) {
            $data = $response['data'] ?? [];
            $status = $data['status'] ?? 'unknown';
            $flowOrder = $data['flowOrder'] ?? null;

            $stmt = db()->prepare(
                'UPDATE flow_orders SET status = ?, flow_order = COALESCE(?, flow_order), raw_response = ?, updated_at = NOW() WHERE flow_token = ?'
            );
            $stmt->execute([
                $status,
                $flowOrder,
                json_encode($response, JSON_UNESCAPED_UNICODE),
                $token,
            ]);
        }

        return $response;
    }

    private function sanitizeRequest(array $payload): array
    {
        if (isset($payload['s'])) {
            unset($payload['s']);
        }
        if (isset($payload['secret_key'])) {
            unset($payload['secret_key']);
        }

        return $payload;
    }

    private function getApiKey(): string
    {
        $config = flow_config();

        return $config['api_key'] ?? '';
    }
}
