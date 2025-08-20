<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaner_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->string('type'); // regular_cleaning, deep_cleaning, maintenance, pest_control, damage
            $table->json('data')->nullable(); // بيانات إضافية
            $table->string('fcm_token')->nullable(); // رمز FCM للجهاز
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['cleaner_id', 'read_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

