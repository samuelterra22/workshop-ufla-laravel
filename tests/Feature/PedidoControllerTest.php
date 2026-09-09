<?php

declare(strict_types=1);

use App\Enums\CanalPedido;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\User;

it('exige autenticação para listar pedidos', function (): void {
    $this->get(route('pedidos.index'))->assertRedirect();
});

it('cria um pedido pela rota', function (): void {
    $this->actingAs(User::factory()->create());

    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 20]);

    $this->post(route('pedidos.store'), [
        'cliente_id' => $cliente->id,
        'canal' => CanalPedido::Web->value,
        'itens' => [
            ['produto_id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 1_500],
        ],
    ])->assertRedirect(route('pedidos.index'));

    $this->assertDatabaseHas('pedidos', [
        'cliente_id' => $cliente->id,
        'total_em_centavos' => 3_000,
    ]);
});

it('recusa pedido sem itens', function (): void {
    $this->actingAs(User::factory()->create());
    $cliente = Cliente::factory()->create();

    $this->post(route('pedidos.store'), [
        'cliente_id' => $cliente->id,
        'canal' => CanalPedido::Web->value,
        'itens' => [],
    ])->assertSessionHasErrors('itens');
});

it('recusa canal inexistente', function (): void {
    $this->actingAs(User::factory()->create());
    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create();

    $this->post(route('pedidos.store'), [
        'cliente_id' => $cliente->id,
        'canal' => 'pombo-correio',
        'itens' => [
            ['produto_id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 100],
        ],
    ])->assertSessionHasErrors('canal');
});
