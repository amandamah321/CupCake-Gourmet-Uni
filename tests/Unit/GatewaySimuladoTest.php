<?php

declare(strict_types=1);

use App\Services\GatewaySimulado;
use PHPUnit\Framework\TestCase;

final class GatewaySimuladoTest extends TestCase
{
    public function testAprovarCartaoDeTeste(): void
    {
        $gateway = new GatewaySimulado();

        $resultado = $gateway->pagarComCartao(
            '4111111111111111',
            '12/28',
            '123'
        );

        $this->assertTrue($resultado['aprovado']);
        $this->assertSame('APROVADO', $resultado['status']);
        $this->assertNotNull($resultado['referencia']);
    }

    public function testRecusarDadosDeCartaoInvalidos(): void
    {
        $gateway = new GatewaySimulado();

        $resultado = $gateway->pagarComCartao(
            '123456',
            '12/28',
            '123'
        );

        $this->assertFalse($resultado['aprovado']);
        $this->assertSame('RECUSADO', $resultado['status']);
        $this->assertNull($resultado['referencia']);
    }

    public function testRecusarCartaoNaoAutorizado(): void
    {
        $gateway = new GatewaySimulado();

        $resultado = $gateway->pagarComCartao(
            '5555555555554444',
            '12/28',
            '123'
        );

        $this->assertFalse($resultado['aprovado']);
        $this->assertSame('RECUSADO', $resultado['status']);
        $this->assertNotNull($resultado['referencia']);
    }

    public function testGerarPixComStatusPendente(): void
    {
        $gateway = new GatewaySimulado();

        $resultado = $gateway->gerarPixPendente();

        $this->assertFalse($resultado['aprovado']);
        $this->assertSame('PENDENTE', $resultado['status']);
        $this->assertNotEmpty($resultado['referencia']);
        $this->assertNotEmpty($resultado['codigo_pix']);
    }
}
