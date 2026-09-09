# Workshop Laravel — UFLA

Projeto de referência do workshop **Desenvolvimento de aplicações Laravel:
ferramentas, performance e qualidade com IA**, apresentado para a equipe de
desenvolvimento da Universidade Federal de Lavras em 09/09/2026.

Não é um projeto de produção. É um projeto para ler, quebrar e reconstruir.
Cada arquivo existe para demonstrar uma decisão discutida no workshop.

**Autor:** Samuel Terra Vieira — [samuelterra.dev](https://samuelterra.dev) ·
[@samuelterra22](https://github.com/samuelterra22)

---

## Começando

### Opção A — Sail (recomendado)

Só tem Docker, sem PHP nem Composer na máquina? Instale as dependências dentro de
um container descartável — este é o `composer install` sem instalar nada local:

```bash
git clone https://github.com/samuelterra22/workshop-ufla-laravel.git
cd workshop-ufla-laravel

docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

- `-u "$(id -u):$(id -g)"` gera os arquivos com o seu usuário, não como `root`.
- `-v "$(pwd):/var/www/html"` monta o projeto dentro do container.
- `--ignore-platform-reqs` porque quem valida extensões PHP é o container do Sail,
  não a sua máquina.

Depois disso o `vendor/bin/sail` já existe e o resto roda por Docker:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer setup
```

> Já tem PHP e Composer local? Troque o `docker run …` por um simples
> `composer install`.

Painel em <http://localhost/admin> · usuário `admin@workshop.test` · senha `password`

### Opção B — local com SQLite

```bash
composer install
composer setup      # cria .env, gera chave, migra e popula
php artisan serve
```

### Opção C — GitHub Codespaces

Botão `Code` → aba `Codespaces` → `Create codespace`. O devcontainer instala
tudo sozinho.

### Aliases que economizam digitação

```bash
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'
```

---

## O quality gate

```bash
composer check
```

Sem PHP nem Composer local? Rode pelo container do Sail — mesmo comando, prefixo
`./vendor/bin/sail`:

```bash
./vendor/bin/sail composer check
```

Roda, em ordem do mais barato para o mais caro:

| # | Checagem | Comando | O que pega |
|---|---|---|---|
| 1 | Pint | `composer test:lint` | Formatação fora do padrão |
| 2 | Rector | `composer test:refactor` | Sintaxe antiga, código morto |
| 3 | PHPStan + Larastan | `composer test:types` | Erro de tipo, contrato quebrado |
| 4 | PHPInsights | `composer test:insights` | Complexidade e arquitetura |
| 5 | Pest | `composer test:unit` | Comportamento e arquitetura |
| 6 | Cobertura | `composer test:coverage` | Código sem teste |
| 7 | composer audit | `composer test:security` | CVE em dependência |
| 8 | Docker build | no CI | Imagem de produção quebrada |

O mesmo `composer check` roda na sua máquina e no runner. Nunca dois
comportamentos diferentes.

**Na fila para entrar:** `peckphp/peck` (erros de escrita) e
`pestphp/pest-plugin-type-coverage` (percentual de código tipado).

---

## O laboratório

Este repositório tem **três defeitos plantados de propósito**. Veja
[`LAB.md`](LAB.md) para o passo a passo. Resumo:

```bash
composer check                    # falha em três lugares diferentes
./vendor/bin/sail composer check  # mesma coisa, se você só tem Docker
```

Sua missão é deixar verde. Cada defeito é pego por uma ferramenta diferente, o
que é justamente o ponto: nenhuma delas sozinha resolveria.

---

## O que olhar primeiro

| Quero entender… | Comece por |
|---|---|
| Como a regra de negócio fica testável | `app/Actions/Pedidos/CriarPedido.php` |
| Por que DTO em vez de array | `app/Data/PedidoData.php` |
| Regra de negócio junto do tipo | `app/Enums/StatusPedido.php` |
| Onde a validação mora | `app/Http/Requests/StorePedidoRequest.php` |
| Painel administrativo no v4 | `app/Filament/Resources/Clientes/` |
| Resource chamando a Action | `app/Filament/Resources/Pedidos/PedidoResource.php` |
| Trabalho assíncrono | `app/Jobs/GerarNotaFiscal.php` |
| Regras de arquitetura como teste | `tests/Arch.php` |
| Proteção contra N+1 | `app/Providers/AppServiceProvider.php` |
| O pipeline | `.github/workflows/quality-gate.yml` |
| Contexto para agente de IA | `CLAUDE.md`, `SPEC.md`, `TASKS.md` |
| Monitoramento da infra | `docs/monitoramento/` |

---

## Estrutura

```
app/
├── Actions/Pedidos/      casos de uso: CriarPedido, CancelarPedido
├── Data/                 DTOs imutáveis que atravessam as camadas
├── Enums/                estados com regra de negócio junto do tipo
├── Events/ Jobs/         efeito colateral fora do request
├── Filament/Resources/   painel: Clientes (estrutura v4) e Pedidos (arquivo único)
├── Http/                 controller magro + Form Request
├── Models/               Eloquent, preso na camada de persistência
├── Policies/             autorização
├── Providers/            preventLazyLoading, alerta de query lenta
└── Services/             ReservaEstoque

docs/
├── monitoramento/        Prometheus, Alertmanager, cAdvisor, Grafana, Telegram
└── ia/                   como o agente entra no ciclo

tests/
├── Arch.php              as sete regras, cobradas pela máquina
├── Unit/Actions/         regra de negócio sem HTTP
└── Feature/              rota, fila e painel
```

---

## Aviso honesto

Este repositório foi escrito para o workshop e **as dependências não foram
instaladas no ambiente onde ele foi gerado**. A sintaxe de todos os arquivos PHP
foi verificada, mas nenhum comando foi executado contra o Laravel real.

Traduzindo: espere ajustar algumas coisas no primeiro `composer install`,
especialmente na camada Filament, cuja API v4 é grande. Se algo não subir, abra
uma issue — a correção também é aprendizado.

---

## Licença

MIT. Use, copie, modifique e leve para o seu projeto sem pedir licença.
