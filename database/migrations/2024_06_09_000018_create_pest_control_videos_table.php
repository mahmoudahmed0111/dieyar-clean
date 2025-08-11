<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pest_control_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pest_control_id')->constrained('pest_controls')->onDelete('cascade');
            $table->enum('type', ['before', 'after']);
            $table->string('video');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pest_control_videos');
    }
};
