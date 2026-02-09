<?php

declare(strict_types=1);

class FlowWebhookController
{
    public function __construct(private FlowPaymentsController $paymentsController)
    {
    }

    public function confirmation(FlowClient $client, array $payload): array
    {
        $this->paymentsController->ensureTables();
        if ($this->paymentsController->getLastError() !== null) {
            return [
                'status' => 500,
                'message' => 'No fue posible preparar las tablas de Flow.',
            ];
        }
        $token = trim((string) ($payload['token'] ?? ''));

        if ($token === '') {
            return [
                'status' => 400,
                'message' => 'Token requerido.',
            ];
        }

        $alreadyProcessed = $this->hasProcessedToken($token);

        if ($alreadyProcessed) {
            $this->logWebhook($token, $payload, 'duplicate', 'Token ya procesado.');
            return [
                'status' => 200,
                'message' => 'OK',
            ];
        }

        $response = $this->paymentsController->updateStatusFromFlow($client, $token);
        if ($response['success']) {
            $this->logWebhook($token, $payload, 'processed', 'Estado actualizado correctamente.');
        } else {
            $this->logWebhook($token, $payload, 'failed', $response['error'] ?? 'Error al consultar Flow.');
        }

        return [
            'status' => 200,
            'message' => 'OK',
        ];
    }

    private function hasProcessedToken(string $token): bool
    {
        $stmt = db()->prepare('SELECT 1 FROM flow_webhook_logs WHERE token = ? AND status = "processed" LIMIT 1');
        $stmt->execute([$token]);

        return (bool) $stmt->fetchColumn();
    }

    private function logWebhook(string $token, array $payload, string $status, string $notes): void
    {
        $stmt = db()->prepare(
            'INSERT INTO flow_webhook_logs (token, payload, status, processed_at, notes) VALUES (?, ?, ?, NOW(), ?)'
        );
        $stmt->execute([
            $token,
            json_encode($payload, JSON_UNESCAPED_UNICODE),
            $status,
            $notes,
        ]);
    }
}
