<?php

declare(strict_types=1);

class FlowClient
{
    private array $config;

    public function __construct(FlowConfigModel $configModel)
    {
        $this->config = $configModel->getActiveConfig();
    }

    public function buildSignature(array $params, string $secretKey): string
    {
        if (isset($params['s'])) {
            unset($params['s']);
        }

        ksort($params, SORT_STRING);
        $signatureString = '';
        foreach ($params as $key => $value) {
            $signatureString .= (string) $key . (string) $value;
        }

        return hash_hmac('sha256', $signatureString, $secretKey);
    }

    public function get(string $path, array $params): array
    {
        return $this->request('GET', $path, $params);
    }

    public function post(string $path, array $params): array
    {
        return $this->request('POST', $path, $params);
    }

    public function request(string $method, string $path, array $params): array
    {
        $config = $this->config;
        if (empty($config['api_key']) || empty($config['secret_key'])) {
            throw new RuntimeException('Configuración Flow incompleta.');
        }

        $params['apiKey'] = $config['api_key'];
        $params['s'] = $this->buildSignature($params, $config['secret_key']);

        $baseUrl = rtrim($config['base_url'], '/');
        $url = $baseUrl . '/' . ltrim($path, '/');

        $ch = curl_init();
        if ($ch === false) {
            throw new RuntimeException('No fue posible iniciar la conexión con Flow.');
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if (strtoupper($method) === 'GET') {
            $url .= '?' . http_build_query($params);
        } else {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/x-www-form-urlencoded'];
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        $options[CURLOPT_URL] = $url;

        curl_setopt_array($ch, $options);
        $body = curl_exec($ch);
        $error = null;
        if ($body === false) {
            $error = curl_error($ch);
        }
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            throw new RuntimeException('Error de red al conectar con Flow: ' . $error);
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            $decoded = ['raw' => $body];
        }

        if ($status !== 200) {
            $message = $decoded['message'] ?? 'Respuesta inválida de Flow.';
            throw new RuntimeException($message);
        }

        return $decoded;
    }
}
