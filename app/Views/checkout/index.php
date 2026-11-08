<?php
$formatarPreco = static function ($preco): string {
    return 'R$ ' . number_format((float) $preco, 2, ',', '.');
};
?>

<section class="checkout">
    <div class="checkout-intro">
        <h1>Finalizar pedido</h1>
        <p>Estamos quase lá! Preencha seus dados para concluir sua compra.</p>
    </div>

    <form method="POST" action="/checkout/finalizar" class="checkout-form">

        <input
            type="hidden"
            name="_csrf_token"
            value="<?= htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8') ?>">

        <div class="checkout-conteudo">

            <div class="checkout-dados">

                <section class="checkout-card">
                    <h2>Seus dados</h2>

                    <div class="checkout-campo">
                        <label for="nome">Nome completo *</label>
                        <input
                            type="text"
                            id="nome"
                            name="nome"
                            maxlength="120"
                            autocomplete="name"
                            required>
                    </div>

                    <div class="checkout-campo">
                        <label for="email">E-mail *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            maxlength="150"
                            autocomplete="email"
                            required>
                    </div>

                    <div class="checkout-campo">
                        <label for="telefone">Telefone</label>
                        <input
                            type="tel"
                            id="telefone"
                            name="telefone"
                            maxlength="30"
                            autocomplete="tel">
                    </div>
                </section>

                <section class="checkout-card">
                    <h2>Informações de entrega</h2>

                    <div class="checkout-campo">
                        <label for="cep">CEP</label>
                        <input
                            type="text"
                            id="cep"
                            name="cep"
                            maxlength="9"
                            placeholder="00000-000"
                            autocomplete="postal-code"
                            data-cep>
                    </div>

                    <div class="checkout-campo">
                        <label for="comentario">Observações do pedido</label>
                        <textarea
                            id="comentario"
                            name="comentario"
                            rows="3"
                            placeholder="Alguma informação adicional?"></textarea>
                    </div>
                </section>

                <section class="checkout-card">
                    <h2>Forma de pagamento</h2>

                    <div class="checkout-pagamentos">

                        <label class="checkout-pagamento-opcao">
                            <input
                                type="radio"
                                name="metodo"
                                value="PIX"
                                data-metodo-pagamento
                                checked>
                            <span>
                                <strong>PIX</strong>
                                <small>Pagamento instantâneo</small>
                            </span>
                        </label>

                        <label class="checkout-pagamento-opcao">
                            <input
                                type="radio"
                                name="metodo"
                                value="CARTAO"
                                data-metodo-pagamento>
                            <span>
                                <strong>Cartão</strong>
                                <small>Crédito ou débito</small>
                            </span>
                        </label>

                    </div>

                    <div class="checkout-cartao" data-campos-cartao hidden>

                        <div class="checkout-campo">
                            <label for="numero_cartao">Número do cartão</label>
                            <input
                                type="text"
                                id="numero_cartao"
                                name="numero_cartao"
                                inputmode="numeric"
                                maxlength="19"
                                autocomplete="off"
                                disabled>
                        </div>

                        <div class="checkout-cartao-detalhes">

                            <div class="checkout-campo">
                                <label for="validade">Validade (MM/AA)</label>
                                <input
                                    type="text"
                                    id="validade"
                                    name="validade"
                                    placeholder="MM/AA"
                                    maxlength="5"
                                    autocomplete="off"
                                    disabled>
                            </div>

                            <div class="checkout-campo">
                                <label for="cvv">CVV</label>
                                <input
                                    type="password"
                                    id="cvv"
                                    name="cvv"
                                    inputmode="numeric"
                                    maxlength="4"
                                    autocomplete="off"
                                    disabled>
                            </div>

                        </div>

                        <p class="checkout-aviso">
                            Pagamento demonstrativo. Nenhuma cobrança real será realizada.
                        </p>

                    </div>
                </section>

            </div>

            <aside class="checkout-resumo">
                <h2>Resumo do pedido</h2>

                <div class="checkout-resumo-produtos">

                    <?php foreach ($itens as $item): ?>
                        <div class="checkout-resumo-item">

                            <div>
                                <strong>
                                    <?= htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8') ?>
                                </strong>

                                <small>
                                    <?= (int) $item['quantidade'] ?> unidade(s)
                                </small>
                            </div>

                            <span>
                                <?= $formatarPreco($item['subtotal']) ?>
                            </span>

                        </div>
                    <?php endforeach; ?>

                </div>

                <div class="checkout-resumo-linha">
                    <span>Subtotal</span>
                    <span><?= $formatarPreco($subtotal) ?></span>
                </div>

                <div class="checkout-resumo-linha" id="linha-entrega" hidden>
                    <span>Entrega</span>
                    <span><?= $formatarPreco($taxa_entrega) ?></span>
                </div>
                <div class="checkout-resumo-total">
                    <span>Total</span>
                    <strong id="valor-total" data-subtotal="<?= htmlspecialchars((string) $subtotal) ?>">
                        <?= $formatarPreco($subtotal) ?>
                    </strong>
                </div>

                <button type="submit" class="btn btn-bloco">
                    Confirmar pedido
                </button>

                <a href="/carrinho" class="carrinho-continuar">
                    Voltar ao carrinho
                </a>

            </aside>

        </div>

    </form>
</section>