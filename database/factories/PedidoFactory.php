<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CanalPedido;
use App\Enums\StatusPedido;
use App\Models\Cliente;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pedido>
 */
final class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'canal' => $this->faker->randomElement(CanalPedido::cases()),
            'status' => StatusPedido::Aguardando,
            'total_em_centavos' => $this->faker->numberBetween(1_000, 100_000),
            'observacao' => null,
        ];
    }

    public function pago(): self
    {
        return $this->state(fn (): array => ['status' => StatusPedido::Pago]);
    }

    public function cancelado(): self
    {
        return $this->state(fn (): array => ['status' => StatusPedido::Cancelado]);
    }
}
