<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\PedidoCriado;
use App\Listeners\DespacharEfeitosDoPedido;
use App\Models\Pedido;
use App\Policies\PedidoPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configurarEloquent();
        $this->configurarObservabilidade();

        Gate::policy(Pedido::class, PedidoPolicy::class);

        Event::listen(PedidoCriado::class, DespacharEfeitosDoPedido::class);
    }

    /**
     * Fora de produção, lazy loading e atributo descartado viram exceção.
     * É a forma mais barata de nunca descobrir um N+1 no chamado do cliente.
     */
    private function configurarEloquent(): void
    {
        $emProducao = $this->app->isProduction();

        Model::preventLazyLoading(! $emProducao);
        Model::preventSilentlyDiscardingAttributes(! $emProducao);
    }

    /**
     * Query lenta vira aviso no log, que o Pulse e o Sentry agrupam.
     *
     * Se quiser ser mais rigoroso, troque o warning por
     * `throw new RuntimeException($mensagem)` fora de produção: aí a query
     * lenta quebra o teste em vez de virar linha de log que ninguém lê.
     */
    private function configurarObservabilidade(): void
    {
        DB::whenQueryingForLongerThan(300, static function ($connection, $event): void {
            logger()->warning("Query lenta ({$event->time}ms): {$event->sql}");
        });
    }
}
