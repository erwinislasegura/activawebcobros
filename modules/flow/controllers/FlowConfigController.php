<?php

declare(strict_types=1);

class FlowConfigController
{
    private ?string $lastError = null;

    public function ensureTable(): void
    {
        $this->lastError = null;

        try {
            db()->exec(
                'CREATE TABLE IF NOT EXISTS flow_config (
                    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                    environment VARCHAR(20) NOT NULL DEFAULT "sandbox",
                    api_key VARCHAR(120) NOT NULL,
                    secret_key VARCHAR(120) NOT NULL,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
            );
        } catch (Exception $e) {
            $this->lastError = 'No fue posible preparar la tabla de configuración Flow.';
        } catch (Error $e) {
            $this->lastError = 'No fue posible preparar la tabla de configuración Flow.';
        }
    }

    public function getConfig(): array
    {
        $this->ensureTable();
        $config = flow_config();

        if ($this->lastError !== null) {
            return $config;
        }

        try {
            $stmt = db()->query('SELECT * FROM flow_config ORDER BY id DESC LIMIT 1');
            $row = $stmt->fetch();
            if (is_array($row)) {
                $config['environment'] = $row['environment'] ?? $config['environment'];
                $config['api_key'] = $row['api_key'] ?? $config['api_key'];
                $config['secret_key'] = $row['secret_key'] ?? $config['secret_key'];
                $config['base_url'] = flow_base_url($config['environment']);
                $config['source'] = 'database';
            }
        } catch (Exception $e) {
        } catch (Error $e) {
        }

        return $config;
    }

    public function saveConfig(string $environment, string $apiKey, string $secretKey): void
    {
        $this->ensureTable();
        if ($this->lastError !== null) {
            return;
        }

        $stmt = db()->prepare('INSERT INTO flow_config (environment, api_key, secret_key) VALUES (?, ?, ?)');
        $stmt->execute([$environment, $apiKey, $secretKey]);
    }

    public function validateConfig(array $config): array
    {
        $errors = [];
        if (!in_array($config['environment'], ['sandbox', 'production'], true)) {
            $errors[] = 'Selecciona un ambiente válido para Flow.';
        }
        if ($config['api_key'] === '' || $config['secret_key'] === '') {
            $errors[] = 'ApiKey y SecretKey son obligatorias.';
        }

        return $errors;
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
