<?php
$formatarPreco = static function ($preco): string {
    return 'R$ ' . number_format((float) $preco, 2, ',', '.');
};
?>

<section class="produto-detalhes">
    <a href="/cardapio" class="produto-voltar">
        &larr; Voltar ao cardápio
    </a>

    <div class="produto-detalhes-conteudo">
        <div class="produto-detalhes-imagem">
            <img
                src="<?= htmlspecialchars($produto['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="produto-detalhes-info">
            <span class="produto-detalhes-categoria">
                <?= htmlspecialchars($produto['categoria'], ENT_QUOTES, 'UTF-8') ?>
            </span>

            <h1><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h1>

            <p class="produto-detalhes-descricao">
                <?= htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8') ?>
            </p>

            <strong class="produto-detalhes-preco">
                <?= $formatarPreco($produto['preco']) ?>
            </strong>

            <form method="POST" action="/carrinho/adicionar" class="produto-detalhes-form">
                <input
                    type="hidden"
                    name="_csrf_token"
                    value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

                <input
                    type="hidden"
                    name="id_produto"
                    value="<?= (int) $produto['id_produto'] ?>">

                <label for="quantidade">Quantidade</label>

                <input
                    type="number"
                    id="quantidade"
                    name="quantidade"
                    value="1"
                    min="1"
                    max="20"
                    required>

                <button type="submit" class="btn btn-bloco">
                    Adicionar ao carrinho
                </button>
            </form>
        </div>
    </div>
</section>