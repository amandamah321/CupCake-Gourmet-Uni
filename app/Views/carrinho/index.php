<?php
$formatarPreco = static function ($preco): string {
    return 'R$ ' . number_format((float) $preco, 2, ',', '.');
};
?>

<section class="carrinho">
    <div class="carrinho-intro">
        <h1>Meu Carrinho</h1>
        <p>Confira seus cupcakes antes de finalizar o pedido.</p>
    </div>

    <?php if (empty($itens)): ?>

        <div class="carrinho-vazio">
            <div class="carrinho-vazio-icone">♡</div>

            <h2>Seu carrinho está vazio</h2>

            <p>
                Você ainda não adicionou nenhum cupcake.
                Explore nosso cardápio e escolha suas delícias favoritas!
            </p>

            <a href="/cardapio" class="btn btn-bloco">
                Explorar cardápio
            </a>
        </div>

    <?php else: ?>

        <div class="carrinho-conteudo">
            <div class="carrinho-produtos">

                <?php foreach ($itens as $item): ?>
                    <article class="carrinho-item">

                        <img
                            src="<?= htmlspecialchars($item['imagem_url'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="carrinho-item-info">
                            <h2><?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?></h2>

                            <p><?= $formatarPreco($item['preco_unitario']) ?> por unidade</p>

                            <strong>
                                <?= $formatarPreco($item['subtotal']) ?>
                            </strong>
                        </div>

                        <div class="carrinho-item-acoes">

                            <form method="POST" action="/carrinho/atualizar">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id_item_carrinho" value="<?= (int) $item['id_item_carrinho'] ?>">

                                <label for="quantidade-<?= (int) $item['id_item_carrinho'] ?>">
                                    Quantidade
                                </label>

                                <input
                                    type="number"
                                    id="quantidade-<?= (int) $item['id_item_carrinho'] ?>"
                                    name="quantidade"
                                    value="<?= (int) $item['quantidade'] ?>"
                                    min="1"
                                    max="20"
                                    required>

                                <button type="submit" class="btn">
                                    Atualizar
                                </button>
                            </form>

                            <form method="POST" action="/carrinho/remover">
                                <input type="hidden" name="_csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="id_item_carrinho" value="<?= (int) $item['id_item_carrinho'] ?>">

                                <button type="submit" class="carrinho-remover">
                                    Remover
                                </button>
                            </form>

                        </div>
                    </article>
                <?php endforeach; ?>

            </div>

            <aside class="carrinho-resumo">
                <h2>Resumo do pedido</h2>

                <div class="carrinho-resumo-linha">
                    <span>Subtotal</span>
                    <span><?= $formatarPreco($subtotal) ?></span>
                </div>

                <div class="carrinho-resumo-linha">
                    <span>Entrega</span>
                    <span><?= $formatarPreco($taxa_entrega) ?></span>
                </div>

                <div class="carrinho-resumo-total">
                    <span>Total</span>
                    <strong><?= $formatarPreco($total) ?></strong>
                </div>

                <a href="/checkout" class="btn btn-bloco">
                    Finalizar pedido
                </a>

                <a href="/cardapio" class="carrinho-continuar">
                    Continuar comprando
                </a>
            </aside>
        </div>

    <?php endif; ?>
</section>