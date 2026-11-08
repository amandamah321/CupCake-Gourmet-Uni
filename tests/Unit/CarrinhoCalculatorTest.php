<?php

declare(strict_types=1);

use App\Services\CarrinhoCalculator;
use PHPUnit\Framework\TestCase;

final class CarrinhoCalculatorTest extends TestCase
{
    public function testCalcularSubtotalDeUmProduto(): void
    {
        $calculator = new CarrinhoCalculator();

        $resultado = $calculator->calcular([
            [
                'quantidade' => 2,
                'preco_unitario' => 5.00,
            ],
        ]);

        $this->assertSame(10.0, $resultado['total']);
    }

    public function testCalcularSubtotalDeVariosProdutos(): void
    {
        $calculator = new CarrinhoCalculator();

        $resultado = $calculator->calcular([
            [
                'quantidade' => 2,
                'preco_unitario' => 5.00,
            ],
            [
                'quantidade' => 3,
                'preco_unitario' => 4.00,
            ],
        ]);

        $this->assertSame(22.0, $resultado['total']);
    }

    public function testRetornarSubtotalDeCadaItem(): void
    {
        $calculator = new CarrinhoCalculator();

        $resultado = $calculator->calcular([
            [
                'quantidade' => 2,
                'preco_unitario' => 5.00,
            ],
        ]);

        $this->assertSame(10.0, $resultado['itens'][0]['subtotal']);
    }

    public function testRejeitarQuantidadeInvalida(): void
    {
        $calculator = new CarrinhoCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator->calcular([
            [
                'quantidade' => 0,
                'preco_unitario' => 5.00,
            ],
        ]);
    }

    public function testRejeitarPrecoNegativo(): void
    {
        $calculator = new CarrinhoCalculator();

        $this->expectException(InvalidArgumentException::class);

        $calculator->calcular([
            [
                'quantidade' => 1,
                'preco_unitario' => -5.00,
            ],
        ]);
    }
}
