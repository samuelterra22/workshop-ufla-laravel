# IA no ciclo de trabalho

## O ciclo

```
spec  →  agente  →  gate  →  review
           ↑                    │
           └── volta enquanto o gate estiver vermelho
```

O quality gate deixa de ser apenas controle de qualidade e passa a ser o
**critério de parada do agente**. Sem ele, o agente não sabe quando terminou e
quem revisa não sabe quando pode confiar.

## Os três arquivos

| Arquivo | O que é |
|---|---|
| `CLAUDE.md` / `AGENTS.md` | As regras da casa: stack, arquitetura, o que nunca alterar |
| `SPEC.md` | O que construir: contexto, regra em linguagem de negócio, critério de aceitação |
| `TASKS.md` | A ordem de execução: tarefas pequenas com o comando que prova que ficaram prontas |

Contexto que fica no Git, não no chat. Qualquer sessão nova começa sabendo tudo,
e mudança de contexto passa por pull request como qualquer outro código.

## Laravel Boost

```bash
composer require laravel/boost --dev
php artisan boost:install
```

Instala um servidor MCP que expõe o projeto para o agente: inspeção de rotas,
banco, models e configuração, execução de Tinker no contexto real, e uma API de
documentação filtrada pelas versões que você realmente instalou.

Não confunda os três pacotes de IA do Laravel:

| Pacote | Para quê |
|---|---|
| **Laravel Boost** | O agente escreve Laravel melhor (ferramenta de desenvolvimento) |
| **Laravel MCP** | Você expõe a sua aplicação para ferramentas de IA externas |
| **Laravel AI SDK** | Você coloca LLM dentro do seu produto |

## Configuração do agente versionada

O diretório `~/.claude` vive em repositório próprio:

```
~/.claude/
├── CLAUDE.md        # regras globais do usuário
├── settings.json    # permissões, modelo, variáveis
├── skills/          # habilidades reutilizáveis
├── commands/        # slash commands próprios
├── agents/          # subagentes especializados
├── hooks/           # scripts disparados por evento do ciclo
└── .mcp.json        # servidores MCP conectados
```

M�quina nova reproduz o ambiente com um clone. Mudança de comportamento do
agente vira commit, com histórico e possibilidade de reverter. Skill escrita
para um projeto passa a valer para todos.

## Rotina de melhoria contínua

Tarefa agendada que:

1. **Coleta** — varre os prompts enviados em todos os repositórios nos últimos 7 dias
2. **Agrupa** — identifica instrução repetida, correção recorrente, contexto sempre recolado
3. **Propõe** — sugere skill nova, subagente ou ajuste no `CLAUDE.md`
4. **Aprova** — a proposta passa por revisão antes de virar commit

O princípio: **instrução que precisou ser repetida três vezes é uma skill que
ainda não existe.**

## O que delego e o que não delego

| Delego | Nunca sem revisar linha a linha |
|---|---|
| CRUD, migrations, factories, seeders | Autenticação, autorização, permissão |
| Testes do caminho feliz e borda óbvia | Cálculo financeiro ou fiscal |
| Refatoração mecânica e migração de versão | Migration destrutiva, dado em produção |
| Documentação, changelog, mensagem de commit | Arquitetura e dependência nova |
| Script de automação e comando Artisan | Código que trata dado sensível de pessoa |

A regra que fecha as duas colunas: **eu assino o pull request.** A
responsabilidade pelo que entra em produção é de quem aprova, não do modelo.
