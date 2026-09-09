<?php

declare(strict_types=1);

namespace App\Data;

/**
 * Objeto de valor imutável. Atravessa as camadas no lugar de array solto,
 * o que permite ao PHPStan verificar o contrato em tempo de análise.
 */
final readonly class ItemData
{
    public function __construct(
        public int $produtoId,
        public int $quantidade,
        public int $precoUnitarioEmCentavos,
    ) {}

    /**
     * @param  array<string, int|string>  $dados
     */
    public static function fromArray(array $dados): self
    {
        return new self(
            produtoId: (int) $dados['produto_id'],
            quantidade: (int) $dados['quantidade'],
            precoUnitarioEmCentavos: (int) $dados['preco_unitario'],
        );
    }

    public function subtotalEmCentavos(): int
    {
        return $this->precoUnitarioEmCentavos * $this->quantidade;
    }
}
