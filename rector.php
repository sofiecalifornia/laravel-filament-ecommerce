<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/database',
        __DIR__.'/domain',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ]);

    $rectorConfig->sets([
        LaravelSetList::LARAVEL_100,
        SetList::PHP_82,
    ]);

    $rectorConfig->rules([
        Rector\Php55\Rector\ClassConstFetch\StaticToSelfOnFinalClassRector::class,
        Rector\Php72\Rector\FuncCall\GetClassOnNullRector::class,
        Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector::class,
        Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector::class,
    ]);

    $rectorConfig->phpVersion(PhpVersion::PHP_82);

    //    $rectorConfig->phpstanConfig(__DIR__ . '/phpstan.neon');
};
