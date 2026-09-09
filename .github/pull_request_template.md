## O que muda

<!-- Uma frase. Se precisar de três parágrafos, o PR está grande demais. -->

## Por quê

<!-- O problema de negócio, não a solução técnica. -->

## Como testar

```bash
composer check
# comando específico que prova que este PR funciona
```

## Checklist

- [ ] `composer check` verde localmente
- [ ] Regra de negócio na Action, não no controller nem no Resource
- [ ] Alteração de banco via migration
- [ ] Efeito colateral (e-mail, integração, PDF) em Job ou Event
- [ ] Teste cobrindo o caminho crítico
