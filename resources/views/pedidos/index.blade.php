{{-- View só apresenta. Nenhum cálculo, nenhuma consulta, nenhuma regra. --}}
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedidos</title>
</head>
<body>
    <h1>Pedidos</h1>

    @if (session('status'))
        <p role="status">{{ session('status') }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Canal</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pedidos as $pedido)
                <tr>
                    <td>{{ $pedido->id }}</td>
                    <td>{{ $pedido->cliente->nome }}</td>
                    <td>{{ $pedido->canal->getLabel() }}</td>
                    <td>{{ $pedido->status->getLabel() }}</td>
                    <td>{{ \Illuminate\Support\Number::currency($pedido->total_em_centavos / 100, 'BRL', 'pt_BR') }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Nenhum pedido ainda.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $pedidos->links() }}
</body>
</html>
