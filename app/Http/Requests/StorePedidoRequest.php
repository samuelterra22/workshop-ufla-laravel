<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CanalPedido;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Validação e autorização vivem aqui. Nunca no controller, nunca no model.
 */
final class StorePedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'cliente_id' => ['required', 'integer', Rule::exists('clientes', 'id')],
            'canal' => ['required', new Enum(CanalPedido::class)],
            'observacao' => ['nullable', 'string', 'max:500'],

            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', Rule::exists('produtos', 'id')],
            'itens.*.quantidade' => ['required', 'integer', 'min:1', 'max:999'],
            'itens.*.preco_unitario' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'itens.required' => 'O pedido precisa de pelo menos um item.',
            'itens.*.quantidade.min' => 'A quantidade de cada item precisa ser no mínimo 1.',
        ];
    }
}
