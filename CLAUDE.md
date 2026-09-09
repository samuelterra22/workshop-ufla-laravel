# Convenções do projeto

Este arquivo é lido por agentes de IA (Claude Code, Cursor, Codex, Copilot) no
início de toda sessão. Ele também serve de referência para pessoas.

`AGENTS.md` é uma cópia deste arquivo, para ferramentas que procuram por esse nome.

## Stack

- Laravel 12 · PHP 8.3 · PostgreSQL 17 · Redis
- Filament 4 para o painel administrativo · Livewire 3
- Pest 3 para testes · PHPStan nível 6 (Larastan) · Pint preset `laravel`
- Octane sobre FrankenPHP em produção; Sail em desenvolvimento

## Arquitetura — regras não negociáveis

1. **Controller magro.** Recebe, delega, devolve. Máximo 20 linhas por método.
2. **Validação e autorização em Form Request**, nunca no controller nem no model.
3. **Um caso de uso, uma Action** em `app/Actions`, com um único método público
   `handle()`. Nome no imperativo: `CriarPedido`, `CancelarPedido`.
4. **Dado que atravessa camada é DTO tipado** (`app/Data`), nunca array solto.
5. **Nenhuma lógica em Blade.** `@if` de permissão é aceitável; cálculo, não.
6. **Escrita que toca mais de uma tabela vive dentro de `DB::transaction()`.**
7. **Efeito colateral** (e-mail, webhook, PDF, integração) sai por Job ou Event.
8. **Nenhuma alteração de banco fora de migration.**

O Resource do Filament ocupa o mesmo lugar de um controller. Ele é camada HTTP:
a regra de negócio vai para a Action, e a ação do painel apenas a chama.

## Antes de considerar qualquer tarefa concluída

```bash
composer check
```

Rode e só pare quando estiver verde. Se falhar, leia a saída, corrija e rode de
novo. Não peça revisão com o gate vermelho.

Ambiente só com Docker (sem PHP nem Composer local): rode tudo pelo Sail —
`./vendor/bin/sail composer check`. As dependências instalam por container
descartável: `docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html"
-w /var/www/html laravelsail/php83-composer:latest composer install
--ignore-platform-reqs`.

## Nunca faça sem perguntar

- Instalar dependência nova
- Criar migration destrutiva (drop, rename de coluna com dado)
- Alterar autenticação, autorização ou qualquer regra de permissão
- Mexer em cálculo financeiro ou fiscal
- Alterar arquivos de CI, Dockerfile ou configuração de deploy
- Rodar qualquer comando contra banco que não seja local

## Convenções de Git

- Branch: `feat/`, `fix/`, `chore/`, `refactor/`, `docs/`
- Commit: Conventional Commits, em português, no imperativo
- Um pull request por caso de uso, com o que muda e como testar

## Onde as coisas ficam

```
app/Actions/<Contexto>/    casos de uso
app/Data/                  DTOs imutáveis
app/Enums/                 estados e categorias com regra junto do tipo
app/Services/              colaboradores com estado ou integração externa
app/Jobs/                  trabalho assíncrono, com política de falha explícita
app/Filament/Resources/    painel administrativo (camada HTTP)
tests/Arch.php             as regras acima, cobradas pela máquina
```
