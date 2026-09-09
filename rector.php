<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ])
    ->withPhpSets(php83: true)
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    ->withSkip([
        __DIR__ . '/app/Filament',
        __DIR__ . '/bootstrap/cache',
    ]);

// Para migrações do Filament v3 -> v4 existe um conjunto de regras próprio:
//   composer require --dev leek/rector-filament
