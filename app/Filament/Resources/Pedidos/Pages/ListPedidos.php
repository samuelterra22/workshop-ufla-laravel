<?php

declare(strict_types=1);

namespace App\Filament\Resources\Pedidos\Pages;

use App\Filament\Resources\Pedidos\PedidoResource;
use Filament\Resources\Pages\ListRecords;

final class ListPedidos extends ListRecords
{
    protected static string $resource = PedidoResource::class;
}
