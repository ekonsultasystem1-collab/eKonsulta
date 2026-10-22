<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminActivityLogsPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logs_no_longer_store_or_display_ip_addresses(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        ActivityLog::record($admin->id, 'Test action', 'Test description');

        $this->assertFalse(Schema::hasColumn('activity_logs', 'ip_address'));

        $this->actingAs($admin)
            ->get(route('admin.activity-logs.index'))
            ->assertOk()
            ->assertDontSee('IP Address')
            ->assertSee('Test description');
    }
}
