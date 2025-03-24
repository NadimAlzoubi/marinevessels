<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type');
            $table->foreignId('vessel_id')->constrained('vessels')->onDelete('cascade')->onUpdate('cascade');;
            $table->string('port');
            $table->string('berth_no');
            $table->string('vessel');
            $table->string('voy');
            $table->decimal('grt', 12, 2);
            $table->decimal('nrt', 12, 2);
            $table->decimal('dwt', 12, 2);
            $table->string('next_port_of_call');
            $table->string('next_port_of_call_eta');
            $table->text('any_requirements');
            $table->timestamps();

            // إضافة الفهارس
            $table->index('vessel_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
