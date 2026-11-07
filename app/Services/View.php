<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class View
{
    public static function render(
        string $view,
        array $dados = [],
        string $titulo = 'Início',
        string $mensagem = ''
    ): void {
        $caminhoView = dirname(__DIR__) . '/Views/' . $view . '.php';
        $caminhoLayout = dirname(__DIR__) . '/Views/layouts/main.php';

        if (!is_file($caminhoView)) {
            throw new RuntimeException('Página não encontrada.');
        }

        extract($dados, EXTR_SKIP);

        ob_start();
        require $caminhoView;
        $conteudo = ob_get_clean();

        require $caminhoLayout;
    }
}