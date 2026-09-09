<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

final readonly class PedidoPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pedido $pedido): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Pedido $pedido): bool
    {
        return $pedido->status->podeSerCancelado();
    }

    public function delete(User $user, Pedido $pedido): bool
    {
        return $pedido->status->podeSerCancelado();
    }
}
