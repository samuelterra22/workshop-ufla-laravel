# Monitoramento

Duas camadas separadas, que respondem a perguntas diferentes.

## Camada de aplicação

| Ferramenta | Onde roda | Responde a |
|---|---|---|
| **Telescope** | desenvolvimento | "o que aconteceu no meu request agora" |
| **Pulse** | produção, self-hosted | "o que está lento no geral" |
| **Sentry** | produção | "qual exceção quebrou e para quem" |
| **Nightwatch** | produção, gerenciado | alternativa first-party ao par Pulse + Sentry |

Comece pelo Pulse: é gratuito, roda dentro da aplicação e resolve a maior parte.

## Camada de container e host

Stack única no Docker Swarm, operada pelo Portainer junto das aplicações.

| Componente | Função |
|---|---|
| **cAdvisor** | Coleta CPU, memória, rede e I/O de cada container do nó |
| **node-exporter** | Métricas do host: disco, carga, rede |
| **Prometheus** | Armazena as séries temporais e avalia as regras de alerta |
| **Alertmanager** | Agrupa, silencia repetição e roteia para o destino certo |
| **Grafana** | Dashboards e histórico para investigar depois do incidente |

Fluxo do alerta:

```
métrica  →  regra dispara  →  Alertmanager  →  webhook  →  Telegram
```

## Subindo

```bash
# os segredos primeiro
printf '%s' 'SEU_BOT_TOKEN' | docker secret create telegram_bot_token -
printf '%s' 'SENHA_FORTE'   | docker secret create grafana_admin_password -

# depois a stack
docker stack deploy -c docker-stack.yml monitoramento
```

Pelo Portainer: `Stacks → Add stack → Web editor`, cole o `docker-stack.yml` e
suba os três arquivos de configuração como configs.

## O que dispara alerta

- Recurso perto do limite: CPU, memória, disco, container reiniciando em loop
- Serviço fora do ar ou healthcheck falhando
- Exceção não tratada em aplicação Laravel
- Fila crescendo ou job falhando em série

## Exceção da aplicação no mesmo canal

O caminho mais curto é um canal de log apontando para o mesmo bot:

```php
// config/logging.php
'telegram' => [
    'driver' => 'monolog',
    'handler' => Monolog\Handler\TelegramBotHandler::class,
    'level' => 'error',
    'with' => [
        'apiKey' => env('TELEGRAM_BOT_TOKEN'),
        'channel' => env('TELEGRAM_CHAT_ID'),
    ],
],
```

E no `stack` padrão:

```php
'stack' => [
    'driver' => 'stack',
    'channels' => ['single', 'telegram'],
    'ignore_exceptions' => false,
],
```

## Por que tudo cai no mesmo canal

A ferramenta importa menos que a regra: **um único lugar onde todo alerta
chega**, com histórico pesquisável e notificação no celular. Telegram resolve
isso sem app extra e sem custo. Slack, Discord ou e-mail funcionam igual — o que
não funciona é alerta espalhado em quatro lugares que ninguém abre.
