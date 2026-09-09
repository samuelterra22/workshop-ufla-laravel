<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Pedido;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Efeito colateral sai do caminho da requisição.
 *
 * Política de falha explícita: três tentativas com espera crescente e
 * destino definido para quando desistir.
 */
final class GerarNotaFiscal implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 120;

    /** @var list<int> */
    public array $backoff = [10, 60, 300];

    public function __construct(public readonly Pedido $pedido)
    {
        $this->onQueue('fiscal');
    }

    public function uniqueId(): string
    {
        return "nota-fiscal-{$this->pedido->id}";
    }

    public function handle(): void
    {
        // Integração real entraria aqui. No workshop, só registramos.
        Log::info('Nota fiscal emitida', [
            'pedido' => $this->pedido->id,
            'total' => $this->pedido->total_em_centavos,
        ]);
    }

    public function failed(Throwable $e): void
    {
        Log::error('Falha ao emitir nota fiscal', [
            'pedido' => $this->pedido->id,
            'erro' => $e->getMessage(),
        ]);
    }
}
