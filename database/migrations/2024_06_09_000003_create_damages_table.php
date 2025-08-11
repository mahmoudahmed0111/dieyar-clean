<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaner_id')->nullable()->constrained('cleaners')->onDelete('set null');
            $table->foreignId('chalet_id')->nullable()->constrained('chalets')->onDelete('set null');
            $table->string('description');
            $table->string('price');
            $table->timestamp('reported_at')->nullable();
            $table->enum('status', ['pending', 'fixed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damages');
    }
};
