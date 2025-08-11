<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chalet_id')->nullable()->constrained('chalets')->onDelete('set null');
            $table->foreignId('cleaner_id')->nullable()->constrained('cleaners')->onDelete('set null');
            $table->string('description');
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending');
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance');
    }
};
