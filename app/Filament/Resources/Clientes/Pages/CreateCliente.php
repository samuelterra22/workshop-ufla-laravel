<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clientes\Pages;

use App\Filament\Resources\Clientes\ClienteResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateCliente extends CreateRecord
{
    protected static string $resource = ClienteResource::class;
}
