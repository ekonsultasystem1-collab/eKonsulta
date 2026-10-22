<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementEventDatesTest extends TestCase
{
    use RefreshDatabase;

    public function test_expired_and_future_event_announcements_are_hidden_from_patients(): void
    {
        $patient = $this->createUser('patient@example.test', 'patient');
        $expired = $this->createAnnouncement('Expired event', today()->subDays(2), today()->subDay());
        $active = $this->createAnnouncement('Current event', today(), today());
        $future = $this->createAnnouncement('Future event', today()->addDay(), today()->addDays(2));

        Notification::create([
            'user_id' => $patient->id,
            'announcement_id' => $expired->id,
            'title' => $expired->title,
            'message' => 'Expired event notification',
            'type' => 'announcement',
        ]);

        $this->actingAs($patient)
            ->get(route('patient.announcements.index'))
            ->assertOk()
            ->assertSee('Current event')
            ->assertDontSee('Expired event')
            ->assertDontSee('Future event');

        $this->actingAs($patient)
            ->get(route('patient.announcements.show', $expired))
            ->assertNotFound();

        $this->actingAs($patient)
            ->get(route('patient.notifications'))
            ->assertOk()
            ->assertDontSee('Expired event notification')
            ->assertDontSee('Expired event');
    }

    public function test_admin_can_create_an_announcement_with_an_event_date_range(): void
    {
        $admin = $this->createUser('admin@example.test', 'admin');

        $this->actingAs($admin)
            ->post(route('admin.announcements.store'), [
                'title' => 'Community health fair',
                'content' => 'Visit the health fair.',
                'event_start_date' => today()->addDays(2)->toDateString(),
                'event_end_date' => today()->addDays(4)->toDateString(),
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.announcements.index'));

        $announcement = Announcement::where('title', 'Community health fair')->firstOrFail();
        $this->assertSame(today()->addDays(2)->toDateString(), $announcement->event_start_date->toDateString());
        $this->assertSame(today()->addDays(4)->toDateString(), $announcement->event_end_date->toDateString());
    }

    public function test_event_end_date_cannot_be_before_its_start_date(): void
    {
        $admin = $this->createUser('admin@example.test', 'admin');

        $this->actingAs($admin)
            ->from(route('admin.announcements.create'))
            ->post(route('admin.announcements.store'), [
                'title' => 'Invalid event',
                'content' => 'Invalid event dates.',
                'event_start_date' => today()->addDays(3)->toDateString(),
                'event_end_date' => today()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('event_end_date');
    }

    private function createAnnouncement(string $title, $startDate, $endDate): Announcement
    {
        $creator = User::firstOrCreate(
            ['email' => 'announcement-creator@example.test'],
            [
                'name' => 'Announcement Creator',
                'password' => 'password',
                'role' => 'admin',
            ]
        );

        return Announcement::create([
            'title' => $title,
            'content' => $title . ' description',
            'created_by' => $creator->id,
            'event_start_date' => $startDate,
            'event_end_date' => $endDate,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    private function createUser(string $email, string $role): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
