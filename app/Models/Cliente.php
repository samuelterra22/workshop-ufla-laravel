<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ClienteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string|null $documento
 * @property bool $ativo
 */
final class Cliente extends Model
{
    /** @use HasFactory<ClienteFactory> */
    use HasFactory;

    protected $fillable = ['nome', 'email', 'documento', 'ativo'];

    /**
     * @return HasMany<Pedido, $this>
     */
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['ativo' => 'boolean'];
    }
}
