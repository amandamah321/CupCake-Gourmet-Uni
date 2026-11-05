<?php

declare(strict_types=1);

namespace App\Services;

final class CarrinhoCalculator
{
    //Calcular subtotal e cada produto do carrinho
    public function calcular(array $itens): array
    {
        $subtotal = 0.0;
        $itensCalculados = [];

        foreach ($itens as $item) {
            $quantidade = (int) ($item['quantidade'] ?? 0);
            $preco = (float) ($item['preco_unitario'] ?? 0);

            if ($quantidade <= 0 || $preco < 0) {
                throw new \InvalidArgumentException(
                    'Quantidade ou preço inválido.'
                );
            }

            $valorItem = round($quantidade * $preco, 2);

            $itensCalculados[] = array_merge($item, [
                'subtotal' => $valorItem,
            ]);

            $subtotal += $valorItem;
        }

        return [
            'itens' => $itensCalculados,
            'total' => round($subtotal, 2),
        ];
    }
}
