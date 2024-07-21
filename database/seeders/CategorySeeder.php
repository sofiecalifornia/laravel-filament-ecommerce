<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Category\Database\Factories\CategoryFactory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        CategoryFactory::new(['name' => 'Technology'])
            ->isVisibleStatus()
            ->has(
                CategoryFactory::new(['name' => 'Mobile'])
                    ->isVisibleStatus()
                    ->hasRandomMedia(),
                'children'
            )
            ->hasRandomMedia()
            ->createOne();
    }
}
