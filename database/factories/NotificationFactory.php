<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\Cleaner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['regular_cleaning', 'deep_cleaning', 'maintenance', 'pest_control', 'damage', 'general'];

        return [
            'cleaner_id' => Cleaner::factory(),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement($types),
            'data' => [
                'chalet_id' => $this->faker->numberBetween(1, 100),
                'chalet_name' => $this->faker->company(),
                'timestamp' => now()->toISOString(),
            ],
            'fcm_token' => $this->faker->uuid(),
            'read_at' => $this->faker->optional()->dateTime(),
            'sent_at' => $this->faker->optional()->dateTime(),
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => now(),
        ]);
    }

    /**
     * Indicate that the notification is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent_at' => now(),
        ]);
    }

    /**
     * Create a regular cleaning notification.
     */
    public function regularCleaning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'regular_cleaning',
            'title' => 'تنظيف منتظم جديد',
            'body' => 'تم إنشاء مهمة تنظيف منتظم جديدة',
        ]);
    }

    /**
     * Create a deep cleaning notification.
     */
    public function deepCleaning(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'deep_cleaning',
            'title' => 'تنظيف عميق جديد',
            'body' => 'تم إنشاء مهمة تنظيف عميق جديدة',
        ]);
    }

    /**
     * Create a maintenance notification.
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'maintenance',
            'title' => 'مهمة صيانة جديدة',
            'body' => 'تم إنشاء مهمة صيانة جديدة',
        ]);
    }

    /**
     * Create a pest control notification.
     */
    public function pestControl(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pest_control',
            'title' => 'مهمة مكافحة آفات جديدة',
            'body' => 'تم إنشاء مهمة مكافحة آفات جديدة',
        ]);
    }

    /**
     * Create a damage notification.
     */
    public function damage(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'damage',
            'title' => 'بلاغ أضرار جديد',
            'body' => 'تم تسجيل بلاغ أضرار جديد',
        ]);
    }
}

