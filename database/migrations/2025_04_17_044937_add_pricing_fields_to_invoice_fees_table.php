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
        Schema::table('invoice_fees', function (Blueprint $table) {
            $table->string('pricing_method')->nullable()->after('amount');
            $table->json('pricing_context')->nullable()->after('pricing_method');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_fees', function (Blueprint $table) {
            //
        });
    }
};
