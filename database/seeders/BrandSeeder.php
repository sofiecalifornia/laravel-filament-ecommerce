<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Brand\Database\Factories\BrandFactory;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        BrandFactory::new()
            ->hasRandomMedia()
            ->count(2)
            ->sequence(
                ['name' => 'Samsung'],
                ['name' => 'iPhone'],
            )
            ->create();
    }
}
