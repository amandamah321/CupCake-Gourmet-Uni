<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Carrinho;
use App\Models\ItemCarrinho;
use App\Services\CarrinhoCalculator;
use App\Services\CheckoutService;
use App\Services\Csrf;

use RuntimeException;

final class CheckoutController
{
    private Carrinho $carrinho;
    private ItemCarrinho $itemCarrinho;
    private CarrinhoCalculator $calculator;
    private CheckoutService $checkoutService;

    public function __construct()
    {
        $this->carrinho = new Carrinho();
        $this->itemCarrinho = new ItemCarrinho();
        $this->calculator = new CarrinhoCalculator();
        $this->checkoutService = new CheckoutService();
    }

    //Recuperar o carrinho da sessao atual
    private function obterIdCarrinho(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $idCarrinho = (int) ($_SESSION['id_carrinho'] ?? 0);

        if ($idCarrinho <= 0) {
            throw new RuntimeException('Carrinho nao encontrado na sessao.');
        }

        $carrinhoExistente = $this->carrinho->buscarPorId($idCarrinho);

        if (!$carrinhoExistente) {
            unset($_SESSION['id_carrinho']);

            throw new RuntimeException('Carrinho nao encontrado.');
        }

        return $idCarrinho;
    }

    //Exibir os dados do checkout
    public function index(): array
    {
        $idCarrinho = $this->obterIdCarrinho();

        $itens = $this->itemCarrinho->todosPorCarrinho($idCarrinho);

        if (empty($itens)) {
            throw new RuntimeException('O carrinho esta vazio.');
        }

        $resultado = $this->calculator->calcular($itens);

        $taxaEntrega = 12.00;

        $tokenCsrf = Csrf::gerarToken();

        return [
            'itens' => $resultado['itens'],
            'subtotal' => $resultado['total'],
            'taxa_entrega' => $taxaEntrega,
            'total' => round($resultado['total'] + $taxaEntrega, 2),
            'csrf_token' => $tokenCsrf,

        ];
    }

    //Finalizar a compra e processar o pagamento
    public function finalizar(array $dados): array
    {
        $token = $dados['_csrf_token'] ?? null;

        if (!is_string($token)) {
            throw new RuntimeException('Token de seguranca invalido.');
        }

        Csrf::validarToken($token);

        $idCarrinho = $this->obterIdCarrinho();

        $nome = trim((string) ($dados['nome'] ?? ''));
        $email = trim((string) ($dados['email'] ?? ''));

        $telefone = isset($dados['telefone'])
            ? trim((string) $dados['telefone'])
            : null;

        $metodo = strtoupper(trim(
            (string) ($dados['metodo'] ?? '')
        ));

        $cep = isset($dados['cep'])
            ? trim((string) $dados['cep'])
            : null;

        $comentario = isset($dados['comentario'])
            ? trim((string) $dados['comentario'])
            : null;

        $numeroCartao = isset($dados['numero_cartao'])
            ? trim((string) $dados['numero_cartao'])
            : null;

        $validade = isset($dados['validade'])
            ? trim((string) $dados['validade'])
            : null;

        $cvv = isset($dados['cvv'])
            ? trim((string) $dados['cvv'])
            : null;

        $resultado = $this->checkoutService->finalizar(
            $idCarrinho,
            $nome,
            $email,
            $telefone,
            $metodo,
            $cep,
            $comentario,
            $numeroCartao,
            $validade,
            $cvv
        );

        if ($resultado['sucesso']) {
            unset($_SESSION['id_carrinho']);
        }

        if (!empty($resultado['aguardando_pix'])) {
            $_SESSION['pedido_pix_pendente'] = (int) $resultado['id_pedido'];
            $_SESSION['carrinho_pix_pendente'] = (int) $_SESSION['id_carrinho'];
            $resultado['csrf_token'] = Csrf::gerarToken();
        }

        return $resultado;
    }

    //Confirmar o pagamento PIX
    public function confirmarPix(array $dados): array
    {
        $token = $dados['_csrf_token'] ?? null;

        if (!is_string($token)) {
            throw new RuntimeException('Token de seguranca invalido.');
        }

        Csrf::validarToken($token);

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $idPedido = (int) ($_SESSION['pedido_pix_pendente'] ?? 0);
        $idCarrinho = (int) ($_SESSION['carrinho_pix_pendente'] ?? 0);

        if ($idPedido <= 0 || $idCarrinho <= 0) {
            throw new RuntimeException('Pedido ou carrinho PIX pendente nao encontrado.');
        }

        $resultado = $this->checkoutService->confirmarPix(
            $idPedido,
            $idCarrinho
        );

        if ($resultado['sucesso']) {
            unset(
                $_SESSION['id_carrinho'],
                $_SESSION['pedido_pix_pendente'],
                $_SESSION['carrinho_pix_pendente']
            );
        }

        return $resultado;
    }
}
