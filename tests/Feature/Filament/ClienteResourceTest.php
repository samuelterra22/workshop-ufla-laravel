<?php

declare(strict_types=1);

use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Models\Cliente;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('lista clientes no painel', function (): void {
    $clientes = Cliente::factory()->count(3)->create();

    livewire(ListClientes::class)
        ->assertCanSeeTableRecords($clientes);
});

it('cria um cliente pelo painel', function (): void {
    livewire(CreateCliente::class)
        ->fillForm([
            'nome' => 'Fulano de Tal',
            'email' => 'fulano@example.com',
            'ativo' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('clientes', ['email' => 'fulano@example.com']);
});

it('valida e-mail duplicado', function (): void {
    Cliente::factory()->create(['email' => 'repetido@example.com']);

    livewire(CreateCliente::class)
        ->fillForm([
            'nome' => 'Outro',
            'email' => 'repetido@example.com',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});
