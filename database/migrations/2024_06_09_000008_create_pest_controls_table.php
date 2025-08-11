<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pest_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chalet_id')->nullable()->constrained('chalets')->onDelete('set null');
            $table->foreignId('cleaner_id')->nullable()->constrained('cleaners')->onDelete('set null');
            $table->date('date');
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->enum('status', ['pending', 'done'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pest_controls');
    }
};
