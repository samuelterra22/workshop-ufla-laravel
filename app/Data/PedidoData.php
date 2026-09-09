<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\CanalPedido;

final readonly class PedidoData
{
    /**
     * @param  list<ItemData>  $itens
     */
    public function __construct(
        public int $clienteId,
        public CanalPedido $canal,
        public array $itens,
        public ?string $observacao = null,
    ) {}

    /**
     * @param  array<string, mixed>  $dados
     */
    public static function fromArray(array $dados): self
    {
        /** @var array<int, array<string, int|string>> $itens */
        $itens = $dados['itens'];

        return new self(
            clienteId: (int) $dados['cliente_id'],
            canal: CanalPedido::from((string) $dados['canal']),
            itens: array_values(array_map(
                static fn (array $item): ItemData => ItemData::fromArray($item),
                $itens,
            )),
            observacao: isset($dados['observacao']) ? (string) $dados['observacao'] : null,
        );
    }

    public function totalEmCentavos(): int
    {
        return array_sum(array_map(
            static fn (ItemData $item): int => $item->subtotalEmCentavos(),
            $this->itens,
        ));
    }
}
