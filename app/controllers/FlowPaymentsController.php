<?php

declare(strict_types=1);

class FlowPaymentsController
{
    public function __construct(
        private FlowOrderModel $orderModel,
        private FlowConfigModel $configModel,
        private FlowClient $client
    ) {
    }

    public function createPayment(array $data): array
    {
        $errors = $this->validatePayment($data);
        if ($errors) {
            return ['errors' => $errors];
        }

        $config = $this->configModel->getActiveConfig();
        $baseReturn = $config['return_url_base'] ?: base_url();
        $baseConfirm = $config['confirmation_url_base'] ?: base_url();

        $payload = [
            'commerceOrder' => $data['local_order_id'],
            'subject' => $data['subject'],
            'amount' => (int) $data['amount'],
            'currency' => $data['currency'],
            'email' => $data['customer_email'],
            'urlReturn' => rtrim($baseReturn, '/') . '/flow/payments/return.php?local_order_id=' . urlencode($data['local_order_id']),
            'urlConfirmation' => rtrim($baseConfirm, '/') . '/flow/webhook/confirmation.php',
        ];

        $response = $this->client->post('payment/create', $payload);

        $orderId = $this->orderModel->create([
            'local_order_id' => $data['local_order_id'],
            'subject' => $data['subject'],
            'amount' => (int) $data['amount'],
            'currency' => $data['currency'],
            'customer_email' => $data['customer_email'],
            'flow_token' => $response['token'] ?? null,
            'flow_order' => $response['flowOrder'] ?? null,
            'status' => 'pending',
            'status_detail' => null,
            'payment_url' => $response['url'] ?? null,
            'raw_request' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'raw_response' => json_encode($response, JSON_UNESCAPED_UNICODE),
        ]);

        return [
            'order_id' => $orderId,
            'payment_url' => isset($response['url'], $response['token']) ? $response['url'] . '?token=' . $response['token'] : null,
            'response' => $response,
        ];
    }

    public function listOrders(array $filters): array
    {
        return $this->orderModel->list($filters);
    }

    public function getOrder(int $id): ?array
    {
        return $this->orderModel->findById($id);
    }

    public function refreshStatus(int $orderId, string $token): array
    {
        $response = $this->client->get('payment/getStatus', [
            'token' => $token,
        ]);

        $status = $this->mapStatus($response);
        $this->orderModel->updateStatusById($orderId, [
            'status' => $status,
            'status_detail' => $response['status'] ?? null,
            'last_status_response' => json_encode($response, JSON_UNESCAPED_UNICODE),
        ]);

        return $response;
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

    private function validatePayment(array $data): array
    {
        $errors = [];
        $amount = (int) ($data['amount'] ?? 0);
        if ($amount <= 0) {
            $errors[] = 'El monto debe ser mayor a cero.';
        }
        if (trim((string) ($data['local_order_id'] ?? '')) === '') {
            $errors[] = 'La orden local es obligatoria.';
        }
        if (trim((string) ($data['subject'] ?? '')) === '') {
            $errors[] = 'La descripción es obligatoria.';
        }
        $email = trim((string) ($data['customer_email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email ingresado no es válido.';
        }

        return $errors;
    }
}
