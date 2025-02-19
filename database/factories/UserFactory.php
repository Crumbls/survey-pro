<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user) {
            // 30% chance to attach to existing tenant, 70% chance to create new one
            $attachToExisting = fake()->boolean(30);

            if (($attachToExisting && Tenant::count() > 0)) {
                $tenant = Tenant::inRandomOrder()->first();
            } else {
                $service = app(\App\Services\TenantService::class);
                $tenant = $service->getOrCreateDefault($user);
            }

            if ($user->tenants()->where('tenants.id', $tenant->getKey())->count()) {
                return;
            }

            $role = $tenant->roles()
                ->firstOrCreate([
                    'title' => 'Center Owner'
                ]);

            if ($role->users->count() > 0) {
                $role = $tenant
                    ->roles()
                    ->where('roles.id', '<>', $role->getKey())
                    ->inRandomOrder()
                    ->take(1)
                    ->first();
            }
            if (!$role) {
                $role = $tenant
                    ->roles()
                    ->create([
                        'title' => 'Test Role'
                    ]);
            }

            $user->tenants()->attach($tenant, [
                'role_id' =>
                    $role->getKey()
            ]);

            $user->save();
        });
    }
}
