<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chalets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->enum('type', ['apartment', 'studio', 'villa'])->nullable(); // apartment, studio, villa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chalets');
    }
};
