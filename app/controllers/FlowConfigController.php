<?php

declare(strict_types=1);

class FlowConfigController
{
    public function __construct(private FlowConfigModel $model)
    {
    }

    public function show(): array
    {
        $config = $this->model->getActiveConfig();
        $config['masked_secret_key'] = $config['secret_key'] !== ''
            ? $this->model->maskSecret($config['secret_key'])
            : '';
        $config['secret_key'] = '';

        return $config;
    }

    public function save(array $data): array
    {
        $errors = [];
        $environment = $data['environment'] ?? 'production';
        $apiKey = trim((string) ($data['api_key'] ?? ''));
        $secretKey = trim((string) ($data['secret_key'] ?? ''));
        $current = $this->model->getActiveConfig();
        if ($secretKey === '' && $current['secret_key'] !== '') {
            $secretKey = $current['secret_key'];
        }

        if (!in_array($environment, ['production', 'sandbox'], true)) {
            $errors[] = 'Selecciona un ambiente válido.';
        }
        if ($apiKey === '' || $secretKey === '') {
            $errors[] = 'ApiKey y SecretKey son obligatorias.';
        }

        if ($errors) {
            return ['errors' => $errors];
        }

        $this->model->saveConfig([
            'environment' => $environment,
            'api_key' => $apiKey,
            'secret_key' => $secretKey,
            'return_url_base' => $data['return_url_base'] ?? null,
            'confirmation_url_base' => $data['confirmation_url_base'] ?? null,
        ]);

        return ['success' => 'Configuración guardada correctamente.'];
    }

    public function testConfig(array $data): array
    {
        $apiKey = trim((string) ($data['api_key'] ?? ''));
        $secretKey = trim((string) ($data['secret_key'] ?? ''));
        if ($apiKey === '' || $secretKey === '') {
            return ['errors' => ['ApiKey y SecretKey son obligatorias para probar.']];
        }

        $dummyParams = [
            'apiKey' => $apiKey,
            'amount' => 1000,
            'currency' => 'CLP',
        ];
        $signature = (new FlowClient($this->model))->buildSignature($dummyParams, $secretKey);
        if ($signature === '') {
            return ['errors' => ['No fue posible generar la firma.']];
        }

        return ['success' => 'Firma generada correctamente. La configuración es válida localmente.'];
    }
}
