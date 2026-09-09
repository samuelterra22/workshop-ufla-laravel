# Filas e trabalho assíncrono

Tudo que o usuário não precisa esperar sai do request.

## O padrão

```php
// dentro da Action, depois da transação
PedidoCriado::dispatch($pedido);

// o listener decide o que despachar
GerarNotaFiscal::dispatch($pedido);                       // fila "fiscal"
NotificarCliente::dispatch($pedido)->delay(now()->addMinute());
```

Veja `app/Listeners/DespacharEfeitosDoPedido.php`.

## Política de falha explícita

```php
final class GerarNotaFiscal implements ShouldQueue, ShouldBeUnique
{
    public int $tries = 3;
    public int $timeout = 120;
    public array $backoff = [10, 60, 300];   // espera crescente

    public function uniqueId(): string
    {
        return "nota-fiscal-{$this->pedido->id}";   // evita nota duplicada
    }

    public function failed(Throwable $e): void
    {
        // destino explícito para a falha, não silêncio
    }
}
```

## Filas separadas por criticidade

```php
// config/horizon.php
'production' => [
    'supervisor-urgente' => [
        'queue' => ['fiscal', 'pagamento'],
        'maxProcesses' => 6,
        'balance' => 'auto',
    ],
    'supervisor-lento' => [
        'queue' => ['relatorio', 'exportacao'],
        'maxProcesses' => 2,
        'timeout' => 900,
    ],
],
```

Relatório pesado não pode segurar o que é urgente. Workers distintos resolvem
isso sem uma linha de código.

## Testando

São duas coisas diferentes e vale testar as duas:

```php
// 1. o dispatch aconteceu, na fila certa
Queue::fake();
(new DespacharEfeitosDoPedido())->handle(new PedidoCriado($pedido));
Queue::assertPushedOn('fiscal', GerarNotaFiscal::class);

// 2. a lógica do job está correta — sem fila nenhuma
(new GerarNotaFiscal($pedido))->handle();
```

Veja `tests/Feature/FilaTest.php`.

## O que quase sempre esquecem

Job falhado precisa de destino: tabela `failed_jobs`, alerta e retry.
**Fila sem monitoramento é trabalho perdido em silêncio**, e o usuário só
descobre dias depois.

No Horizon isso é um painel. Sem Horizon, no mínimo:

```bash
php artisan queue:failed
php artisan queue:retry all
```

E um alerta: fila crescendo é uma das regras em `docs/monitoramento/`.
