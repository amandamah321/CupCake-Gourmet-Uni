<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class Csrf
{
    //Iniciar a sessao
    private static function iniciarSessao(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    //Gerar ou recuperar o token
    public static function gerarToken(): string
    {
        self::iniciarSessao();

        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    //Validar o token recebido
    public static function validarToken(?string $token): void
    {
        self::iniciarSessao();

        if (
            empty($token) ||
            empty($_SESSION['_csrf_token']) ||
            !hash_equals($_SESSION['_csrf_token'], $token)
        ) {
            throw new RuntimeException('Token de seguranca invalido.');
        }
    }
}
