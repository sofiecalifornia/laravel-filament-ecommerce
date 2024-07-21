<?php

declare(strict_types=1);

use Domain\Shop\Branch\Database\Factories\BranchFactory;
use Domain\Shop\Branch\Models\Branch;

use function Pest\Laravel\assertDatabaseEmpty;
use function Pest\Laravel\getJson;

beforeEach(fn () => config(['media-library.version_urls' => false]));

it('list', function () {

    assertDatabaseEmpty(Branch::class);

    BranchFactory::new()
        ->hasSpecificMedia()
        ->enabled()
        ->count(3)
        ->sequence(
            [
                'name' => 'Branch 1',
            ],
            [
                'name' => 'Branch 2',
            ],
            [
                'name' => 'Branch 3',
            ],
        )
        ->create();

    $response = getJson('api/branches?include=media')
        ->assertOk();

    expect($response)->toMatchSnapshot();
});
