<?php

declare(strict_types=1);

namespace App\Actions\Pedidos;

use App\Enums\StatusPedido;
use App\Models\Pedido;
use App\Services\ReservaEstoque;
use DomainException;
use Illuminate\Support\Facades\DB;

final readonly class CancelarPedido
{
    public function __construct(
        private ReservaEstoque $estoque,
    ) {}

    public function handle(Pedido $pedido): Pedido
    {
        if (! $pedido->status->podeSerCancelado()) {
            throw new DomainException(
                "Pedido {$pedido->id} não pode ser cancelado no status {$pedido->status->value}."
            );
        }

        return DB::transaction(function () use ($pedido): Pedido {
            $this->estoque->devolver($pedido->itens);

            $pedido->update(['status' => StatusPedido::Cancelado]);

            return $pedido->refresh();
        });
    }
}
