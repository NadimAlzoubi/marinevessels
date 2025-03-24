<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselsBunkersRobTable extends Migration
{
    public function up()
    {
        Schema::create('vessels_bunkers_rob', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_report_id')->constrained('reports')->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('vessel_bunkers_rob_type_id')->constrained('vessels_bunkers_rob_types')->onDelete('cascade')->onUpdate('cascade');;
            $table->timestamps();

            // إضافة الفهارس
            $table->index('vessel_report_id');
            $table->index('vessel_bunkers_rob_type_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessels_bunkers_rob');
    }
}
