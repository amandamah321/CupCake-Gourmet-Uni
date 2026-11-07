<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Avaliacao;
use App\Models\Produto;

final class HomeController
{
    private Produto $produto;
    private Avaliacao $avaliacao;

    public function __construct()
    {
        $this->produto = new Produto();
        $this->avaliacao = new Avaliacao();
    }

    //Carregar os produtos e as avaliacoes da pagina inicial
    public function index(): array
    {
        $produtos = $this->produto->todos();

        return [
            'produtos' => $produtos,
            'destaques' => array_slice($produtos, 0, 3),
            'avaliacoes' => $this->avaliacao->recentes(),
        ];
    }
}