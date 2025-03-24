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
        Schema::create('vessels_report_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // اسم الحقل
            $table->string('type');  // نوع الحقل (رقمي، نصي، ...)
            $table->string('placeholder')->nullable(); // نص placeholder
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vessels_report_fields');
    }
};
