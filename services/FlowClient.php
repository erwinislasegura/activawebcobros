<?php

declare(strict_types=1);

class FlowClient
{
    private string $apiKey;
    private string $secretKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $secretKey, string $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->secretKey = $secretKey;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function buildSignature(array $params): string
    {
        if (isset($params['s'])) {
            unset($params['s']);
        }

        ksort($params, SORT_STRING);
        $signatureString = '';
        foreach ($params as $key => $value) {
            $signatureString .= (string) $key . (string) $value;
        }

        return hash_hmac('sha256', $signatureString, $this->secretKey);
    }

    public function get(string $path, array $params): array
    {
        $paramsWithSignature = $this->withSignature($params);
        $url = $this->baseUrl . '/' . ltrim($path, '/');
        $url .= '?' . http_build_query($paramsWithSignature);

        return $this->sendRequest('GET', $url, $paramsWithSignature);
    }

    public function post(string $path, array $params): array
    {
        $paramsWithSignature = $this->withSignature($params);
        $url = $this->baseUrl . '/' . ltrim($path, '/');

        return $this->sendRequest('POST', $url, $paramsWithSignature);
    }

    private function withSignature(array $params): array
    {
        $signature = $this->buildSignature($params);
        $params['s'] = $signature;

        return $params;
    }

    private function sendRequest(string $method, string $url, array $params): array
    {
        $ch = curl_init();
        if ($ch === false) {
            return [
                'success' => false,
                'status' => 0,
                'error' => 'No fue posible iniciar la conexión HTTP.',
                'data' => null,
            ];
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_HTTPHEADER] = ['Content-Type: application/x-www-form-urlencoded'];
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        $error = null;
        if ($body === false) {
            $error = curl_error($ch);
        }
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false) {
            return [
                'success' => false,
                'status' => $status,
                'error' => $error ?: 'Error desconocido de red.',
                'data' => null,
            ];
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            $decoded = ['raw' => $body];
        }

        if ($status !== 200) {
            return [
                'success' => false,
                'status' => $status,
                'error' => $decoded['message'] ?? 'Respuesta inválida de Flow.',
                'data' => $decoded,
            ];
        }

        return [
            'success' => true,
            'status' => $status,
            'error' => null,
            'data' => $decoded,
        ];
    }
}
