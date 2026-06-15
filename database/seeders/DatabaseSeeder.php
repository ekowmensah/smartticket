<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function setPermissionsTeamId;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $platformAdmin = User::factory()->create([
            'name' => 'Platform Admin',
            'email' => 'platform.admin@smartcast.test',
            'phone_number' => '+233200000001',
            'status' => UserStatus::ACTIVE,
        ]);

        setPermissionsTeamId(\App\Support\PermissionScope::PLATFORM);
        $platformAdmin->assignRole('super-admin');
    }
}
