<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Avaliacao;

final class SobreController
{
    private Avaliacao $avaliacao;

    public function __construct()
    {
        $this->avaliacao = new Avaliacao();
    }

    public function index(): array
    {
        return [
            'avaliacoes' => $this->avaliacao->recentes(3),
        ];
    }
}
