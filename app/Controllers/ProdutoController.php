<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Produto;
use App\Services\Csrf;
use App\Services\View;

final class ProdutoController
{
    public function mostrar(int $id): void
    {
        $produto = (new Produto())->buscarPorId($id);

        if ($produto === null) {
            http_response_code(404);

            View::render(
                'produto/nao-encontrado',
                [],
                'Produto não encontrado'
            );

            return;
        }

        $tokenCsrf = Csrf::gerarToken();

        View::render(
            'produto/detalhes',
            [
                'produto' => $produto,
                'csrf_token' => $tokenCsrf,
            ],
            $produto['nome']
        );
    }
}
