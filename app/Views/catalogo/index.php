<?php
$formatarPreco = static function ($preco): string {
    return 'R$ ' . number_format((float) $preco, 2, ',', '.');
};
?>

<section class="catalogo">
    <div class="catalogo-intro">
        <h1>Nosso Cardápio</h1>
        <p>Descubra nossos cupcakes artesanais, preparados com ingredientes selecionados e muito carinho.</p>
    </div>

    <nav class="catalogo-filtros" aria-label="Filtrar produtos por categoria">
        <a
            href="/cardapio"
            class="<?= $categoriaSelecionada === null ? 'ativo' : '' ?>"
        >
            Todos
        </a>

        <?php foreach ($categorias as $categoria): ?>
            <a
                href="/cardapio?categoria=<?= (int) $categoria['id_categoria'] ?>"
                class="<?= $categoriaSelecionada !== null && (int) $categoriaSelecionada['id_categoria'] === (int) $categoria['id_categoria'] ? 'ativo' : '' ?>"
            >
                <?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <?php if (empty($produtos)): ?>
        <p class="catalogo-vazio">Nenhum produto encontrado nesta categoria.</p>
    <?php else: ?>

        <?php foreach ($categorias as $categoria): ?>
            <?php
            if (
                $categoriaSelecionada !== null &&
                (int) $categoriaSelecionada['id_categoria'] !== (int) $categoria['id_categoria']
            ) {
                continue;
            }

            $produtosCategoria = array_filter(
                $produtos,
                static fn($produto) =>
                    (int) $produto['id_categoria'] === (int) $categoria['id_categoria']
            );

            if (empty($produtosCategoria)) {
                continue;
            }
            ?>

            <section class="catalogo-categoria">
                <h2><?= htmlspecialchars($categoria['nome'], ENT_QUOTES, 'UTF-8') ?></h2>

                <div class="catalogo-grade">
                    <?php foreach ($produtosCategoria as $produto): ?>
                        <article class="catalogo-card">
                            <img
                                src="<?= htmlspecialchars($produto['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>"
                                class="catalogo-imagem"
                            >

                            <div class="catalogo-card-conteudo">
                                <h3><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h3>

                                <p class="catalogo-descricao">
                                    <?= htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8') ?>
                                </p>

                                <strong class="catalogo-preco">
                                    <?= $formatarPreco($produto['preco']) ?>
                                </strong>

                                <form method="POST" action="/carrinho/adicionar">
                                    <input
                                        type="hidden"
                                        name="_csrf_token"
                                        value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>"
                                    >

                                    <input
                                        type="hidden"
                                        name="id_produto"
                                        value="<?= (int) $produto['id_produto'] ?>"
                                    >

                                    <input type="hidden" name="quantidade" value="1">

                                    <button type="submit" class="btn btn-bloco">
                                        Adicionar ao carrinho
                                    </button>
                                </form>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endforeach; ?>

    <?php endif; ?>
</section>
