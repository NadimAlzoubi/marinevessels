<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVesselsServicesTypesTable extends Migration
{
    public function up()
    {
        Schema::create('vessels_services_types', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->decimal('basic_price', 12, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vessels_services_types');
    }
}
