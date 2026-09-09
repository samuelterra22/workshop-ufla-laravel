# Spec: aplicar desconto por cupom no pedido

> Modelo de especificação. Copie este arquivo para cada caso de uso novo,
> preencha e abra o pull request com ele junto do código.

## Contexto

Hoje o total do pedido é a soma dos itens, sem nenhum abatimento. Comercial
precisa oferecer cupom de desconto em campanhas pontuais. Quem usa: atendimento
pelo balcão e o próprio cliente no site.

## Regra de negócio

- Um cupom tem código, percentual de desconto e data de validade.
- O desconto incide sobre o subtotal dos itens, nunca sobre o frete.
- Cupom expirado ou inexistente não bloqueia o pedido: o pedido é criado sem
  desconto e o atendente é avisado.
- Um pedido aceita no máximo um cupom.
- Pedido vindo do canal `api` não aceita cupom.

## Critério de aceitação

- [ ] Dado um cupom válido de 10%, quando o subtotal é R$ 100,00, então o total
      é R$ 90,00
- [ ] Dado um cupom expirado, quando o pedido é criado, então o total é o
      subtotal cheio e nenhuma exceção é lançada
- [ ] Dado o canal `api`, quando um cupom é informado, então ele é ignorado
- [ ] Dado um segundo cupom, quando informado, então a validação recusa
- [ ] Cobertura de 100% do caminho crítico de `AplicarCupom`

## Fora de escopo

- Cupom de valor fixo (só percentual nesta entrega)
- Cupom por cliente ou por produto
- Tela de gestão de cupons no painel
- Relatório de uso de cupom

Esta seção evita mais retrabalho que todas as outras juntas.

## Como validar

```bash
composer check
vendor/bin/pest --filter=AplicarCupom
```
