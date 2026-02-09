# Módulo Pagos Flow

## Variables de entorno
Configura (opcional) en el entorno del servidor:

- `FLOW_ENV`: `sandbox` o `production`.
- `FLOW_API_KEY`: API Key entregada por Flow.
- `FLOW_SECRET_KEY`: Secret Key entregada por Flow.

> Si guardas credenciales en la pantalla de **Configuración**, estas tendrán prioridad sobre las variables de entorno.

## URLs requeridas en Flow
- **urlConfirmation**: `https://tu-dominio/flow-webhook-confirmation.php`
- **urlReturn**: `https://tu-dominio/flow-payments-detail.php?local_order_id={tuOrden}`

## Firma (parámetro `s`)
1. Toma **todos** los parámetros del request excepto `s`.
2. Ordénalos alfabéticamente por nombre.
3. Concatena en un string: `nombreParametro + valor + nombreParametro + valor` (sin separadores).
4. Calcula `hash_hmac('sha256', stringConcatenado, secretKey)`.
5. Agrega el resultado como parámetro `s`.

### Ejemplo
Parámetros (ordenados):

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

## Instalación de base de datos
Ejecuta el script de actualización más reciente en `database/` para crear:

- `flow_config`
- `flow_orders`
- `flow_webhook_logs`

## Seguridad
- Las Secret Keys nunca se registran en logs ni en los campos de auditoría.
- El webhook es idempotente: el mismo token no se procesa dos veces.
