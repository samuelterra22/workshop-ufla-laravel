<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Actions\Pedidos\CriarPedido;
use App\Data\PedidoData;
use Illuminate\Support\Str;
use App\Http\Requests\StorePedidoRequest;
use App\Models\Pedido;
use Illuminate\Contracts\View\View;

final class PedidoController extends Controller
{
    public function index(): View
    {
        $pedidos = Pedido::query()
            ->with([ "cliente" ])
            ->latest()
            ->paginate(15);

        return view( "pedidos.index", [ "pedidos" => $pedidos ] );
    }

    public function store( StorePedidoRequest $request, CriarPedido $criarPedido ): RedirectResponse
    {
        $pedido = $criarPedido->handle(
            PedidoData::fromArray( $request->validated() )
        );

        return redirect()
            ->route( "pedidos.index" )
            ->with( "status", "Pedido {$pedido->id} criado." );
    }
}
