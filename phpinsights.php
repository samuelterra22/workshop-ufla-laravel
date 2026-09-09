<?php

declare(strict_types=1);

use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenDefineFunctions;
use NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes;
use SlevomatCodingStandard\Sniffs\Commenting\UselessFunctionDocCommentSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;

return [
    'preset' => 'laravel',

    'exclude' => [
        'app/Filament',
        'bootstrap',
        'config',
        'database/migrations',
        'public',
        'resources',
        'storage',
    ],

    'add' => [
        Classes::class => [
            ForbiddenNormalClasses::class,
        ],
    ],

    'remove' => [
        ForbiddenDefineFunctions::class,
        UselessFunctionDocCommentSniff::class,
    ],

    'config' => [
        DeclareStrictTypesSniff::class => [
            'newlinesCountBetweenOpenTagAndDeclare' => 2,
            'spacesCountAroundEqualsSign' => 0,
        ],
    ],

    'requirements' => [
        'min-quality' => 80,
        'min-complexity' => 75,
        'min-architecture' => 80,
        'min-style' => 95,
        'disable-security-check' => false,
    ],
];
