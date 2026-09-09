<?php

declare(strict_types=1);

use App\Actions\Pedidos\CriarPedido;
use App\Data\ItemData;
use App\Data\PedidoData;
use App\Enums\CanalPedido;
use App\Enums\StatusPedido;
use App\Events\PedidoCriado;
use App\Exceptions\EstoqueInsuficienteException;
use App\Models\Cliente;
use App\Models\Produto;
use Illuminate\Support\Facades\Event;

/*
| Repare: nenhum destes testes sobe HTTP, sessão ou navegador.
| Isso só é possível porque a regra de negócio vive na Action.
*/

it('soma o total considerando a quantidade de cada item', function (): void {
    $cliente = Cliente::factory()->create();
    $produtoA = Produto::factory()->create(['estoque' => 100]);
    $produtoB = Produto::factory()->create(['estoque' => 100]);

    $dados = new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Web,
        itens: [
            new ItemData(produtoId: $produtoA->id, quantidade: 3, precoUnitarioEmCentavos: 1_000),
            new ItemData(produtoId: $produtoB->id, quantidade: 2, precoUnitarioEmCentavos: 2_500),
        ],
    );

    $pedido = app(CriarPedido::class)->handle($dados);

    // 3 x 1000 + 2 x 2500 = 8000
    expect($pedido->total_em_centavos)->toBe(8_000);
});

it('grava um item por linha do pedido', function (): void {
    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 50]);

    $pedido = app(CriarPedido::class)->handle(new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Balcao,
        itens: [new ItemData($produto->id, 4, 1_500)],
    ));

    $itens = $pedido->itens()->get();

    expect($itens)->toHaveCount(1)
        ->and($itens->first()->quantidade)->toBe(4);
});

it('reserva o estoque dos produtos do pedido', function (): void {
    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 10]);

    app(CriarPedido::class)->handle(new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Web,
        itens: [new ItemData($produto->id, 3, 1_000)],
    ));

    expect($produto->fresh()->estoque)->toBe(7);
});

it('nasce aguardando pagamento quando o canal exige pagamento imediato', function (): void {
    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 10]);

    $pedido = app(CriarPedido::class)->handle(new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Web,
        itens: [new ItemData($produto->id, 1, 1_000)],
    ));

    expect($pedido->status)->toBe(StatusPedido::Aguardando);
});

it('nasce como rascunho quando vem da integração', function (): void {
    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 10]);

    $pedido = app(CriarPedido::class)->handle(new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Api,
        itens: [new ItemData($produto->id, 1, 1_000)],
    ));

    expect($pedido->status)->toBe(StatusPedido::Rascunho);
});

it('dispara o evento de pedido criado', function (): void {
    Event::fake([PedidoCriado::class]);

    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 10]);

    app(CriarPedido::class)->handle(new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Web,
        itens: [new ItemData($produto->id, 1, 1_000)],
    ));

    Event::assertDispatched(PedidoCriado::class);
});

it('recusa o pedido quando falta estoque e não deixa rastro', function (): void {
    $cliente = Cliente::factory()->create();
    $produto = Produto::factory()->create(['estoque' => 2]);

    $acao = fn () => app(CriarPedido::class)->handle(new PedidoData(
        clienteId: $cliente->id,
        canal: CanalPedido::Web,
        itens: [new ItemData($produto->id, 5, 1_000)],
    ));

    expect($acao)->toThrow(EstoqueInsuficienteException::class);

    // A transação garante que nada ficou pela metade.
    $this->assertDatabaseCount('pedidos', 0);
    expect($produto->fresh()->estoque)->toBe(2);
});
