<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Services\View;

return [
    'GET' => [
        '/' => function (): void {
            $dados = (new HomeController())->index();

            View::render('home/index', $dados, 'Início');
        },
    ],
];
