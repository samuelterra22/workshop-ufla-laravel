# Tarefas

> Modelo de backlog consumível por agente. Cada item é pequeno, verificável e
> traz o comando que prova que ficou pronto.

## Spec atual: desconto por cupom

- [ ] 1. Migration e model `cupons` (código, percentual, validade)
      → `php artisan migrate:fresh && vendor/bin/pest --filter=Cupom`
- [ ] 2. `CupomData` em `app/Data`, imutável e tipado
      → `composer test:types`
- [ ] 3. Enum `MotivoCupomIgnorado` com os casos expirado e canal não elegível
      → `composer test:types`
- [ ] 4. Action `AplicarCupom` com um único método `handle()`
      → `vendor/bin/pest --filter=AplicarCupom`
- [ ] 5. Integrar em `CriarPedido` sem quebrar os testes existentes
      → `vendor/bin/pest --filter=CriarPedido`
- [ ] 6. Regra no `StorePedidoRequest`: no máximo um cupom
      → `vendor/bin/pest --filter=PedidoController`
- [ ] 7. Teste de arquitetura das classes novas
      → `vendor/bin/pest --filter=arch`
- [ ] 8. Gate inteiro verde
      → `composer check`
