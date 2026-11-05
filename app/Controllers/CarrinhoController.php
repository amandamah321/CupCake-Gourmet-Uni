<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Carrinho;
use App\Models\ItemCarrinho;
use App\Models\Produto;
use App\Services\CarrinhoCalculator;
use App\Services\Csrf;
use RuntimeException;


final class CarrinhoController
{
    private Carrinho $carrinho;
    private ItemCarrinho $itemCarrinho;
    private Produto $produto;
    private CarrinhoCalculator $calculator;

    public function __construct()
    {
        $this->carrinho = new Carrinho();
        $this->itemCarrinho = new ItemCarrinho();
        $this->produto = new Produto();
        $this->calculator = new CarrinhoCalculator();
    }

    //Recuperar ou criar o carrinho da sessao
    private function obterOuCriarCarrinho(): int
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (isset($_SESSION['id_carrinho'])) {
            $idCarrinho = (int) $_SESSION['id_carrinho'];

            $carrinhoExistente = $this->carrinho->buscarPorId($idCarrinho);

            if ($carrinhoExistente) {
                return $idCarrinho;
            }

            unset($_SESSION['id_carrinho']);
        }

        $idCarrinho = $this->carrinho->criar();

        $_SESSION['id_carrinho'] = $idCarrinho;

        return $idCarrinho;
    }

    //Listar os itens e calcular o total do carrinho
    public function index(): array
    {
        $idCarrinho = $this->obterOuCriarCarrinho();

        $itens = $this->itemCarrinho->todosPorCarrinho($idCarrinho);

        $resultado = $this->calculator->calcular($itens);

        $tokenCsrf = Csrf::gerarToken();

        return [
            'itens' => $resultado['itens'],
            'subtotal' => $resultado['total'],
            'taxa_entrega' => 12.00,
            'total' => round($resultado['total'] + 12.00, 2),
            'csrf_token' => $tokenCsrf,
        ];
    }

    //Adicionar um produto ao carrinho
    public function adicionar(
        int $idProduto,
        int $quantidade = 1,
        ?string $token = null
    ): void {
        Csrf::validarToken($token);

        if ($idProduto <= 0 || $quantidade <= 0) {
            throw new RuntimeException('Produto ou quantidade invalida.');
        }

        $produto = $this->produto->buscarPorId($idProduto);

        if (!$produto) {
            throw new RuntimeException('Produto nao encontrado ou indisponivel.');
        }

        $idCarrinho = $this->obterOuCriarCarrinho();

        $itemExistente = $this->itemCarrinho->buscarProduto(
            $idCarrinho,
            $idProduto
        );

        if ($itemExistente) {
            $novaQuantidade = (int) $itemExistente['quantidade'] + $quantidade;

            $this->itemCarrinho->atualizarQuantidade(
                (int) $itemExistente['id_item_carrinho'],
                $novaQuantidade
            );
        } else {
            $this->itemCarrinho->adicionar(
                $idCarrinho,
                $idProduto,
                $quantidade,
                (float) $produto['preco']
            );
        }

        $this->carrinho->atualizar($idCarrinho);
    }

    //Atualizar a quantidade de um item
    public function atualizar(
        int $idItem,
        int $quantidade,
        ?string $token = null
    ): void {
        Csrf::validarToken($token);

        if ($idItem <= 0 || $quantidade <= 0) {
            throw new RuntimeException('Item ou quantidade invalida.');
        }

        $idCarrinho = $this->obterOuCriarCarrinho();

        $itens = $this->itemCarrinho->todosPorCarrinho($idCarrinho);

        $itemPertenceAoCarrinho = false;

        foreach ($itens as $item) {
            if ((int) $item['id_item_carrinho'] === $idItem) {
                $itemPertenceAoCarrinho = true;
                break;
            }
        }

        if (!$itemPertenceAoCarrinho) {
            throw new RuntimeException('Item nao encontrado no carrinho.');
        }

        $this->itemCarrinho->atualizarQuantidade(
            $idItem,
            $quantidade
        );

        $this->carrinho->atualizar($idCarrinho);
    }

    //Remover um item do carrinho
    public function remover(
        int $idItem,
        ?string $token = null
    ): void {
        Csrf::validarToken($token);

        if ($idItem <= 0) {
            throw new RuntimeException('Item invalido.');
        }

        $idCarrinho = $this->obterOuCriarCarrinho();

        $itens = $this->itemCarrinho->todosPorCarrinho($idCarrinho);

        $itemPertenceAoCarrinho = false;

        foreach ($itens as $item) {
            if ((int) $item['id_item_carrinho'] === $idItem) {
                $itemPertenceAoCarrinho = true;
                break;
            }
        }

        if (!$itemPertenceAoCarrinho) {
            throw new RuntimeException('Item nao encontrado no carrinho.');
        }

        $this->itemCarrinho->remover($idItem);

        $this->carrinho->atualizar($idCarrinho);
    }
}
