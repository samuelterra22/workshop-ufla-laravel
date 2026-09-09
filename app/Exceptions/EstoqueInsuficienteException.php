<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class EstoqueInsuficienteException extends RuntimeException
{
    public static function paraProduto(int $produtoId, int $solicitado, int $disponivel): self
    {
        return new self(
            "Estoque insuficiente para o produto {$produtoId}: solicitado {$solicitado}, disponível {$disponivel}."
        );
    }
}
