<?php

declare(strict_types=1);

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

final class ClienteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('nome')
                ->label('Nome')
                ->required()
                ->maxLength(120),

            TextInput::make('email')
                ->label('E-mail')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('documento')
                ->label('CPF ou CNPJ')
                ->maxLength(20),

            Toggle::make('ativo')
                ->label('Cliente ativo')
                ->default(true),
        ]);
    }
}
