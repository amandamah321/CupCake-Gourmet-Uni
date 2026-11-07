<?php

declare(strict_types=1);

namespace App\Services;

final class GatewaySimulado
{
    //Simular o pagamento com cartao (nao faz cobrancas e nem armazena os dados do cartao)
    public function pagarComCartao(
        string $numero,
        string $validade,
        string $cvv
    ): array {
        $numero = preg_replace('/\D/', '', $numero) ?? '';

        if (
            strlen($numero) !== 16 ||
            !preg_match('/^\d{2}\/\d{2}$/', $validade) ||
            !preg_match('/^\d{3,4}$/', $cvv)
        ) {
            return [
                'aprovado' => false,
                'status' => 'RECUSADO',
                'referencia' => null,
                'mensagem' => 'Dados do cartão inválidos.',
            ];
        }

        // Cartão fake para testes
        if ($numero === '4111111111111111') {
            return [
                'aprovado' => true,
                'status' => 'APROVADO',
                'referencia' => 'SIM-CARTAO-' . strtoupper(bin2hex(random_bytes(4))),
                'mensagem' => 'Pagamento simulado aprovado!',
            ];
        }

        return [
            'aprovado' => false,
            'status' => 'RECUSADO',
            'referencia' => 'SIM-CARTAO-' . strtoupper(bin2hex(random_bytes(4))),
            'mensagem' => 'Pagamento simulado recusado.',
        ];
    }

    //Gerar uma cobranca PIX simulada sem aprovar o pagamento
    public function gerarPixPendente(): array
    {
        return [
            'aprovado' => false,
            'status' => 'PENDENTE',
            'referencia' => 'SIM-PIX-' . strtoupper(bin2hex(random_bytes(4))),
            'codigo_pix' => 'PIX-SIMULADO-' . strtoupper(bin2hex(random_bytes(8))),
            'mensagem' => 'Aguardando confirmacao do pagamento PIX.',
        ];
    }

    //Simular um pagamento com PIX (nao faz pagamento real)
    public function pagarComPix(): array
    {
        return [
            'aprovado' => true,
            'status' => 'APROVADO',
            'referencia' => 'SIM-PIX-' . strtoupper(bin2hex(random_bytes(4))),
            'codigo_pix' => 'PIX-SIMULADO-' . strtoupper(bin2hex(random_bytes(8))),
            'mensagem' => 'Pagamento PIX simulado aprovado!',
        ];
    }
}
