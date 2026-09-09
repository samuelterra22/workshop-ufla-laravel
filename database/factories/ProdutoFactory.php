<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produto>
 */
final class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => ucfirst($this->faker->words(2, true)),
            'sku' => mb_strtoupper($this->faker->unique()->bothify('???-####')),
            'preco_em_centavos' => $this->faker->numberBetween(500, 50_000),
            'estoque' => $this->faker->numberBetween(10, 500),
        ];
    }

    public function semEstoque(): self
    {
        return $this->state(fn (): array => ['estoque' => 0]);
    }
}
