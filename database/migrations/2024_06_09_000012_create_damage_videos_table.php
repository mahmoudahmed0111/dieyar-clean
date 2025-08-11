<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damage_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_id')->constrained('damages')->onDelete('cascade');
            $table->string('video');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damage_videos');
    }
};
