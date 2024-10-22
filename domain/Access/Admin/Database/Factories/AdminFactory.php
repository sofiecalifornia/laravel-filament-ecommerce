<?php

declare(strict_types=1);

namespace Domain\Access\Admin\Database\Factories;

use Domain\Access\Admin\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Domain\Access\Admin\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected $model = Admin::class;

    #[\Override]
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => app()->environment('local', 'testing') ? 'secret' : fake()->password(minLength: 15),
            'remember_token' => Str::random(10),
            'timezone' => config('app-default.timezone'),
            'google2fa_secret' => null,
        ];
    }

    public function withGoogle2fa(): self
    {
        return $this->state([
            'google2fa_secret' => '3CMLPJRWJOYEDEQL',
        ]);
    }

    public function unverified(): self
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
