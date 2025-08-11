<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regular_cleaning_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regular_cleaning_id')->constrained('regular_cleanings')->onDelete('cascade');
            $table->foreignId('inventory_id')->constrained('inventory')->onDelete('cascade');
            $table->string('quantity')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regular_cleaning_inventory');
    }
};
