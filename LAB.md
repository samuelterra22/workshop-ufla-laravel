# Laboratório: do vermelho ao verde

Este repositório tem **três defeitos plantados**. Cada um é pego por uma
ferramenta diferente do quality gate. Sua missão é deixar `composer check` verde.

Tempo estimado: 25 a 30 minutos.

---

## Passo 1 — Preparar

```bash
composer install
composer setup
```

Se você usa Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail composer setup
```

---

## Passo 2 — Rodar o gate e ver falhar

```bash
composer check
```

Leia a saída com calma antes de corrigir qualquer coisa. Repare que a execução
para na primeira falha: é o `fail fast`, e é de propósito. Rodar o mais barato
primeiro economiza tempo de espera.

---

## Passo 3 — Corrigir os três defeitos

### Defeito 1 — Formatação

**Quem pega:** Pint
**Onde:** `app/Http/Controllers/PedidoController.php`

O arquivo tem aspas duplas onde a convenção pede simples, imports fora de ordem,
espaçamento dentro de parênteses e falta `declare(strict_types=1)`.

```bash
composer lint     # corrige sozinho
git diff          # veja o que mudou
```

Este é o defeito fácil. Ele existe para mostrar uma coisa: **discussão sobre
formatação em code review é tempo humano desperdiçado**. A máquina resolve.

---

### Defeito 2 — Erro de tipo

**Quem pega:** PHPStan + Larastan (nível 6)
**Onde:** `app/Actions/Pedidos/CancelarPedido.php`

```bash
composer test:types
```

Não existe correção automática. Leia a mensagem, entenda o contrato e ajuste.

Dicas, em ordem de quanto entregam:

1. O que `ReservaEstoque::devolver()` declara receber?
2. O que `$pedido->itens` devolve de verdade?
3. Existe um método público em `ReservaEstoque` que resolve exatamente isso e
   que ninguém está chamando.

Este defeito também deixa um teste vermelho, e a comparação entre as duas saídas
é o ponto do exercício:

- O **teste** diz que quebrou.
- O **PHPStan** diz qual linha, qual contrato e o que era esperado.

As duas ferramentas são complementares. Nenhuma substitui a outra.

### Defeito 3 — Regra de negócio errada

**Quem pega:** Pest
**Onde:** `app/Actions/Pedidos/CriarPedido.php`

```bash
composer test:unit
```

O teste `soma o total considerando a quantidade de cada item` está vermelho.

**Leia o teste primeiro, o código depois. O teste está certo.**

Dica: existe um método em `PedidoData` que faz exatamente a conta correta e não
está sendo usado.

---

## Passo 4 — Abrir o pull request

```bash
git checkout -b fix/quality-gate
git add .
git commit -m "fix: corrige os três defeitos do laboratório"
git push -u origin fix/quality-gate
```

Abra o PR pelo GitHub. O workflow dispara sozinho.

---

## Passo 5 — Ver o check verde

Acompanhe a aba **Actions**. Repare em três coisas:

1. Os jobs `static`, `tests` e `image` rodam em paralelo
2. Os testes são divididos em 4 shards, também paralelos
3. O tempo de cada etapa, que é o que justifica a ordem do gate

Com branch protection ligada (`Settings → Rules`), o botão de merge fica
bloqueado até tudo passar. **Sem essa configuração, o pipeline roda mas não
impede nada.**

---

## Desafios extras

Terminou antes? Escolha um:

1. **Adicione uma nona checagem.** Sugestões: `peckphp/peck` (erros de escrita),
   `pestphp/pest-plugin-type-coverage`, [Infection](https://infection.github.io)
   (mutation testing), [Trivy](https://trivy.dev) na imagem Docker, CodeQL.

2. **Suba o PHPStan para o nível 8.** Gere um baseline primeiro
   (`vendor/bin/phpstan analyse --generate-baseline`) e vá reduzindo.

3. **Escreva a Action de cupom** descrita em `SPEC.md`, seguindo `TASKS.md`.
   Se estiver usando um agente de IA, é um bom exercício de Spec-Driven
   Development: passe os dois arquivos e deixe o gate ser o critério de parada.

4. **Quebre de propósito.** Adicione um `dd()` em qualquer lugar e veja o teste
   de arquitetura reprovar. Depois tire o `final` de uma Action e veja de novo.

---

## Gabarito

Não tem. Os três defeitos são pequenos e a saída de cada ferramenta diz
exatamente onde está o problema — ler essa saída é metade do exercício.

Se travar de verdade, o histórico do Git tem a resposta: procure o commit
`chore: planta os defeitos do laboratório`.
