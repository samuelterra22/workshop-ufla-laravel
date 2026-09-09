<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
final class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'documento' => $this->faker->numerify('###.###.###-##'),
            'ativo' => true,
        ];
    }

    public function inativo(): self
    {
        return $this->state(fn (): array => ['ativo' => false]);
    }
}
