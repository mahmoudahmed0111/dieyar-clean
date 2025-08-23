<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. إصلاح جدول deep_cleanings - إضافة cleaning_type
        if (!Schema::hasColumn('deep_cleanings', 'cleaning_type')) {
            Schema::table('deep_cleanings', function (Blueprint $table) {
                $table->string('cleaning_type')->default('deep_cleaning')->after('chalet_id');
            });
        }

        // 2. إصلاح جدول regular_cleanings - إضافة status و cleaning_type
        if (!Schema::hasColumn('regular_cleanings', 'status')) {
            Schema::table('regular_cleanings', function (Blueprint $table) {
                $table->string('status')->default('pending')->after('chalet_id');
            });
        }

        if (!Schema::hasColumn('regular_cleanings', 'cleaning_type')) {
            Schema::table('regular_cleanings', function (Blueprint $table) {
                $table->string('cleaning_type')->default('regular_cleaning')->after('status');
            });
        }

        // 3. إصلاح جدول pest_controls - تغيير status من enum إلى string
        Schema::table('pest_controls', function (Blueprint $table) {
            // حذف العمود القديم
            $table->dropColumn('status');
        });

        Schema::table('pest_controls', function (Blueprint $table) {
            // إضافة العمود الجديد
            $table->string('status')->default('in_progress')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. حذف cleaning_type من deep_cleanings
        if (Schema::hasColumn('deep_cleanings', 'cleaning_type')) {
            Schema::table('deep_cleanings', function (Blueprint $table) {
                $table->dropColumn('cleaning_type');
            });
        }

        // 2. حذف status و cleaning_type من regular_cleanings
        if (Schema::hasColumn('regular_cleanings', 'status')) {
            Schema::table('regular_cleanings', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('regular_cleanings', 'cleaning_type')) {
            Schema::table('regular_cleanings', function (Blueprint $table) {
                $table->dropColumn('cleaning_type');
            });
        }

        // 3. إعادة status في pest_controls إلى enum
        Schema::table('pest_controls', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('pest_controls', function (Blueprint $table) {
            $table->enum('status', ['pending', 'done'])->default('pending')->after('description');
        });
    }
};

