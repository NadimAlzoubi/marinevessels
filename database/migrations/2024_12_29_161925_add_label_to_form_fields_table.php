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
        Schema::table('vessels_report_fields', function (Blueprint $table) {
            $table->string('label')->after('id'); // إضافة عمود label بعد عمود id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vessels_report_fields', function (Blueprint $table) {
            $table->dropColumn('label'); // حذف العمود إذا تم التراجع
        });
    }
};
