# Administración Municipal

## Inicio rápido

1. Crear la base de datos y cargar el esquema:
   ```bash
   mysql -u root -p -e "CREATE DATABASE municipio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   mysql -u root -p municipio < "base de datos/schema.sql"
   ```
2. Ajustar las variables de entorno si la conexión no es la predeterminada:
   ```bash
   export DB_HOST=127.0.0.1
   export DB_NAME=municipio
   export DB_USER=root
   export DB_PASS=""
   ```
3. Iniciar el servidor PHP:
   ```bash
   php -S 0.0.0.0:8000 -t .
   ```
4. Abrir `http://localhost:8000` (muestra primero el login).

## Usuario inicial (super user)

- **RUT:** `9.999.999-9`
- **Contraseña:** `SuperUser123!`

> Este usuario se inserta al ejecutar el esquema SQL incluido en `base de datos/schema.sql`.
