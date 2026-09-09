<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PedidoCriado;
use App\Jobs\GerarNotaFiscal;
use App\Jobs\NotificarCliente;
use Illuminate\Support\Carbon;

/**
 * O evento só decide o que despachar. O trabalho pesado fica nos jobs,
 * cada um na sua fila, com política de falha própria.
 */
final class DespacharEfeitosDoPedido
{
    public function handle(PedidoCriado $evento): void
    {
        GerarNotaFiscal::dispatch($evento->pedido);

        NotificarCliente::dispatch($evento->pedido)
            ->delay(Carbon::now()->addMinute());
    }
}
