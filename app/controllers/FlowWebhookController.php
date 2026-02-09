<?php

declare(strict_types=1);

class FlowWebhookController
{
    public function __construct(
        private FlowOrderModel $orderModel,
        private FlowWebhookLogModel $logModel,
        private FlowClient $client
    ) {
    }

    public function confirmation(array $payload): array
    {
        $token = trim((string) ($payload['token'] ?? ''));
        if ($token === '') {
            return ['status' => 400, 'message' => 'Token requerido.'];
        }

        $existing = $this->logModel->findByToken($token);
        if ($existing && (int) $existing['processed'] === 1) {
            return ['status' => 200, 'message' => 'OK'];
        }

        if (!$existing) {
            $this->logModel->create($token, $payload, 'Recibido', false);
        }

        $response = $this->client->get('payment/getStatus', [
            'token' => $token,
        ]);

        $status = $this->mapStatus($response);
        $this->orderModel->updateStatusByToken($token, [
            'status' => $status,
            'status_detail' => $response['status'] ?? null,
            'last_status_response' => json_encode($response, JSON_UNESCAPED_UNICODE),
        ]);

        $this->logModel->markProcessed($token, 'Estado actualizado');

        return ['status' => 200, 'message' => 'OK'];
    }

    private function mapStatus(array $response): string
    {
        $status = strtolower((string) ($response['status'] ?? 'unknown'));
        return match ($status) {
            'paid', 'approved' => 'paid',
            'rejected' => 'rejected',
            'expired' => 'expired',
            'pending' => 'pending',
            'failed' => 'failed',
            default => 'unknown',
        };
    }
}
