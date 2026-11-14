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

    //Confirmar um pagamento PIX simulado
    public function confirmarPix(
        int $idPedido,
        int $idCarrinho
    ): array {
        $this->db->beginTransaction();

        try {
            $pedidoExistente = $this->pedido->buscarPorId($idPedido);
            $pagamentoExistente = $this->pagamento->porPedido($idPedido);
            $carrinhoExistente = $this->carrinho->buscarPorId($idCarrinho);

            if (!$carrinhoExistente) {
                throw new RuntimeException('Carrinho nao encontrado.');
            }

            if (
                !$pedidoExistente ||
                !$pagamentoExistente ||
                $pagamentoExistente['metodo'] !== 'PIX' ||
                $pagamentoExistente['status'] !== 'PENDENTE' ||
                $pedidoExistente['status'] !== 'PENDENTE'
            ) {
                throw new RuntimeException('Pedido PIX pendente nao encontrado.');
            }

            //Atualizar o pagamento
            $pagamentoAtualizado = $this->pagamento->atualizarStatus(
                $idPedido,
                'APROVADO'
            );

            if (!$pagamentoAtualizado) {
                throw new RuntimeException('Nao foi possivel confirmar o pagamento.');
            }

            //Confirmar o pedido
            $pedidoAtualizado = $this->pedido->atualizarStatus(
                $idPedido,
                'CONFIRMADO'
            );

            if (!$pedidoAtualizado) {
                throw new RuntimeException('Nao foi possivel confirmar o pedido.');
            }

            //Esvaziar o carrinho
            $stmt = $this->db->prepare(
                'DELETE FROM item_carrinho
             WHERE id_carrinho = :id'
            );

            $stmt->execute(['id' => $idCarrinho]);

            $this->db->commit();

            return [
                'sucesso' => true,
                'id_pedido' => $idPedido,
                'total' => (float) $pedidoExistente['total'],
                'metodo' => 'PIX',
                'status' => 'CONFIRMADO',
                'referencia' => $pagamentoExistente['referencia_transacao'],
                'codigo_pix' => null,
                'mensagem' => 'Pedido confirmado com sucesso!',
            ];
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
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

        if ($telefone !== null) {
            $telefoneNumeros = preg_replace('/\D/', '', $telefone);

            if (
                $telefoneNumeros === null ||
                !preg_match('/^[1-9]{2}9[0-9]{8}$/', $telefoneNumeros)
            ) {
                throw new RuntimeException(
                    'Informe um celular válido com DDD + 9 dígitos.'
                );
            }
        }

        $cep = $cep !== null ? trim($cep) : '';
        $cepNumeros = preg_replace('/\D/', '', $cep);

        if ($cepNumeros === null || !preg_match('/^\d{8}$/', $cepNumeros)) {
            throw new RuntimeException(
                'Informe um CEP válido para finalizar o pedido.'
            );
        }

        $cep = substr($cepNumeros, 0, 5) . '-' . substr($cepNumeros, 5, 3);

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
            $taxaEntrega = self::TAXA_ENTREGA;

            $total = round($subtotal + $taxaEntrega, 2);

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

            //Gerar PIX pendente sem confirmar o pedido
            if ($metodo === 'PIX') {
                $resultadoPagamento = $this->gateway->gerarPixPendente();

                $this->pagamento->registrar(
                    $idPedido,
                    'PIX',
                    'PENDENTE',
                    $resultadoPagamento['referencia']
                );

                $this->db->commit();

                return [
                    'sucesso' => false,
                    'aguardando_pix' => true,
                    'id_pedido' => $idPedido,
                    'subtotal' => $subtotal,
                    'taxa_entrega' => self::TAXA_ENTREGA,
                    'total' => $total,
                    'metodo' => 'PIX',
                    'status' => 'PENDENTE',
                    'referencia' => $resultadoPagamento['referencia'],
                    'codigo_pix' => $resultadoPagamento['codigo_pix'],
                    'mensagem' => $resultadoPagamento['mensagem'],
                ];
            }

            //Processar pagamento com cartao
            $resultadoPagamento = $this->gateway->pagarComCartao(
                $numeroCartao,
                $validade,
                $cvv
            );

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
