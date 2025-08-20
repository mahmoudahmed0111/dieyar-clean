<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cleaner;
use App\Models\Notification;
use App\Models\Chalet;
use App\Models\RegularCleaning;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class NotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $cleaner;

    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء عامل نظافة للاختبار
        $this->cleaner = Cleaner::factory()->create([
            'fcm_token' => 'test_fcm_token_123'
        ]);
    }

    /** @test */
    public function cleaner_can_get_notifications()
    {
        // إنشاء إشعارات للاختبار
        Notification::factory()->count(5)->create([
            'cleaner_id' => $this->cleaner->id
        ]);

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'notifications',
                    'pagination'
                ],
                'message'
            ]);
    }

    /** @test */
    public function cleaner_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'cleaner_id' => $this->cleaner->id,
            'read_at' => null
        ]);

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->putJson("/api/notifications/{$notification->id}/read");

        $response->assertStatus(200);

        $this->assertNotNull($notification->fresh()->read_at);
    }

    /** @test */
    public function cleaner_can_update_fcm_token()
    {
        $newToken = 'new_fcm_token_456';

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->postJson('/api/notifications/fcm-token', [
                'fcm_token' => $newToken
            ]);

        $response->assertStatus(200);

        $this->assertEquals($newToken, $this->cleaner->fresh()->fcm_token);
    }

    /** @test */
    public function cleaner_can_get_notification_stats()
    {
        // إنشاء إشعارات مقروءة وغير مقروءة
        Notification::factory()->count(3)->create([
            'cleaner_id' => $this->cleaner->id,
            'read_at' => now()
        ]);

        Notification::factory()->count(2)->create([
            'cleaner_id' => $this->cleaner->id,
            'read_at' => null
        ]);

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->getJson('/api/notifications/stats');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'total' => 5,
                    'read' => 3,
                    'unread' => 2
                ]
            ]);
    }

    /** @test */
    public function notification_is_sent_when_regular_cleaning_is_created()
    {
        $chalet = Chalet::factory()->create();

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->postJson('/api/regular-cleanings', [
                'chalet_id' => $chalet->id,
                'cleaning_date' => now()->format('Y-m-d'),
                'notes' => 'Test cleaning'
            ]);

        $response->assertStatus(201);

        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'cleaner_id' => $this->cleaner->id,
            'type' => 'regular_cleaning'
        ]);
    }

    /** @test */
    public function notification_is_sent_when_deep_cleaning_is_created()
    {
        $chalet = Chalet::factory()->create();

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->postJson('/api/deep-cleanings', [
                'chalet_id' => $chalet->id,
                'cleaning_date' => now()->format('Y-m-d'),
                'notes' => 'Test deep cleaning'
            ]);

        $response->assertStatus(201);

        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'cleaner_id' => $this->cleaner->id,
            'type' => 'deep_cleaning'
        ]);
    }

    /** @test */
    public function notification_is_sent_when_maintenance_is_created()
    {
        $chalet = Chalet::factory()->create();

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->postJson('/api/maintenance', [
                'chalet_id' => $chalet->id,
                'maintenance_date' => now()->format('Y-m-d'),
                'description' => 'Test maintenance',
                'status' => 'pending'
            ]);

        $response->assertStatus(201);

        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'cleaner_id' => $this->cleaner->id,
            'type' => 'maintenance'
        ]);
    }

    /** @test */
    public function notification_is_sent_when_pest_control_is_created()
    {
        $chalet = Chalet::factory()->create();

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->postJson('/api/pest-controls', [
                'chalet_id' => $chalet->id,
                'treatment_date' => now()->format('Y-m-d'),
                'notes' => 'Test pest control'
            ]);

        $response->assertStatus(201);

        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'cleaner_id' => $this->cleaner->id,
            'type' => 'pest_control'
        ]);
    }

    /** @test */
    public function notification_is_sent_when_damage_is_created()
    {
        $chalet = Chalet::factory()->create();

        $response = $this->actingAs($this->cleaner, 'sanctum')
            ->postJson('/api/damages', [
                'chalet_id' => $chalet->id,
                'damage_date' => now()->format('Y-m-d'),
                'description' => 'Test damage',
                'severity' => 'minor'
            ]);

        $response->assertStatus(201);

        // التحقق من إنشاء إشعار
        $this->assertDatabaseHas('notifications', [
            'cleaner_id' => $this->cleaner->id,
            'type' => 'damage'
        ]);
    }
}

