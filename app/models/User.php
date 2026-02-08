<?php

declare(strict_types=1);

class User
{
    public static function findByRut(PDO $pdo, string $rut): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE rut = :rut LIMIT 1');
        $stmt->execute(['rut' => $rut]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function create(PDO $pdo, array $data): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO users (nombre, apellido, cargo, fecha_nacimiento, rut, correo, telefono, username, rol, password_hash, estado)
             VALUES (:nombre, :apellido, :cargo, :fecha_nacimiento, :rut, :correo, :telefono, :username, :rol, :password_hash, :estado)'
        );

        $stmt->execute([
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'cargo' => $data['cargo'],
            'fecha_nacimiento' => $data['fecha_nacimiento'],
            'rut' => $data['rut'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'],
            'username' => $data['username'],
            'rol' => $data['rol'],
            'password_hash' => $data['password_hash'],
            'estado' => $data['estado'] ?? 0,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function findByCorreo(PDO $pdo, string $correo): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE correo = :correo LIMIT 1');
        $stmt->execute(['correo' => $correo]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public static function findByUsername(PDO $pdo, string $username): ?array
    {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }
}
