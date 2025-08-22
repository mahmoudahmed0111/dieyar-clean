<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // حذف عمود status القديم
        Schema::table('pest_controls', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // إضافة عمود status جديد كـ string
        Schema::table('pest_controls', function (Blueprint $table) {
            $table->string('status')->default('in_progress')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // حذف عمود status الجديد
        Schema::table('pest_controls', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // إعادة إضافة عمود status القديم كـ enum
        Schema::table('pest_controls', function (Blueprint $table) {
            $table->enum('status', ['pending', 'done'])->default('pending')->after('description');
        });
    }
};
