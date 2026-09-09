<?php

declare(strict_types=1);

use App\Actions\Pedidos\CancelarPedido;
use App\Enums\StatusPedido;
use App\Models\ItemPedido;
use App\Models\Pedido;
use App\Models\Produto;

it('devolve o estoque ao cancelar', function (): void {
    $produto = Produto::factory()->create(['estoque' => 5]);
    $pedido = Pedido::factory()->create(['status' => StatusPedido::Aguardando]);

    ItemPedido::query()->create([
        'pedido_id' => $pedido->id,
        'produto_id' => $produto->id,
        'quantidade' => 3,
        'preco_unitario_em_centavos' => 1_000,
    ]);

    app(CancelarPedido::class)->handle($pedido->load('itens'));

    expect($produto->fresh()->estoque)->toBe(8)
        ->and($pedido->fresh()->status)->toBe(StatusPedido::Cancelado);
});

it('recusa cancelar um pedido já pago', function (): void {
    $pedido = Pedido::factory()->pago()->create();

    expect(fn () => app(CancelarPedido::class)->handle($pedido->load('itens')))
        ->toThrow(DomainException::class);
});
