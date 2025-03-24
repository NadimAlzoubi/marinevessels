<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselsTable extends Migration
{
    public function up()
    {
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('job_no')->unique();
            $table->string('vessel_number');
            $table->string('port_name');
            $table->dateTime('eta');
            $table->dateTime('etd');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessels');
    }
}
