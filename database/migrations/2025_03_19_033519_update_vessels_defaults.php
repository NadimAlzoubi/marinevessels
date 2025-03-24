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
        Schema::table('vessels', function (Blueprint $table) {
            $table->string('job_no')->nullable()->change();
            $table->string('port_name')->nullable()->change();
            $table->dateTime('eta')->nullable()->change();
            $table->dateTime('etd')->nullable()->change();
            $table->string('status')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->string('job_no')->nullable(false)->change();
            $table->string('port_name')->nullable(false)->change();
            $table->dateTime('eta')->nullable(false)->change();
            $table->dateTime('etd')->nullable(false)->change();
            $table->string('status')->nullable(false)->change();
        });
    }
};
