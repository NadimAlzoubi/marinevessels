<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselsBunkersRobTypesTable extends Migration
{
    public function up()
    {
        Schema::create('vessels_bunkers_rob_types', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('unit');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessels_bunkers_rob_types');
    }
}
