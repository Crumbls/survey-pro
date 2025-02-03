<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'primary_color' => $this->faker->hexColor(),
            'secondary_color' => $this->faker->hexColor(),
            'accent_color' => $this->faker->hexColor(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Tenant $tenant) {
            if (!$tenant->users()->count()) {
                $user = User::factory()->create();

                // Attach user to tenant with default admin role
                $role = Role::firstOrCreate(['name' => 'center-owner'], ['title' => 'Center Owner']);

                $tenant->users()->attach($user->id, [
                    'role_id' => $role->id,
                ]);
                print_r($tenant);
            }
        });
    }
}
