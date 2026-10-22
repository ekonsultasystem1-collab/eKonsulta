<?php

namespace Tests\Feature;

use App\Models\HealthCenter;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminListSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_users_services_and_health_centers_sort_on_the_server(): void
    {
        $admin = $this->createUser('admin@example.test', 'Administrator', 'admin', '2024-01-01');
        $this->createUser('older@example.test', 'Older User', 'staff', '2020-01-01');
        $this->createUser('newer@example.test', 'Newer User', 'staff', '2025-01-01');

        $this->createService('Older Service', 'OLDER', '2020-01-01');
        $this->createService('Newer Service', 'NEWER', '2025-01-01');

        $this->createHealthCenter('Older Health Center', '2020-01-01');
        $this->createHealthCenter('Newer Health Center', '2025-01-01');

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['sort' => 'oldest', 'search' => 'User']))
            ->assertOk()
            ->assertSee('value="User"', false)
            ->assertSeeInOrder(['Older User', 'Newer User']);

        $this->actingAs($admin)
            ->get(route('admin.users.index', ['sort' => 'newest']))
            ->assertSeeInOrder(['Newer User', 'Older User']);

        $this->actingAs($admin)
            ->get(route('admin.services.index', ['sort' => 'oldest']))
            ->assertOk()
            ->assertSeeInOrder(['Older Service', 'Newer Service']);

        $this->actingAs($admin)
            ->get(route('admin.services.index', ['sort' => 'newest']))
            ->assertSeeInOrder(['Newer Service', 'Older Service']);

        $this->actingAs($admin)
            ->get(route('admin.health-centers.index', ['sort' => 'oldest']))
            ->assertOk()
            ->assertSeeInOrder(['Older Health Center', 'Newer Health Center']);

        $this->actingAs($admin)
            ->get(route('admin.health-centers.index', ['sort' => 'newest']))
            ->assertSeeInOrder(['Newer Health Center', 'Older Health Center']);
    }

    private function createUser(string $email, string $name, string $role, string $createdAt): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
            'status' => 'active',
        ]);

        $this->setCreationDate($user, $createdAt);

        return $user;
    }

    private function createService(string $name, string $code, string $createdAt): Service
    {
        $service = Service::create([
            'name' => $name,
            'code' => $code,
            'status' => true,
        ]);

        $this->setCreationDate($service, $createdAt);

        return $service;
    }

    private function createHealthCenter(string $name, string $createdAt): HealthCenter
    {
        $healthCenter = HealthCenter::create([
            'name' => $name,
            'barangay' => 'Test Barangay',
            'address' => 'Test Address',
            'status' => 'active',
        ]);

        $this->setCreationDate($healthCenter, $createdAt);

        return $healthCenter;
    }

    private function setCreationDate(User|Service|HealthCenter $model, string $createdAt): void
    {
        $date = Carbon::parse($createdAt);
        $model->forceFill(['created_at' => $date, 'updated_at' => $date])->saveQuietly();
    }
}
