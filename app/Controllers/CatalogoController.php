<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Categoria;
use App\Models\Produto;
use App\Services\View;
use App\Services\Csrf;

final class CatalogoController
{
    private Produto $produto;
    private Categoria $categoria;

    public function __construct()
    {
        $this->produto = new Produto();
        $this->categoria = new Categoria();
    }

    //Carregar os produtos e as categorias do catalogo
    public function index(): void
    {
        $categorias = $this->categoria->todas();
        $idCategoria = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $mensagem = $_SESSION['mensagem'] ?? '';
        unset($_SESSION['mensagem']);

        if ($idCategoria !== null && $idCategoria !== false && $idCategoria > 0) {
            $categoriaSelecionada = $this->categoria->buscarPorId($idCategoria);

            if ($categoriaSelecionada === null) {
                http_response_code(404);
                echo 'Categoria não encontrada.';
                return;
            }

            $produtos = $this->produto->porCategoria($idCategoria);
        } else {
            $categoriaSelecionada = null;
            $produtos = $this->produto->todos();
        }

        $tokenCsrf = Csrf::gerarToken();

        View::render('catalogo/index', [
            'categorias' => $categorias,
            'produtos' => $produtos,
            'categoriaSelecionada' => $categoriaSelecionada,
            'csrf_token' => $tokenCsrf,
            'mensagem' => $mensagem,
        ], 'Cardápio');
    }
}
