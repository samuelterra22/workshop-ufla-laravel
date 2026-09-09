<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'admin@workshop.test'],
            ['name' => 'Admin do Workshop', 'password' => 'password'],
        );

        Cliente::factory()->count(20)->create();
        Produto::factory()->count(30)->create();
    }
}
