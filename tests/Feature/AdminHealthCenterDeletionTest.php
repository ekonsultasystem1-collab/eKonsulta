<?php

namespace Tests\Feature;

use App\Models\AppointmentSchedule;
use App\Models\HealthCenter;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminHealthCenterDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_center_is_removed_from_active_lists_while_linked_history_is_preserved(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $healthCenter = HealthCenter::create([
            'name' => 'Napindan Health Center',
            'barangay' => 'Napindan',
            'address' => 'Test Address',
            'status' => 'active',
        ]);
        $service = Service::create([
            'name' => 'General Consultation',
            'code' => 'GENERAL',
            'status' => true,
        ]);
        $healthCenter->services()->attach($service);
        $schedule = AppointmentSchedule::create([
            'health_center_id' => $healthCenter->id,
            'service_id' => $service->id,
            'schedule_date' => today(),
            'appointment_time' => '09:00:00',
            'capacity' => 1,
        ]);

        $this->followingRedirects()
            ->actingAs($admin)
            ->delete(route('admin.health-centers.destroy', $healthCenter))
            ->assertOk()
            ->assertSee('Health center removed from active lists and patient booking. Linked records and history were preserved.')
            ->assertDontSee('Napindan Health Center');

        $this->assertDatabaseHas('health_centers', ['id' => $healthCenter->id]);
        $this->assertNotNull($healthCenter->fresh()->deleted_at);
        $this->assertSame('inactive', $healthCenter->fresh()->status);
        $this->assertDatabaseHas('services', ['id' => $service->id]);
        $this->assertDatabaseMissing('health_center_service', [
            'health_center_id' => $healthCenter->id,
            'service_id' => $service->id,
        ]);
        $this->assertDatabaseHas('appointment_schedules', ['id' => $schedule->id]);
        $this->assertSame($healthCenter->id, $schedule->fresh()->healthCenter->id);
        $this->assertSame($service->id, $schedule->fresh()->service->id);

        $this->get(route('register'))
            ->assertOk()
            ->assertDontSee('Napindan Health Center');
    }
}
