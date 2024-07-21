<?php

declare(strict_types=1);

test('Not debugging statements are left in our code.')
    ->expect(['dd', 'dump', 'ray', 'rd', 'die', 'eval', 'sleep'])
    ->not->toBeUsed();

function domainEnums(): array
{
    return [
        'Domain\Access\Admin\Enums',
        'Domain\Access\Role\Enums',
        'Domain\Shop\Branch\Enums',
        'Domain\Shop\Brand\Enums',
        'Domain\Shop\Category\Enums',
        'Domain\Shop\Customer\Enums',
        'Domain\Shop\Order\Enums',
        'Domain\Shop\Product\Enums',
        'Domain\Shop\Stock\Enums',
    ];
};
function domainActions(): array
{
    return [
        'Domain\Access\Admin\Actions',
        'Domain\Access\Role\Actions',
        'Domain\Shop\Branch\Actions',
        'Domain\Shop\Brand\Actions',
        'Domain\Shop\Category\Actions',
        'Domain\Shop\Customer\Actions',
        'Domain\Shop\Order\Actions',
        'Domain\Shop\Product\Actions',
        'Domain\Shop\Stock\Actions',
    ];
};
function domainDTOs(): array
{
    return [
        'Domain\Access\Admin\DataTransferObjects',
        'Domain\Access\Role\DataTransferObjects',
        'Domain\Shop\Branch\DataTransferObjects',
        'Domain\Shop\Brand\DataTransferObjects',
        'Domain\Shop\Category\DataTransferObjects',
        'Domain\Shop\Customer\DataTransferObjects',
        'Domain\Shop\Order\DataTransferObjects',
        'Domain\Shop\Product\DataTransferObjects',
        'Domain\Shop\Stock\DataTransferObjects',
    ];
};
function domainModels(): array
{
    return [
        'Domain\Access\Admin\Models',
        'Domain\Access\Role\Models',
        'Domain\Shop\Branch\Models',
        'Domain\Shop\Brand\Models',
        'Domain\Shop\Category\Models',
        'Domain\Shop\Customer\Models',
        'Domain\Shop\Order\Models',
        'Domain\Shop\Product\Models',
        'Domain\Shop\Stock\Models',
    ];
};
function domainModelQueries(): array
{
    return [
        'Domain\Access\Admin\Models\Query',
        'Domain\Access\Role\Models\Query',
        'Domain\Shop\Branch\Models\Query',
        'Domain\Shop\Brand\Models\Query',
        'Domain\Shop\Category\Models\Query',
        'Domain\Shop\Customer\Models\Query',
        'Domain\Shop\Order\Models\Query',
        'Domain\Shop\Product\Models\Query',
        'Domain\Shop\Stock\Models\Query',
    ];
};
function domainObservers(): array
{
    return [
        'Domain\Access\Admin\Observers',
        'Domain\Access\Role\Observers',
        'Domain\Shop\Branch\Observers',
        'Domain\Shop\Brand\Observers',
        'Domain\Shop\Category\Observers',
        'Domain\Shop\Customer\Observers',
        'Domain\Shop\Order\Observers',
        'Domain\Shop\Product\Observers',
        'Domain\Shop\Stock\Observers',
    ];
};
function domainPolicies(): array
{
    return [
        'Domain\Access\Admin\Policies',
        'Domain\Access\Role\Policies',
        'Domain\Shop\Branch\Policies',
        'Domain\Shop\Brand\Policies',
        'Domain\Shop\Category\Policies',
        'Domain\Shop\Customer\Policies',
        'Domain\Shop\Order\Policies',
        'Domain\Shop\Product\Policies',
        'Domain\Shop\Stock\Policies',
    ];
};
function domainFactories(): array
{
    return [
        'Domain\Access\Admin\Database\Factories',
        'Domain\Access\Role\Database\Factories',
        'Domain\Shop\Branch\Database\Factories',
        'Domain\Shop\Brand\Database\Factories',
        'Domain\Shop\Category\Database\Factories',
        'Domain\Shop\Customer\Database\Factories',
        'Domain\Shop\Order\Database\Factories',
        'Domain\Shop\Product\Database\Factories',
        'Domain\Shop\Stock\Database\Factories',
    ];
};
function domainRules(): array
{
    return [
        'Domain\Access\Admin\Rules',
        'Domain\Access\Role\Rules',
        'Domain\Shop\Branch\Rules',
        'Domain\Shop\Brand\Rules',
        'Domain\Shop\Category\Rules',
        'Domain\Shop\Customer\Rules',
        'Domain\Shop\Order\Rules',
        'Domain\Shop\Product\Rules',
        'Domain\Shop\Stock\Rules',
    ];
};

test('domain enums')
    ->with(fn () => domainEnums())
    ->expect(fn (string $folder) => $folder)
    ->toBeEnums();

test('domain actions')
    ->with(fn () => domainActions())
    ->expect(fn (string $folder) => $folder)
    // ->toUseNothing() // ErrorException: Attempt to read property "stmts" on null ...
    ->toImplementNothing()
    ->toExtendNothing()
    ->toBeFinal()
    ->toBeReadonly()
    ->toHaveSuffix('Action')
    ->not->toUse('app');

test('domain DTOs')
    ->with(fn () => domainDTOs())
    ->expect(fn (string $folder) => $folder)
    // ->toUseNothing() // ErrorException: Attempt to read property "stmts" on null ...
    ->toImplementNothing()
    ->toExtendNothing()
    ->toBeFinal()
    ->toBeReadonly()
    ->toHaveSuffix('Data');

test('domain models')
    ->with(fn () => domainModels())
    ->expect(fn (string $folder) => $folder)
    ->toExtend(Illuminate\Database\Eloquent\Model::class)
    ->ignoring(domainModelQueries());

test('domain model queries')
    ->with(fn () => domainModelQueries())
    ->expect(fn (string $folder) => $folder)
    ->toExtend(Illuminate\Database\Eloquent\Builder::class);

test('domain model factories')
    ->with(fn () => domainFactories())
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Factory')
    ->toExtend(Illuminate\Database\Eloquent\Factories\Factory::class);

test('model rules')
    ->with(fn () => domainRules())
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Rule')
    ->toImplement(Illuminate\Contracts\Validation\ValidationRule::class);

test('controllers')
    ->expect('App\Http\Controllers')
    ->toExtendNothing() // laravel 11 will delete abstract controller class
    ->toHaveSuffix('Controller');

test('model resources')
    ->expect('App\Http\Resources')
    ->toHaveSuffix('Resource')
    ->toExtend(TiMacDonald\JsonApi\JsonApiResource::class);

test('do not use Illuminate\Http in domain')
    ->expect('Illuminate\Http')
    ->not->toBeUsedIn('Domain');

test('settings')
    ->expect('App\Settings')
    ->toHaveSuffix('Settings')
    ->toExtend(Spatie\LaravelSettings\Settings::class);

test('listeners')
    ->expect('App\Listeners')
    ->toHaveSuffix('Listener')
    ->toExtendNothing();

test('policies')
    ->with(fn () => array_merge(domainPolicies(), ['App\Policies']))
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Policy')
    ->toImplementNothing()
    ->toExtendNothing();

test('domain observers')
    ->with(fn () => domainObservers())
    ->expect(fn (string $folder) => $folder)
    ->toHaveSuffix('Observer')
    ->toImplementNothing()
    ->toExtendNothing();

//test('domain models only use Illuminate\Database')
//    ->expect(fn () => domainModels())
//    ->toOnlyUse('Illuminate\Database')->only();

//test('domain models usage')
//    ->expect(fn () => domainModels())
//    ->toOnlyBeUsedIn(array_merge(
//        domainActions(),
//        domainObservers(),
//        domainPolicies(),
//        domainFactories(),
//        domainModels(),
//        domainRules(),
//        domainDTOs(),
//        [
//            // Expecting 'Domain\Shop\Order\Models\Order' not to be used on 'Domain\Shop\Order\Exports\ExportOrder'.
//            //at domain/Shop/Order/Exports/ExportOrder.php:7
//            'Domain\Shop\Order\Exports',
//            'Database\Seeders',
//            'App\Listeners',
//            'App\Helpers',
//            'App\Http\Resources',
//        ],
//    ));

//test('domain queue')
//    ->expect('App\Jobs')
//    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');
