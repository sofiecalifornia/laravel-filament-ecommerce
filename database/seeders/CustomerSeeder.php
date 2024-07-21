<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\Shop\Customer\Database\Factories\AddressFactory;
use Domain\Shop\Customer\Database\Factories\CustomerFactory;
use Domain\Shop\Customer\Enums\Gender;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        CustomerFactory::new()
            ->active()
            ->has(AddressFactory::new())
            ->createOne([
                'gender' => Gender::MALE,
                'first_name' => 'Lloric',
                'last_name' => 'Garcia',
                'email' => 'lloricode@gmail.com',
                'password' => 'secret',
            ]);
    }
}
