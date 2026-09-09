<?php

declare(strict_types=1);

use App\Events\PedidoCriado;
use App\Jobs\GerarNotaFiscal;
use App\Jobs\NotificarCliente;
use App\Listeners\DespacharEfeitosDoPedido;
use App\Models\Pedido;
use Illuminate\Support\Facades\Queue;

/*
| Duas coisas diferentes, testadas separadamente:
|   1. o dispatch aconteceu, na fila certa   -> Queue::fake()
|   2. a lógica do job está correta          -> instancia e chama handle()
*/

it('enfileira a nota fiscal na fila fiscal', function (): void {
    Queue::fake();

    $pedido = Pedido::factory()->create();

    (new DespacharEfeitosDoPedido())->handle(new PedidoCriado($pedido));

    Queue::assertPushedOn('fiscal', GerarNotaFiscal::class);
});

it('enfileira a notificação do cliente', function (): void {
    Queue::fake();

    $pedido = Pedido::factory()->create();

    (new DespacharEfeitosDoPedido())->handle(new PedidoCriado($pedido));

    Queue::assertPushed(NotificarCliente::class);
});

it('gera id único por pedido para evitar nota duplicada', function (): void {
    $pedido = Pedido::factory()->create();

    expect((new GerarNotaFiscal($pedido))->uniqueId())
        ->toBe("nota-fiscal-{$pedido->id}");
});
