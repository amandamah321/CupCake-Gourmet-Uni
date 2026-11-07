<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

if (PHP_SAPI === 'cli-server') {
    $caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $arquivo = __DIR__ . $caminho;

    if (is_file($arquivo)) {
        return false;
    }
}

$rotas = require dirname(__DIR__) . '/routes/web.php';

$metodo = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$caminho = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

try {
    $acao = $rotas[$metodo][$caminho] ?? null;

    if ($acao === null) {
        http_response_code(404);
        echo 'Página não encontrada.';
        exit;
    }

    $acao();
} catch (Throwable $erro) {
    http_response_code(500);

    $config = require dirname(__DIR__) . '/config/bootstrap.php';

    if ($config['app']['debug']) {
        echo '<pre>';
        echo htmlspecialchars($erro->getMessage(), ENT_QUOTES, 'UTF-8');
        echo '</pre>';
    } else {
        echo 'Ocorreu um erro ao carregar a página.';
    }
}
