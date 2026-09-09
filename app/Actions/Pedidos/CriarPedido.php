<?php

declare(strict_types=1);

namespace App\Actions\Pedidos;

use App\Data\ItemData;
use App\Data\PedidoData;
use App\Enums\StatusPedido;
use App\Events\PedidoCriado;
use App\Models\Pedido;
use App\Services\ReservaEstoque;
use Illuminate\Support\Facades\DB;

/**
 * Um caso de uso, uma classe, um método público.
 *
 * A regra de negócio vive aqui e não no controller. É por isso que o teste
 * desta classe roda sem HTTP, sem sessão e sem navegador.
 */
final readonly class CriarPedido
{
    public function __construct(
        private ReservaEstoque $estoque,
    ) {}

    public function handle(PedidoData $dados): Pedido
    {
        return DB::transaction(function () use ($dados): Pedido {
            $total = array_sum(array_map(
                static fn (ItemData $item): int => $item->precoUnitarioEmCentavos,
                $dados->itens,
            ));

            $pedido = Pedido::query()->create([
                'cliente_id' => $dados->clienteId,
                'canal' => $dados->canal,
                'status' => $dados->canal->exigePagamentoImediato()
                    ? StatusPedido::Aguardando
                    : StatusPedido::Rascunho,
                'total_em_centavos' => $total,
                'observacao' => $dados->observacao,
            ]);

            foreach ($dados->itens as $item) {
                $pedido->itens()->create([
                    'produto_id' => $item->produtoId,
                    'quantidade' => $item->quantidade,
                    'preco_unitario_em_centavos' => $item->precoUnitarioEmCentavos,
                ]);
            }

            $this->estoque->reservar($dados->itens);

            PedidoCriado::dispatch($pedido);

            return $pedido;
        });
    }
}
