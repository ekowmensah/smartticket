<?php

namespace Database\Factories;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Enums\OrganizationType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'public_id' => (string) Str::ulid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => fake()->randomElement(OrganizationType::cases()),
            'public_email' => fake()->companyEmail(),
            'public_phone' => '+233'.fake()->unique()->numerify('#########'),
            'status' => OrganizationStatus::PENDING,
            'approval_status' => OrganizationApprovalStatus::PENDING,
            'timezone' => 'Africa/Accra',
            'currency_code' => 'GHS',
            'country_code' => 'GH',
            'created_by' => User::factory(),
        ];
    }
}
