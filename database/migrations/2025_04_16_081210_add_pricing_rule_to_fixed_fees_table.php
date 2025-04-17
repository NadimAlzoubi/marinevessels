<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('fixed_fees', function (Blueprint $table) {
            $table->string('pricing_rule')->default('fixed')->after('amount'); // قيم مثل: fixed, loa, gt, time, quantity, frequency, percentage
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
