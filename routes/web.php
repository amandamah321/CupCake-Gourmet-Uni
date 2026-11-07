<?php

declare(strict_types=1);

use App\Controllers\CarrinhoController;
use App\Controllers\CatalogoController;
use App\Controllers\HomeController;
use App\Services\View;

return [
    'GET' => [
        '/' => function (): void {
            $dados = (new HomeController())->index();

            View::render('home/index', $dados, 'Início');
        },

        '/cardapio' => function (): void {
            (new CatalogoController())->index();
        },

        '/carrinho' => function (): void {
            $dados = (new CarrinhoController())->index();

            View::render('carrinho/index', $dados, 'Meu Carrinho');
        },
    ],

    'POST' => [
        '/carrinho/adicionar' => function (): void {
            $idProduto = filter_input(INPUT_POST, 'id_produto', FILTER_VALIDATE_INT);
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
            $token = $_POST['_csrf_token'] ?? null;

            if (!is_string($token)) {
                throw new RuntimeException('Token de segurança inválido.');
            }

            (new CarrinhoController())->adicionar(
                $idProduto !== false && $idProduto !== null ? $idProduto : 0,
                $quantidade !== false && $quantidade !== null ? $quantidade : 1,
                $token
            );

            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_start();
            }

            $_SESSION['mensagem'] = 'Produto adicionado ao carrinho com sucesso!';

            header('Location: /cardapio');
            exit;
        },

        '/carrinho/atualizar' => function (): void {
            $idItem = filter_input(INPUT_POST, 'id_item_carrinho', FILTER_VALIDATE_INT);
            $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);
            $token = $_POST['_csrf_token'] ?? null;

            if (!is_string($token)) {
                throw new RuntimeException('Token de segurança inválido.');
            }

            (new CarrinhoController())->atualizar(
                $idItem !== false && $idItem !== null ? $idItem : 0,
                $quantidade !== false && $quantidade !== null ? $quantidade : 0,
                $token
            );

            $_SESSION['mensagem'] = 'Carrinho atualizado com sucesso!';

            header('Location: /carrinho');
            exit;
        },

        '/carrinho/remover' => function (): void {
            $idItem = filter_input(INPUT_POST, 'id_item_carrinho', FILTER_VALIDATE_INT);
            $token = $_POST['_csrf_token'] ?? null;

            if (!is_string($token)) {
                throw new RuntimeException('Token de segurança inválido.');
            }

            (new CarrinhoController())->remover(
                $idItem !== false && $idItem !== null ? $idItem : 0,
                $token
            );

            $_SESSION['mensagem'] = 'Produto removido do carrinho!';

            header('Location: /carrinho');
            exit;
        },
    ],
];
