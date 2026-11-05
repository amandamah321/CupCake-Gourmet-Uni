<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Carrinho;
use App\Models\Cliente;
use App\Models\ItemCarrinho;
use App\Models\ItemPedido;
use App\Models\Pagamento;
use App\Models\Pedido;
use PDO;
use RuntimeException;
use Throwable;

final class CheckoutService
{
    private PDO $db;

    private Carrinho $carrinho;
    private Cliente $cliente;
    private ItemCarrinho $itemCarrinho;
    private ItemPedido $itemPedido;
    private Pagamento $pagamento;
    private Pedido $pedido;

    private CarrinhoCalculator $calculator;
    private GatewaySimulado $gateway;
    //taxa de entrega sempre 12
    private const TAXA_ENTREGA = 12.00;

    public function __construct()
    {
        $this->db = Database::getConnection();

        $this->carrinho = new Carrinho();
        $this->cliente = new Cliente();
        $this->itemCarrinho = new ItemCarrinho();
        $this->itemPedido = new ItemPedido();
        $this->pagamento = new Pagamento();
        $this->pedido = new Pedido();

        $this->calculator = new CarrinhoCalculator();
        $this->gateway = new GatewaySimulado();
    }

    //Finalizar a compra e pagamento SIMULADO
    public function finalizar(
        int $idCarrinho,
        string $nome,
        string $email,
        ?string $telefone,
        string $metodo,
        ?string $cep = null,
        ?string $comentario = null,
        ?string $numeroCartao = null,
        ?string $validade = null,
        ?string $cvv = null
    ): array {
        $nome = trim($nome);
        $email = trim($email);
        $telefone = $telefone !== null ? trim($telefone) : null;
        $telefone = $telefone === '' ? null : $telefone;

        if (
            $idCarrinho <= 0 ||
            $nome === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL)
        ) {
            throw new RuntimeException(
                'Dados do cliente ou carrinho inválidos.'
            );
        }

        if (!in_array($metodo, ['CARTAO', 'PIX'], true)) {
            throw new RuntimeException(
                'Método de pagamento inválido.'
            );
        }

        if ($metodo === 'CARTAO') {
            if (
                $numeroCartao === null ||
                $validade === null ||
                $cvv === null
            ) {
                throw new RuntimeException(
                    'Informe os dados do cartão.'
                );
            }
        }

        $this->db->beginTransaction();

        try {
            // Verifica se o carrinho existe.
            $carrinhoExistente = $this->carrinho->buscarPorId(
                $idCarrinho
            );

            if (!$carrinhoExistente) {
                throw new RuntimeException(
                    'Carrinho não encontrado.'
                );
            }

            // Recuperar os produtos do carrinho
            $itens = $this->itemCarrinho->todosPorCarrinho(
                $idCarrinho
            );

            if (empty($itens)) {
                throw new RuntimeException(
                    'O carrinho está vazio.'
                );
            }

            // Calcular os produtos e a taxa de entrega
            $resultadoCalculo = $this->calculator->calcular($itens);

            $subtotal = $resultadoCalculo['total'];
            $total = round(
                $subtotal + self::TAXA_ENTREGA,
                2
            );

            // Buscar o cliente pelo e-mail
            $clienteExistente = $this->cliente->buscarPorEmail(
                $email
            );

            if ($clienteExistente) {
                $idCliente = (int) $clienteExistente['id_cliente'];
            } else {
                $idCliente = $this->cliente->criar(
                    $nome,
                    $email,
                    $telefone
                );
            }

            // Criar o pedido
            $idPedido = $this->pedido->criar(
                $idCliente,
                $total,
                $cep,
                $comentario
            );

            //Registrar os produtos do pedido
            foreach ($itens as $item) {
                $this->itemPedido->adicionar(
                    $idPedido,
                    (int) $item['id_produto'],
                    $item['nome'],
                    (int) $item['quantidade'],
                    (float) $item['preco_unitario']
                );
            }

            //Processar o pagamento simulado
            if ($metodo === 'CARTAO') {
                $resultadoPagamento = $this->gateway->pagarComCartao(
                    $numeroCartao,
                    $validade,
                    $cvv
                );
            } else {
                $resultadoPagamento = $this->gateway->pagarComPix();
            }

            //Quando transicao recusada, reverter toda a operação
            if (!$resultadoPagamento['aprovado']) {
                $this->db->rollBack();

                return [
                    'sucesso' => false,
                    'mensagem' => $resultadoPagamento['mensagem'],
                    'status' => 'RECUSADO',
                ];
            }

            //Registrar o pagamento aprovado
            $this->pagamento->registrar(
                $idPedido,
                $metodo,
                $resultadoPagamento['status'],
                $resultadoPagamento['referencia']
            );

            //Atualizar o status do pedido
            $statusAtualizado = $this->pedido->atualizarStatus(
                $idPedido,
                'CONFIRMADO'
            );

            if (!$statusAtualizado) {
                throw new RuntimeException(
                    'Não foi possível confirmar o pedido.'
                );
            }

            // Esvaziar o carrinho após a aprovação
            $stmt = $this->db->prepare(
                'DELETE FROM item_carrinho
                 WHERE id_carrinho = :id'
            );

            $stmt->execute(['id' => $idCarrinho]);

            // Confirmar todas as operações
            $this->db->commit();

            return [
                'sucesso' => true,
                'id_pedido' => $idPedido,
                'subtotal' => $subtotal,
                'taxa_entrega' => self::TAXA_ENTREGA,
                'total' => $total,
                'metodo' => $metodo,
                'status' => 'CONFIRMADO',
                'referencia' => $resultadoPagamento['referencia'],
                'codigo_pix' => $resultadoPagamento['codigo_pix'] ?? null,
                'mensagem' => 'Pedido confirmado com sucesso!',
            ];

        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }
}
