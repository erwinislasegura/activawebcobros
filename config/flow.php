<?php

declare(strict_types=1);

function flow_env(): string
{
    $env = strtolower(trim((string) getenv('FLOW_ENV')));
    if (!in_array($env, ['sandbox', 'production'], true)) {
        $env = 'sandbox';
    }

    return $env;
}

function flow_base_url(string $environment): string
{
    return $environment === 'production'
        ? 'https://www.flow.cl/api'
        : 'https://sandbox.flow.cl/api';
}

function flow_config(): array
{
    $environment = flow_env();
    $config = [
        'environment' => $environment,
        'api_key' => (string) getenv('FLOW_API_KEY'),
        'secret_key' => (string) getenv('FLOW_SECRET_KEY'),
        'base_url' => flow_base_url($environment),
        'source' => 'env',
    ];

    if (function_exists('db')) {
        try {
            $stmt = db()->query('SELECT * FROM flow_config ORDER BY id DESC LIMIT 1');
            $row = $stmt->fetch();
            if (is_array($row) && !empty($row['environment'])) {
                $config['environment'] = $row['environment'];
                $config['api_key'] = $row['api_key'] ?? $config['api_key'];
                $config['secret_key'] = $row['secret_key'] ?? $config['secret_key'];
                $config['base_url'] = flow_base_url($config['environment']);
                $config['source'] = 'database';
            }
        } catch (Exception $e) {
        } catch (Error $e) {
        }
    }

    return $config;
}
