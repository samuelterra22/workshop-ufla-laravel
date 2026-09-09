<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\ItemData;
use App\Exceptions\EstoqueInsuficienteException;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;

/**
 * Toda manipulação de estoque passa por aqui. Nenhum outro ponto do sistema
 * decrementa a coluna diretamente.
 */
final readonly class ReservaEstoque
{
    /**
     * @param  list<ItemData>  $itens
     *
     * @throws EstoqueInsuficienteException
     */
    public function reservar(array $itens): void
    {
        foreach ($itens as $item) {
            $produto = Produto::query()
                ->lockForUpdate()
                ->findOrFail($item->produtoId);

            if ($produto->estoque < $item->quantidade) {
                throw EstoqueInsuficienteException::paraProduto(
                    $item->produtoId,
                    $item->quantidade,
                    $produto->estoque,
                );
            }

            $produto->decrement('estoque', $item->quantidade);
        }
    }

    /**
     * @param  list<ItemData>  $itens
     */
    public function devolver(array $itens): void
    {
        foreach ($itens as $item) {
            Produto::query()
                ->whereKey($item->produtoId)
                ->increment('estoque', $item->quantidade);
        }
    }

    /**
     * Converte os itens persistidos em DTOs e devolve o estoque.
     *
     * Existe justamente para que ninguém precise passar uma Collection do
     * Eloquent onde o contrato pede uma lista de ItemData.
     */
    public function devolverDoPedido(Pedido $pedido): void
    {
        $itens = $pedido->itens
            ->map(static fn (ItemPedido $item): ItemData => new ItemData(
                produtoId: $item->produto_id,
                quantidade: $item->quantidade,
                precoUnitarioEmCentavos: $item->preco_unitario_em_centavos,
            ))
            ->values()
            ->all();

        $this->devolver($itens);
    }
}
