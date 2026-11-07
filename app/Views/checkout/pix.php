<?php

declare(strict_types=1);
?>

<section class="checkout-pix-pagina">
    <div class="checkout-pix__card">
        <h1>Pagamento via PIX</h1>

        <p class="checkout-pix__description">
            Escaneie o QR Code demonstrativo ou utilize o código abaixo
            para visualizar o fluxo de pagamento simulado.
        </p>

        <div class="checkout-pix__qrcode">
            <div class="checkout-pix__pattern" aria-hidden="true"></div>
            <span>QR CODE DEMONSTRATIVO</span>
        </div>

        <p class="checkout-pix__warning">
            Este QR Code é ilustrativo e não permite realizar pagamentos reais.
        </p>

        <div class="checkout-pix__details">
            <p><strong>Pedido:</strong> #<?= (int) $id_pedido ?></p>
            <p><strong>Total:</strong> R$ <?= number_format((float) $total, 2, ',', '.') ?></p>
            <p><strong>Status:</strong> Aguardando pagamento</p>
        </div>

        <div class="checkout-pix__code">
            <label for="codigo-pix">Código PIX simulado</label>
            <textarea id="codigo-pix" readonly><?= htmlspecialchars((string) $codigo_pix, ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <form action="/checkout/confirmar-pix" method="POST">
            <input
                type="hidden"
                name="_csrf_token"
                value="<?= htmlspecialchars((string) $csrf_token, ENT_QUOTES, 'UTF-8') ?>">

            <button type="submit" class="btn btn-primary">
                Simular pagamento
            </button>
        </form>
    </div>
</section>