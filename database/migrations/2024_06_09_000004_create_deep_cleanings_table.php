<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deep_cleanings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cleaner_id')->nullable()->constrained('cleaners')->onDelete('set null');
            $table->foreignId('chalet_id')->nullable()->constrained('chalets')->onDelete('set null');
            $table->date('date');
            $table->string('price')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deep_cleanings');
    }
};
