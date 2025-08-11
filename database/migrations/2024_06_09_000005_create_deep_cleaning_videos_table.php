<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deep_cleaning_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deep_cleaning_id')->constrained('deep_cleanings')->onDelete('cascade');
            $table->enum('type', ['before', 'after']);
            $table->string('video');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deep_cleaning_videos');
    }
};
