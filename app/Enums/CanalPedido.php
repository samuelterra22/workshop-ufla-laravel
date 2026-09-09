<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CanalPedido: string implements HasLabel
{
    case Web = 'web';
    case Whatsapp = 'whatsapp';
    case Api = 'api';
    case Balcao = 'balcao';

    public function getLabel(): string
    {
        return match ($this) {
            self::Web => 'Site',
            self::Whatsapp => 'WhatsApp',
            self::Api => 'Integração (API)',
            self::Balcao => 'Balcão',
        };
    }

    /**
     * Regra de negócio junto do tipo: pedido vindo de integração é faturado
     * no fechamento do mês e não exige pagamento imediato.
     */
    public function exigePagamentoImediato(): bool
    {
        return $this !== self::Api;
    }
}
