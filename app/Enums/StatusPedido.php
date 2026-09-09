<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusPedido: string implements HasColor, HasLabel
{
    case Rascunho = 'rascunho';
    case Aguardando = 'aguardando_pagamento';
    case Pago = 'pago';
    case Cancelado = 'cancelado';

    public function getLabel(): string
    {
        return match ($this) {
            self::Rascunho => 'Rascunho',
            self::Aguardando => 'Aguardando pagamento',
            self::Pago => 'Pago',
            self::Cancelado => 'Cancelado',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Rascunho => 'gray',
            self::Aguardando => 'warning',
            self::Pago => 'success',
            self::Cancelado => 'danger',
        };
    }

    /**
     * Só é possível cancelar antes de o pagamento ser confirmado.
     */
    public function podeSerCancelado(): bool
    {
        return in_array($this, [self::Rascunho, self::Aguardando], strict: true);
    }
}
