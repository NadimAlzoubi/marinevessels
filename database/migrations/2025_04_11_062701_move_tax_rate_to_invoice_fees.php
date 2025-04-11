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
        Schema::table('invoice_fees', function (Blueprint $table) {
            $table->decimal('amount', 15, 3)->after('quantity')->default(0);
            $table->decimal('tax_rate', 5, 3)->after('amount')->default(0);
            $table->decimal('discount', 15, 3)->change();
        });

        Schema::table('fixed_fees', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('fixed_fees', function (Blueprint $table) {
            $table->decimal('tax_rate', 5, 3)->after('amount')->default(0);
        });

        Schema::table('invoice_fees', function (Blueprint $table) {
            $table->dropColumn('tax_rate');
            $table->dropColumn('amount');
        });
    }
};
