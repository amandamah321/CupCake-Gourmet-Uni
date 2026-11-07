<?php
$formatarPreco = static function ($preco): string {
    return 'R$ ' . number_format((float) $preco, 2, ',', '.');
};

$sucesso = isset($sucesso) ? (bool) $sucesso : false;
?>

<section class="checkout">
    <div class="checkout-resultado">

        <?php if ($sucesso): ?>

            <div class="checkout-resultado-icone">✓</div>

            <h1>Pedido confirmado!</h1>

            <p>
                Seu pedido foi registrado com sucesso.
                Agradecemos por escolher a CupCake Gourmet!
            </p>

            <div class="checkout-resultado-detalhes">

                <div>
                    <span>Número do pedido</span>
                    <strong>#<?= (int) $id_pedido ?></strong>
                </div>

                <div>
                    <span>Forma de pagamento</span>
                    <strong><?= htmlspecialchars($metodo, ENT_QUOTES, 'UTF-8') ?></strong>
                </div>

                <div>
                    <span>Total pago</span>
                    <strong><?= $formatarPreco($total) ?></strong>
                </div>

                <div>
                    <span>Referência</span>
                    <strong>
                        <?= htmlspecialchars($referencia ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                </div>

            </div>

            <?php if (!empty($codigo_pix)): ?>
                <div class="checkout-pix">
                    <h2>Código PIX demonstrativo</h2>

                    <p>
                        <?= htmlspecialchars($codigo_pix, ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <small>
                        Este código é fictício e não permite realizar pagamentos reais.
                    </small>
                </div>
            <?php endif; ?>

        <?php else: ?>

            <div class="checkout-resultado-icone checkout-resultado-erro">!</div>

            <h1>Pagamento não aprovado</h1>

            <p>
                <?= htmlspecialchars($mensagem ?? 'Não foi possível concluir o pagamento.', ENT_QUOTES, 'UTF-8') ?>
            </p>

            <p>Seu carrinho continua disponível para tentar novamente.</p>

            <a href="/checkout" class="btn">
                Tentar novamente
            </a>

        <?php endif; ?>

        <a href="/cardapio" class="btn btn-bloco">
            Voltar ao cardápio
        </a>

    </div>
</section>