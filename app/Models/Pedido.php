<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CanalPedido;
use App\Enums\StatusPedido;
use Database\Factories\PedidoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $cliente_id
 * @property CanalPedido $canal
 * @property StatusPedido $status
 * @property int $total_em_centavos
 * @property string|null $observacao
 * @property-read Cliente $cliente
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ItemPedido> $itens
 */
final class Pedido extends Model
{
    /** @use HasFactory<PedidoFactory> */
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'canal',
        'status',
        'total_em_centavos',
        'observacao',
    ];

    /**
     * @return BelongsTo<Cliente, $this>
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * @return HasMany<ItemPedido, $this>
     */
    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'canal' => CanalPedido::class,
            'status' => StatusPedido::class,
            'total_em_centavos' => 'integer',
        ];
    }
}
