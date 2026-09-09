<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Testes de arquitetura
|--------------------------------------------------------------------------
|
| As sete regras do workshop deixam de ser combinado verbal e passam a ser
| cobradas pela máquina. Rodam junto com o resto da suíte e custam
| milissegundos.
|
| Limite honesto: isto verifica ESTRUTURA, não semântica. Garante que a
| Action é final e imutável, não que a regra dentro dela está certa.
|
*/

arch('nada de debug esquecido no código')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'die', 'exit'])
    ->not->toBeUsed();

arch('tipagem estrita em todo o projeto')
    ->expect('App')
    ->toUseStrictTypes();

arch('actions são finais, imutáveis e têm um único método público')
    ->expect('App\Actions')
    ->toBeFinal()
    ->toBeReadonly()
    ->toHaveMethod('handle');

arch('DTOs são objetos de valor imutáveis')
    ->expect('App\Data')
    ->toBeFinal()
    ->toBeReadonly();

arch('enums ficam em App\Enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('models não vazam para o controller')
    ->expect('App\Models')
    ->not->toBeUsedIn('App\Http\Controllers');

arch('jobs implementam ShouldQueue')
    ->expect('App\Jobs')
    ->toImplement(Illuminate\Contracts\Queue\ShouldQueue::class);

arch('nada de facade de banco fora da camada de aplicação')
    ->expect(Illuminate\Support\Facades\DB::class)
    ->not->toBeUsedIn(['App\Http\Controllers', 'App\Filament']);

arch('presets do Laravel')
    ->preset()
    ->laravel();

arch('nada de código inseguro')
    ->preset()
    ->security();
