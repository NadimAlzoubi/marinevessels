<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselServicesTable extends Migration
{
    public function up()
    {
        Schema::create('vessel_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('vessel_service_type_id')->constrained('vessels_services_types')->onDelete('cascade')->onUpdate('cascade');;
            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->timestamps();

            // إضافة الفهارس
            $table->index('vessel_id');
            $table->index('vessel_service_type_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessel_services');
    }
}
