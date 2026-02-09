# Módulo Pagos Flow

## Variables / Parametría
- Configura las credenciales desde **Pagos Flow > Configuración**.
- Campos principales:
  - Ambiente (production/sandbox)
  - ApiKey
  - SecretKey (se muestra enmascarado)
  - Base URL retorno (si no se define, usa `base_url()`)
  - Base URL confirmación (si no se define, usa `base_url()`)

## URLs que debes registrar en Flow
- **urlConfirmation**: `{BASE_URL_CONFIRMACION}/flow/webhook/confirmation.php`
- **urlReturn**: `{BASE_URL_RETORNO}/flow/payments/return.php?local_order_id={tu_orden}`

## Flujo end-to-end
1. Configura credenciales y ambiente en la pantalla de configuración.
2. Crea un pago en **Pagos Flow > Crear pago**.
3. Se crea la orden local y se obtiene el `payment_url`.
4. El usuario final paga en Flow.
5. Flow llama al webhook (`urlConfirmation`) con el `token`.
6. El sistema consulta `payment/getStatus`, actualiza la orden y marca el log como procesado.

## Firma (HMAC SHA256)
1. Recibe el array de parámetros **sin** `s`.
2. Ordena por nombre (`ksort`).
3. Concatena `key + value + key + value ...` (sin separadores).
4. Calcula `s = hash_hmac('sha256', string, secretKey)`.

### Ejemplo
Parámetros:

```
amount=1000
apiKey=ABC123
commerceOrder=ORD-99
currency=CLP
```

String concatenado:

```
amount1000apiKeyABC123commerceOrderORD-99currencyCLP
```

Firma:

```
hash_hmac('sha256', 'amount1000apiKeyABC123commerceOrderORD-99currencyCLP', 'SECRET_KEY')
```

## Logs de ejemplo
- Todas las respuestas se guardan como JSON en `flow_orders.raw_response` y `flow_orders.last_status_response`.
- Los eventos de webhook quedan en `flow_webhook_logs` (sin incluir secretos).
