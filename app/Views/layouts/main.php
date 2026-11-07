<?php
$titulo = $titulo ?? 'Início';
$conteudo = $conteudo ?? '';
$quantidadeCarrinho = $quantidadeCarrinho ?? 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f23f75">
    <meta name="description" content="CupCake Gourmet - Cupcakes artesanais feitos com carinho">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <title><?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?> | CupCake Gourmet</title>
    <link rel="stylesheet" href="/css/style.css">
</head>

<body>

    <div class="barra-superior">
        Cupcakes artesanais feitos com carinho
        <span>•</span>
        Consulte as opções de entrega
    </div>

    <header class="topo">
        <div class="topo-barra">

            <button class="menu-toggle" type="button" aria-label="Abrir menu" aria-expanded="false" data-menu-toggle>
                ☰
            </button>

            <a class="logo" href="/">CupCake Gourmet</a>

            <nav class="menu" id="menu" aria-label="Menu principal">
                <a href="/">Início</a>
                <a href="/cardapio">Cardápio</a>
                <a href="/sobre">Sobre Nós</a>
                <a href="/carrinho">Carrinho</a>
            </nav>

            <a class="carrinho-link" href="/carrinho" aria-label="Carrinho de compras">
                <span aria-hidden="true">🛒</span>
                <span class="badge"><?= (int) $quantidadeCarrinho ?></span>
            </a>

        </div>
    </header>

    <main class="container">
        <?php if (!empty($mensagem)): ?>
            <div class="alerta alerta-sucesso" role="status">
                <?= htmlspecialchars($mensagem, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?= $conteudo ?>
    </main>

    <footer class="rodape">
        <div class="rodape-conteudo">
            <div class="rodape-marca">
                <h2>CupCake Gourmet</h2>
                <p>
                    Cupcakes artesanais preparados com carinho
                    para tornar seus momentos mais especiais.
                </p>
            </div>

            <div class="rodape-contato">
                <h3>Entre em contato</h3>
                <p>São Paulo</p>
                <p>(00) 00000-0000</p>
                <p>email.cup@gmail.com</p>
                <p>Instagram | WhatsApp</p>
            </div>
        </div>

        <div class="rodape-final">
            © <?= date('Y') ?> CupCake Gourmet - Todos os direitos reservados
        </div>
    </footer>

    <script src="/js/app.js" defer></script>
</body>

</html>