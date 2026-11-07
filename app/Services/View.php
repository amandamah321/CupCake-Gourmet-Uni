<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ItemCarrinho;
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

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $quantidadeCarrinho = 0;

        if (isset($_SESSION['id_carrinho'])) {
            $quantidadeCarrinho = (new ItemCarrinho())
                ->quantidadeTotalPorCarrinho((int) $_SESSION['id_carrinho']);
        }

        extract($dados, EXTR_SKIP);

        ob_start();
        require $caminhoView;
        $conteudo = ob_get_clean();

        require $caminhoLayout;
    }
}
