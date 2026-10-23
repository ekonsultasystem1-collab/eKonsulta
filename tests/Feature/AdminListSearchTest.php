<?php

namespace Tests\Feature;

use App\Models\HealthCenter;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_service_and_health_center_searches_filter_results(): void
    {
        $admin = $this->createUser('admin@example.test', 'Administrator', 'admin');
        $this->createUser('matching@example.test', 'Matching User', 'staff');
        $this->createUser('other@example.test', 'Other User', 'staff');

        Service::create([
            'name' => 'Matching Service',
            'code' => 'MATCH',
            'status' => true,
        ]);
        Service::create([
            'name' => 'Other Service',
            'code' => 'OTHER',
            'status' => true,
        ]);

        HealthCenter::create([
            'name' => 'Matching Health Center',
            'barangay' => 'Matching',
            'address' => 'Test Address',
            'status' => 'active',
        ]);
        HealthCenter::create([
            'name' => 'Other Health Center',
            'barangay' => 'Other',
            'address' => 'Other Address',
            'status' => 'active',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['search' => 'Matching']))
            ->assertOk()
            ->assertSee('<details class="user-menu" id="userMenu">', false)
            ->assertSee('Logout')
            ->assertDontSee('data-list-sort-select')
            ->assertDontSee('Newest first')
            ->assertDontSee('Oldest first')
            ->assertSee('value="Matching"', false)
            ->assertSee('Matching User')
            ->assertDontSee('Other User');

        $this->actingAs($admin)
            ->get(route('admin.services.index', ['search' => 'MATCH']))
            ->assertOk()
            ->assertSee('value="MATCH"', false)
            ->assertSee('Matching Service')
            ->assertDontSee('Other Service');

        $this->actingAs($admin)
            ->get(route('admin.health-centers.index', ['search' => 'Matching']))
            ->assertOk()
            ->assertSee('value="Matching"', false)
            ->assertSee('Matching Health Center')
            ->assertDontSee('Other Health Center');
    }

    private function createUser(string $email, string $name, string $role): User
    {
        return User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
