<?php

namespace Tests\Feature\Auth;

use App\Models\Organization;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone_number' => '+233201010101',
            'organization_name' => 'Test Events',
            'organization_type' => 'business',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $organization = Organization::query()->where('name', 'Test Events')->firstOrFail();

        $this->assertAuthenticated();
        $response->assertRedirect(route('organizer.dashboard', $organization, absolute: false));
    }
}
