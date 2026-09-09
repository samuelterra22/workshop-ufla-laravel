<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Pedido;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class NotificarCliente implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    /** @var list<int> */
    public array $backoff = [30, 120, 600];

    public function __construct(public readonly Pedido $pedido)
    {
        $this->onQueue('notificacao');
    }

    public function handle(): void
    {
        Log::info('Cliente notificado', [
            'pedido' => $this->pedido->id,
            'cliente' => $this->pedido->cliente_id,
        ]);
    }
}
