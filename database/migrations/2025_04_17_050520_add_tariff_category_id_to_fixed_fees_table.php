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
        Schema::table('fixed_fees', function (Blueprint $table) {
            $table->foreign('tariff_category_id')->references('id')->on('tariff_categories')->nullOnDelete();
        });                
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fixed_fees', function (Blueprint $table) {
            //
        });
    }
};
