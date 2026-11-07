<section class="hero">
    <div class="hero-conteudo">
        <h1>Cupcakes artesanais feitos com amor</h1>
        <p>Sabores especiais para momentos únicos.</p>
        <a class="btn" href="/cardapio">Ver produtos →</a>
    </div>
</section>

<h2>Produtos em Destaque</h2>

<div class="grade">
    <?php foreach (($destaques ?? []) as $produto): ?>
        <article class="card">
            <a href="/produto/<?= (int) $produto['id_produto'] ?>">
                <div class="card-img">
                    <img
                        src="<?= htmlspecialchars($produto['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </a>

            <div class="card-corpo">
                <h3><?= htmlspecialchars($produto['nome'], ENT_QUOTES, 'UTF-8') ?></h3>

                <p class="desc">
                    <?= htmlspecialchars($produto['descricao'], ENT_QUOTES, 'UTF-8') ?>
                </p>

                <p class="preco">
                    R$ <?= number_format((float) $produto['preco'], 2, ',', '.') ?>
                </p>

                <a
                    class="btn btn-bloco"
                    href="/produto/<?= (int) $produto['id_produto'] ?>">
                    Ver produto
                </a>
            </div>
        </article>
    <?php endforeach; ?>
</div>


<section class="secao-avaliacoes">
    <div class="avaliacoes-conteudo">
        <h2>Avaliações</h2>

        <div class="avaliacoes">
            <?php foreach (($avaliacoes ?? []) as $avaliacao): ?>
                <article class="avaliacao-card">
                    <div class="avaliacao-estrelas" aria-label="<?= (int) $avaliacao['estrelas'] ?> de 5 estrelas">
                        <?= str_repeat('★', (int) $avaliacao['estrelas']) ?><?= str_repeat('☆', 5 - (int) $avaliacao['estrelas']) ?>
                    </div>

                    <p class="avaliacao-comentario">
                        “<?= htmlspecialchars($avaliacao['comentario'] ?? '', ENT_QUOTES, 'UTF-8') ?>”
                    </p>

                    <div class="avaliacao-cliente">
                        — <?= htmlspecialchars($avaliacao['nome_exibicao'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
