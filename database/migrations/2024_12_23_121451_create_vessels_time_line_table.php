<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselsTimeLineTable extends Migration
{
    public function up()
    {
        Schema::create('vessels_time_line', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_time_line_id')->constrained('reports')->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('vessel_time_line_type_id')->constrained('vessels_time_line_types')->onDelete('cascade')->onUpdate('cascade');;
            $table->dateTime('date');
            $table->timestamps();

            // إضافة الفهارس
            $table->index('vessel_time_line_id');
            $table->index('vessel_time_line_type_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessels_time_line');
    }
}
