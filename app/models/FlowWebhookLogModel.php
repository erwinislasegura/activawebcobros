<?php

declare(strict_types=1);

class FlowWebhookLogModel
{
    public function ensureTable(): void
    {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS flow_webhook_logs (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                flow_token VARCHAR(80) NOT NULL,
                payload MEDIUMTEXT NULL,
                processed TINYINT(1) NOT NULL DEFAULT 0,
                processing_notes VARCHAR(255) NULL,
                created_at DATETIME NOT NULL,
                processed_at DATETIME NULL,
                PRIMARY KEY (id),
                UNIQUE KEY uq_flow_webhook_token (flow_token)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function findByToken(string $token): ?array
    {
        $this->ensureTable();
        $stmt = db()->prepare('SELECT * FROM flow_webhook_logs WHERE flow_token = ?');
        $stmt->execute([$token]);
        $log = $stmt->fetch();

        return is_array($log) ? $log : null;
    }

    public function create(string $token, array $payload, string $notes, bool $processed): void
    {
        $this->ensureTable();
        $stmt = db()->prepare(
            'INSERT INTO flow_webhook_logs (flow_token, payload, processed, processing_notes, created_at, processed_at)
             VALUES (?, ?, ?, ?, NOW(), ?)'
        );
        $stmt->execute([
            $token,
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            $processed ? 1 : 0,
            $notes,
            $processed ? date('Y-m-d H:i:s') : null,
        ]);
    }

    public function markProcessed(string $token, string $notes): void
    {
        $this->ensureTable();
        $stmt = db()->prepare(
            'UPDATE flow_webhook_logs SET processed = 1, processing_notes = ?, processed_at = NOW() WHERE flow_token = ?'
        );
        $stmt->execute([$notes, $token]);
    }

    public function list(int $limit = 100): array
    {
        $this->ensureTable();
        $stmt = db()->prepare('SELECT * FROM flow_webhook_logs ORDER BY created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
