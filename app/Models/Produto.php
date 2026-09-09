<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProdutoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $nome
 * @property string $sku
 * @property int $preco_em_centavos
 * @property int $estoque
 */
final class Produto extends Model
{
    /** @use HasFactory<ProdutoFactory> */
    use HasFactory;

    protected $fillable = ['nome', 'sku', 'preco_em_centavos', 'estoque'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preco_em_centavos' => 'integer',
            'estoque' => 'integer',
        ];
    }
}
