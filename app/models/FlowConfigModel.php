<?php

declare(strict_types=1);

class FlowConfigModel
{
    public function ensureTable(): void
    {
        db()->exec(
            'CREATE TABLE IF NOT EXISTS flow_config (
                id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                environment ENUM("production", "sandbox") NOT NULL DEFAULT "production",
                api_key VARCHAR(120) NOT NULL,
                secret_key VARCHAR(200) NOT NULL,
                return_url_base VARCHAR(255) NULL,
                confirmation_url_base VARCHAR(255) NULL,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                created_at DATETIME NOT NULL,
                updated_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
    }

    public function getActiveConfig(): array
    {
        $this->ensureTable();
        $stmt = db()->query('SELECT * FROM flow_config WHERE is_active = 1 ORDER BY id DESC LIMIT 1');
        $config = $stmt->fetch() ?: [];

        $environment = $config['environment'] ?? 'production';
        $baseUrl = $environment === 'sandbox' ? 'https://sandbox.flow.cl/api' : 'https://www.flow.cl/api';

        return [
            'id' => $config['id'] ?? null,
            'environment' => $environment,
            'api_key' => $config['api_key'] ?? '',
            'secret_key' => $config['secret_key'] ?? '',
            'return_url_base' => $config['return_url_base'] ?? null,
            'confirmation_url_base' => $config['confirmation_url_base'] ?? null,
            'base_url' => $baseUrl,
        ];
    }

    public function saveConfig(array $data): void
    {
        $this->ensureTable();
        db()->exec('UPDATE flow_config SET is_active = 0');

        $stmt = db()->prepare(
            'INSERT INTO flow_config (environment, api_key, secret_key, return_url_base, confirmation_url_base, is_active, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())'
        );
        $stmt->execute([
            $data['environment'],
            $data['api_key'],
            $data['secret_key'],
            $data['return_url_base'],
            $data['confirmation_url_base'],
        ]);
    }

    public function maskSecret(string $secret): string
    {
        $length = strlen($secret);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4) . substr($secret, -4);
    }
}
